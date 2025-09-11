<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Plugin\Catalog\Model\Category;

/**
 * Class DataProvider
 * @package MageWorkshop\DetailedReview\Model\Plugin\Catalog\Category
 */
class DataProvider
{
    /** @var \Magento\Eav\Model\Config $eavConfig */
    protected $eavConfig;

    /**
     * DataProvider constructor.
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(\Magento\Eav\Model\Config $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @IGNORE - this method was introduced in Magento 2.1 and is not called in the previous versions
     *
     * @param \Magento\Catalog\Model\Category\DataProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPrepareMeta(\Magento\Catalog\Model\Category\DataProvider $subject, $result)
    {
        if (!isset($result['display_settings']['children']['review_form']['arguments']['data']['config'])) {
            $metadata = $subject->getAttributesMeta($this->eavConfig->getEntityType('catalog_category'));
            if (isset($metadata['review_form'])) {
                $metadata['review_form']['sortOrder'] = -1;
                $result['display_settings']['children']['review_form']['arguments']['data']['config']
                    = $metadata['review_form'];
            }
        }

        return $result;
    }
}
