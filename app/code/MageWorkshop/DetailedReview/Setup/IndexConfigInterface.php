<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Setup;

interface IndexConfigInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $fkName
     * @return $this
     */
    public function setName($fkName);

    /**
     * @return string|array
     */
    public function getColumn();

    /**
     * @param string|array $column
     * @return $this
     */
    public function setColumn($column);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options);
}
