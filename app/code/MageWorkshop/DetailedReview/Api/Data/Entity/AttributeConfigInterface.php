<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Api\Data\Entity;

/**
 * Interface AttributeConfigInterface
 * This interface defines getters for attribute configuration container that is used to set up and create attributes.
 * These values are set as the default ones for the attribute while creating it via installer or from the Admin Panel.
 * Getters are called in the way they are used in the installation scripts for Magento 2 attributes,
 * so, unfortunately, names are different from the field names in the database.
 *
 * @package MageWorkshop\DetailedReview\Api\Data\Entity
 * @api
 */
interface AttributeConfigInterface
{
    /**
     * @param array $config
     * @return $this
     */
    public function populateFromConfig(array $config);

    /**
     * @return string
     */
    public function getFrontendInput();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @return bool
     */
    public function getRequired();

    /**
     * @return bool
     */
    public function getUserDefined();

    /**
     * @return bool
     */
    public function getVisibleOnFront();

    /**
     * @return string
     */
    public function getInputType();

    /**
     * @return string
     */
    public function getOptionType();

    /**
     * @return string
     */
    public function getBackendModel();

    /**
     * @return string
     */
    public function getSourceModel();

    /**
     * @return string
     */
//    public function getFrontendModel();

    /**
     * @return string
     */
    public function getValidateRules();

    /**
     * @return array
     */
    public function getConfig();
}
