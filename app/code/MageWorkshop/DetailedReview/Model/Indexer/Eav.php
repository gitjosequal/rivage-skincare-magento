<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\Indexer;

use MageWorkshop\DetailedReview\Model\Details;

/**
 * Class AbstractEavIndexer
 * Refresh flat table structure. There is no need to care about attributes because unchanged attribute columns
 * won't be modified by the TableBuilder
 *
 * @package MageWorkshop\DetailedReview\Model\Indexer
 */
class Eav extends \MageWorkshop\DetailedReview\Model\Indexer\AbstractIndexer
{
    protected $entityCode = Details::ENTITY;
}
