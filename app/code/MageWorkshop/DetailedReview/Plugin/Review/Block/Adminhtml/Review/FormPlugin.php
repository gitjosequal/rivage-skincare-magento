<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Plugin\Review\Block\Adminhtml\Review;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Exception\LocalizedException;
use MageWorkshop\DetailedReview\Block\Adminhtml\Review\AdditionalFieldsContainer;

/**
 * Class FormPlugin
 * This class injects additional fields into the review form
 * Be patient while editing this class because there are 4 different cases with different logic:
 * 1) enter Edit Review pare - review data is available and is initialized inside Magento
 * 2) enter New Review - form is rendered prior to selecting the product. That is why there is no info about the product
 *    and target stores at that moment. So, we just need to insert container and wait till all data becomes available
 * 3) Edit Review, Visibility (stores) are changed - AJAX call to our own controller is processed. We know the review id
 *    and product id is taken from it, so need to get fields list for all stores and process them one by one
 * 4) New Review, Visibility (stores) are changed - the same as above, but we do not have the review data, so product id
 *    is passed as a request argument - it is present in the hidden input on the page, but only in this case
 */
class FormPlugin
{
    const SELECT_STORES = 'select_stores';

    const PRODUCT_ID = 'product_id';

    const EVENT_MAGEWORKSHOP_DETAILEDREVIEW_COLLECT_UNKNOWN_INPUT_TYPE_CONFIGURATIONS
        = 'mageworkshop_detailedreview_collect_unknown_input_type_configurations';

    /**
     * @var \Magento\Framework\App\Request\Http $request
     */
    private $request;

    /** @var
     * \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     */
    private $reviewHelper;

    /** @var
     * \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     */
    private $attributeHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Framework\View\LayoutInterface $layout
     */
    private $layout;

    /**
     * @var \Magento\Framework\Registry $registry
     */
    private $registry;

    /**
     * FormPlugin constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->request = $request;
        $this->reviewHelper = $reviewHelper;
        $this->attributeHelper = $attributeHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->layout = $layout;
        $this->registry = $registry;
        $this->eventManager = $eventManager;
    }

    /**
     * @var \Magento\Framework\Event\ManagerInterface $eventManager
     */
    private $eventManager;

