<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Plugin\Review;

use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\Review;

abstract class AbstractReview
{
    const VALIDATE_CLASS_STRING_LENGTH = 'StringLength';
    const VALIDATE_CLASS_HOSTNAME = 'Uri';
    const VALIDATE_CLASS_DIGITS = 'Float';
    const VALIDATE_CLASS_REGEX = 'Regex';

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     */
    protected $attributeHelper;

    /**
     * @var \MageWorkshop\Core\Helper\Data $dataHelper
     */
    protected $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry $registry
     */
    protected $registry;

    /** @var \Magento\Framework\App\Request\Http $request */
    protected $request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface $messageManager
     */
    protected $messageManager;

    /**
     * @var \Magento\Review\Model\ReviewFactory $reviewFactory
     */
    protected $reviewFactory;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig */
    protected $scopeConfig;

    /** @var \MageWorkshop\Core\Helper\Serializer */
    private $serializer;

    /**
     * AbstractReview constructor.
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     * @param \MageWorkshop\Core\Helper\Data $dataHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \MageWorkshop\Core\Helper\Serializer $serializer
     */
    public function __construct(
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper,
        \MageWorkshop\Core\Helper\Data $dataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \MageWorkshop\Core\Helper\Serializer $serializer
    ) {
        $this->attributeHelper = $attributeHelper;
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->serializer = $serializer;
    }

    /**
     * @param Review $review
     * @param array $errors
     * @return array|bool
     * @throws LocalizedException
     */
    protected function checkValidation(Review $review, $errors = [])
    {
        $additionalFormAttributes = $this->attributeHelper->getReviewFormAttributesConfigurationByReview($review);
        $data = $this->request->getPostValue();
        $standardAttributes = ['nickname', 'title', 'detail' ];
        $validationErrors = [];

        foreach ($additionalFormAttributes as $attribute) {
            //Don't check the fields that have already been tested
            if (in_array($attribute['attribute_code'], $standardAttributes) || $attribute['is_required'] === "0") {
                continue;
            }

            $rules = $this->serializer->unserialize($attribute['validate_rules']);
            if (is_array($rules) && !empty($rules)) {
                foreach ($rules as $rule) {
                    if (isset($rule['type']) && isset($data[$attribute['attribute_code']])) {
                        $error = $this->checkAttributeOnValidationRules(
                            $rule['type'],
                            $rule['value'],
                            $data[$attribute['attribute_code']],
                            $attribute['frontend_label']
                        );

                        if (!empty($error)) {
                            array_push($validationErrors, $error);
                        }
                    }
                }
            }
        }

        if ($errors === true) {
            if (empty($validationErrors)) {
                return $errors;
            }
            $errors = [];
        }

        $errors = array_merge((array) $errors, $validationErrors);

        return $errors;
    }

    /**
     * @param $ruleType
     * @param $ruleValue
     * @param $attributeValue
     * @param $title
     * @return \Magento\Framework\Phrase|string
     * @throws LocalizedException
     */
    protected function checkAttributeOnValidationRules($ruleType, $ruleValue, $attributeValue, $title)
    {
        $arg = [];
        $error = '';
        if (strpos($ruleType, 'length') !== false) {
            $arg['class'] = self::VALIDATE_CLASS_STRING_LENGTH;

            if (strpos($ruleType, 'min') !== false) {
                $arg['type'] = 'min';
                $arg['param'] = 'more';
            } else {
                $arg['type'] = 'max';
                $arg['param'] = 'less';
            }

            if (!$this->applyValidation($attributeValue, $arg['class'], $arg['type'], $ruleValue)) {
                $error = sprintf(
                    '%s of %s must be equal or %s than %d symbols.',
                    $this->changeRuleType($ruleType),
                    $title,
                    $arg['param'],
                    $ruleValue
                );
            }
        } elseif (strpos($ruleType, 'url') !== false) {
            $arg['class'] = self::VALIDATE_CLASS_REGEX;
            $arg['type'] = '';
            $ruleValue = '/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i';
            $arg['type'] = 'pattern';
            if (!$this->applyValidation($attributeValue, $arg['class'], $arg['type'], $ruleValue)) {
                $error = __('Please enter a valid ' . $this->changeRuleType($ruleType));
            }
        } else {
            if (strpos($ruleType, 'number') !== false) {
                $arg['class'] = self::VALIDATE_CLASS_DIGITS;
            } else {
                throw new LocalizedException(__('Error, no such validation class'));
            }

            if (!$this->applyValidation($attributeValue, $arg['class'])) {
                $error = __('Please enter a valid ' . $this->changeRuleType($ruleType));
            }
        }

        return $error;
    }

    /**
     * @param $str
     * @return mixed
     */
    protected function changeRuleType($str)
    {
        return preg_replace('/[0-9]+/', '', (ucfirst(str_replace('-', ' ', $str))));
    }

    /**
     * @param $attributeValue
     * @param $class
     * @param string $type
     * @param string $ruleValue
     * @return bool
     */
    protected function applyValidation($attributeValue, $class, $type = '', $ruleValue = '')
    {
        $additionalArgs = empty($type)
            ? []
            : [$type => $ruleValue];
        return \Zend_Validate::is(
            $attributeValue,
            $class,
            $additionalArgs
        );
    }
}
