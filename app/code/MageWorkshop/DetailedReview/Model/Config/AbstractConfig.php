<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\Config;

use Magento\Framework\DataObject;

abstract class AbstractConfig extends DataObject
{
    protected $requiredConfigFields = [];

    protected $optionalConfigFields = [];

    /**
     * AbstractConfig constructor.
     * @param array $requiredConfigFields
     * @param array $optionalConfigFields
     * @param array $data
     */
    public function __construct(
        array $requiredConfigFields,
        array $optionalConfigFields,
        array $data = []
    ) {
        parent::__construct($data);
        $this->requiredConfigFields = $requiredConfigFields;
        $this->optionalConfigFields = $optionalConfigFields;
    }

    /**
     * Get full attribute data via real getters to perform proper data validation
     *
     * @return array
     */
    public function getConfig()
    {
        $config = [];

        foreach ($this->requiredConfigFields as $field) {
            $config[$field] = $this->getDataUsingMethod($field);
        }

        foreach ($this->optionalConfigFields as $field) {
            if ($data = $this->getDataUsingMethod($field)) {
                $config[$field] = $data;
            }
        }

        return $config;
    }

    /**
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function getDataOrDefault($key, $defaultValue)
    {
        if (!$value = $this->getData($key)) {
            $value = $defaultValue;
        }
        return $value;
    }
}
