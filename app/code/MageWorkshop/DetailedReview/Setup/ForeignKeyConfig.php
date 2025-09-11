<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Setup;

class ForeignKeyConfig implements ForeignKeyConfigInterface
{
    /** @var string $fkName */
    protected $fkName;

    /** @var string $column */
    protected $column;

    /** @var string $referenceTable */
    protected $referenceTable;

    /** @var string $referenceColumn */
    protected $referenceColumn;

    /** @var string $onDelete */
    protected $onDelete;

    /**
     * {@inheritdoc}
     */
    public function getFkName()
    {
        return $this->fkName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFkName($fkName)
    {
        $this->fkName = $fkName;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumn($column)
    {
        $this->column = $column;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceTable()
    {
        return $this->referenceTable;
    }

    /**
     * {@inheritdoc}
     */
    public function setReferenceTable($referenceTable)
    {
        $this->referenceTable = $referenceTable;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceColumn()
    {
        return $this->referenceColumn;
    }

    /**
     * {@inheritdoc}
     */
    public function setReferenceColumn($referenceColumn)
    {
        $this->referenceColumn = $referenceColumn;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOnDelete()
    {
        return $this->onDelete;
    }

    /**
     * {@inheritdoc}
     */
    public function setOnDelete($onDelete)
    {
        $this->onDelete = $onDelete;
        return $this;
    }
}
