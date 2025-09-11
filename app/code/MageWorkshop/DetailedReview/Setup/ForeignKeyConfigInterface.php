<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Setup;

interface ForeignKeyConfigInterface
{
    /**
     * @return string
     */
    public function getFkName();

    /**
     * @param string $fkName
     * @return $this
     */
    public function setFkName($fkName);

    /**
     * @return string
     */
    public function getColumn();

    /**
     * @param string $column
     * @return $this
     */
    public function setColumn($column);

    /**
     * @return string
     */
    public function getReferenceTable();

    /**
     * @param string $referenceTable
     * @return $this
     */
    public function setReferenceTable($referenceTable);

    /**
     * @return string
     */
    public function getReferenceColumn();

    /**
     * @param string $referenceColumn
     * @return $this
     */
    public function setReferenceColumn($referenceColumn);

    /**
     * @return string
     */
    public function getOnDelete();

    /**
     * @param string $onDelete
     * @return $this
     */
    public function setOnDelete($onDelete);
}