    /**
     * @param Generic $subject
     * @throws \Exception
     */
    public function afterSetForm(Generic $subject/*, $result */)
    {
        $form = $subject->getForm();
        $form->setData('enctype', 'multipart/form-data');
        // Depends on the mode - creating new review or creating an existing one. See form id in:
        // \Magento\Review\Block\Adminhtml\Add\Form at line #62
        // \Magento\Review\Block\Adminhtml\Edit\Form at line #65
        $targetFieldSetName = ($review = $this->getReview())
            ? 'review_details'
            : 'add_review_form';

        // Note! There is no review data if new review is being created and the product has not yet been selected
        if ($review !== null && !$this->reviewHelper->isProductReview($review)) {
            return;
        }

        $targetFieldSet = false;
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $element */
        foreach ($form->getElements() as $element) {
            if ($element->getId() === $targetFieldSetName) {
                $targetFieldSet = $element;
                break;
            }
        }

        if ($targetFieldSet) {
            // Need to add delimiter for additional review fields because they are loaded with AJAX request
            // and depend on the product store(s) selected. Also, stores list can be changed while editing review
            // We show/hide only the fields that appear after this delimiter.
            // NOTE! It will be a problem if somebody also adds additional fields after ours
            // ( Will look into this if this ever happens )
            if (!$this->request->isAjax()) {
                $fieldsContainer = $targetFieldSet->addField(
                    'additional_fields_delimiter',
                    'text',
                    [
                        'name' => '',
                        'title' => '',
                        'label' => '',
                    ]
                );
                /** @var AdditionalFieldsContainer $renderer */
                $renderer = $this->layout->createBlock(
                    \MageWorkshop\DetailedReview\Block\Adminhtml\Review\AdditionalFieldsContainer::class,
                    'additional_fields_delimiter'
                );
                $fieldsContainer->setRenderer($renderer);
            }
            // This happens while creating new review from the Admin Panel before the product is selected
            if (!$product = $this->getProduct()) {
                return;
            }

            $requiredAttributes = $this->attributeHelper->getRequiredAttributes();
            // better to use prepared JSON config here because it contains a lot of necessary data
            $additionalFormAttributes = $this->attributeHelper->getReviewFormAttributesConfiguration(
                $this->getProduct(),
                $this->getStoreIds()
            );

            /** @var \MageWorkshop\DetailedReview\Model\Attribute $attribute */
            foreach ($additionalFormAttributes as $attribute) {
                $attributeCode = $attribute['attribute_code'];
                $attributeLabel = $attribute['frontend_label'];

                if (in_array($attributeCode, $requiredAttributes, false)) {
                    continue;
                }

                $inputType = '';
                $options = [];
                $size = 0;
                $style = 'min-width:200px;';
                $isRequired = (bool)$attribute['is_required'];
                $validation = '';
                $config = [];

                switch ($attribute['frontend_input']) {
                    case 'boolean':
                        $inputType = 'select';
                        if (!$isRequired) {
                            $options = ['0' => '-- Please Select --'];
                        }
                        $options = array_merge($options, [
                            '1' => __('No'),
                            '2' => __('Yes')
                        ]);
                        break;
                    case 'select':
                        $inputType = $attribute['frontend_input'];
                        foreach ($attribute['options'] as $option) {
                            if (!$option['value']) {
                                if ($isRequired) {
                                    continue;
                                }
                                $option['label'] = '-- Please Select --';
                            }
                            $options[$option['value']] = $option['label'];
                        }
                        break;
                    case 'swatch':
                        $inputType = 'select';
                        $options = $attribute['options'];

                        if (!$isRequired) {
                            $options = array_merge(
                                [[
                                    'value' => '0',
                                    'label' => '-- Please Select --'
                                ]],
                                $options
                            );
                        }

                        break;
                    case 'multiselect':
                        $inputType = $attribute['frontend_input'];
                        /** @var array $options */
                        $options = $attribute['options'];

                        foreach ($options as $index => &$option) {
                            if (!$option['value']) {
                                if ($isRequired) {
                                    unset($options[$index]);
                                } else {
                                    $option['label'] = '-- Please Select --';
                                }
                            }
                        }
                        unset($option);
                        $size = count($options) > 10 ? 10 : count($options) + 2;
                        break;
                    case 'textarea':
                        $style .= 'height:16em;';
                        $inputType = $attribute['frontend_input'];
                        $validation = $attribute['validation_class'];
                        break;
                    case 'text':
                        $inputType = $attribute['frontend_input'];
                        $validation = $attribute['validation_class'];
                        break;
                    default:
                        $transportObject = new \Magento\Framework\DataObject([
                            'inputType'  => $inputType,
                            'attribute'  => $attribute,
                            'review'     => $review,
                            'validation' => $validation,
                            'config'     => $config
                        ]);
                        $this->eventManager->dispatch(
                            self::EVENT_MAGEWORKSHOP_DETAILEDREVIEW_COLLECT_UNKNOWN_INPUT_TYPE_CONFIGURATIONS,
                            ['transportObject' => $transportObject]
                        );

                        $inputType = $transportObject->getData('inputType');
                        $validation = $transportObject->getData('validation');
                        $config = $transportObject->getData('config');
                        break;
                }

                if ($inputType) {
                    $config = array_merge([
                        'name' => $attributeCode,
                        'title' => __($attributeLabel),
                        'label' => __($attributeLabel),
                        'required' => $isRequired,
                        'values' => $options,
                        'style' => $style,
                        'class' => $validation,
                    ], $config);

                    $field = $targetFieldSet->addField(
                        $attributeCode,
                        $inputType,
                        $config
                    );

                    if ($size) {
                        $field->setSize($size);
                    }
                }
            }

            $values = isset($review)
                ? $review->getData()
                : [];
            $form->setValues($values);
        }
    }

    /**
     * Note! There may be no review data if new review is being created and the product has not yet been selected
     *
     * @return \Magento\Review\Model\Review|null
     */
    private function getReview()
    {
        return $this->registry->registry('review_data');
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    private function getStoreIds()
    {
        if (!$storeIds = $this->registry->registry(self::SELECT_STORES)) {
            if ($review = $this->getReview()) {
                $storeIds = [$review->getStoreId()];
            } else {
                $storeIds = [];
            }
        }
        return $storeIds;
    }

    /**
     * @return \Magento\Catalog\Model\Product|null
     * @throws \Exception
     */
    private function getProduct()
    {
        // Product ID is passed from \MageWorkshop\DetailedReview\Controller\Adminhtml\Review\AdditionalFields
        // while reloading the review fields, but does not exist before the product is selected
        $productId = (int) $this->registry->registry(self::PRODUCT_ID);

        if (!$productId && $review = $this->getReview()) {
            if (!$this->reviewHelper->isProductReview($review)) {
                throw new LocalizedException(__('Review with ID %1 is not a product review!', $productId));
            }
            $productId = (int) $review->getEntityPkValue();
        }

        if ($productId) {
            $productCollection = $this->productCollectionFactory->create();
            // No need to filter products collection by store because actually product can be present in multiple stores
            // $productCollection->setStoreId($review->getStoreId());
            $productCollection->addIdFilter($productId);
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $productCollection->getFirstItem();
            if (!$product->getId()) {
                throw new LocalizedException(__('Product with ID %1 was not found!', $productId));
            }
            return $product;
        }
        return null;
    }
}
