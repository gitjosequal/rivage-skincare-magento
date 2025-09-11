<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Setup;

interface ColumnConfigInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getLength();

    /**
     * @param int|string $length
     * @return $this
     */
    public function setLength($length);

    /**
     * @return string
     */
    public function getOptions();

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options);

    /**
     * @return string
     */
    public function getComment();

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment($comment);

    /**
     * @return null|ForeignKeyConfigInterface
     */
    public function getForeignKeyConfig();

    /**
     * @param ForeignKeyConfigInterface $foreignKeyConfig
     * @return $this
     */
    public function setForeignKeyConfig(ForeignKeyConfigInterface $foreignKeyConfig);
}
