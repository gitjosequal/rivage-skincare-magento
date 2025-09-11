<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\Config;

use MageWorkshop\DetailedReview\Api\Data\Entity\AttributeConfigInterface;

abstract class AbstractAttribute extends AbstractConfig implements AttributeConfigInterface
{
    const NO_DATA_EXCEPTION = 'No attribute data provided';

    const NO_BACKEND_TYPE_EXCEPTION = 'No backend type specified for the attribute: %1';

    const INVALID_VALIDATE_RULES_EXCEPTION = 'Can\'t unserialize validate rules "%1$s" for attribute with input %2$s';

    const CLASS_NOT_FOUND_EXCEPTION = '%s class was not found';
    /**
     * @var \MageWorkshop\Core\Helper\Serializer
     */
    private $serializer;

    /**
     * AbstractAttribute constructor.
     * @param array $requiredConfigFields
     * @param array $optionalConfigFields
     * @param \MageWorkshop\Core\Helper\Serializer $serializer
     * @param array $data
     */
    public function __construct(
        array $requiredConfigFields,
        array $optionalConfigFields,
        \MageWorkshop\Core\Helper\Serializer $serializer,
        array $data = []
    ) {
        parent::__construct($requiredConfigFields, $optionalConfigFields, $data);
        $this->serializer = $serializer;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function populateFromConfig(array $config)
    {
        if (empty($config)) {
            throw new \DomainException(self::NO_DATA_EXCEPTION);
        }
        $this->addData($config);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendInput()
    {
        return $this->getData('frontend_input');
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->getData('label');
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return (int) $this->getData('position');
    }

    /**
     * {@inheritdoc}
     */
    public function getRequired()
    {
        return (bool) $this->getDataOrDefault('required', 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserDefined()
    {
        return (bool) $this->getDataOrDefault('user_defined', 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibleOnFront()
    {
        return (bool) $this->getDataOrDefault('visible_on_front', 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getInputType()
    {
        return $this->getData('input_type');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionType()
    {
        return $this->getData('option_type');
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendModel()
    {
        return $this->validateClassExists($this->getData('backend_model'));
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceModel()
    {
        return $this->validateClassExists($this->getData('source_model'));
    }

    /**
     * {@inheritdoc}
     */
//    public function getFrontendModel()
//    {
//        if (!$backendType = $this->getData('frontend_model')) {
//            throw new \DomainException(sprintf(self::NO_BACKEND_TYPE_EXCEPTION, $this->getFrontendInput()));
//        }
//    }

    /**
     * {@inheritdoc}
     * @throws \DomainException
     */
    public function getValidateRules()
    {
        $data = $this->getData('validate_rules');
        if (empty($data) || !is_array(@$this->serializer->unserialize($data))) {
            throw new \DomainException(
                sprintf(self::INVALID_VALIDATE_RULES_EXCEPTION, $data, $this->getFrontendInput())
            );
        }
        return $data;
    }

    /**
     * Check if class exists. Empty value is ok because simple attribute types may not need all this stuff
     *
     * @param string $className
     * @return mixed
     * @throws \RuntimeException
     */
    protected function validateClassExists($className)
    {
        if (!empty($className) && !class_exists($className)) {
            throw new \RuntimeException(sprintf(self::CLASS_NOT_FOUND_EXCEPTION, $className));
        }
        return $className;
    }
}
