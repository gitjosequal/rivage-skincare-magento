<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\ImageLoader\Plugin\DetailedReview\Setup;

use MageWorkshop\DetailedReview\Setup\DetailsSetup;

class DetailsSetupPlugin
{
    /**
     * @param DetailsSetup $subject
     * @param array $result
     * @return array
     */
    public function afterGetEntityAttributes(DetailsSetup $subject, array $result)
    {
        $result['image'] = [
            'input'      => 'image',
            'label'      => 'Image',
            'global'     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'type'       => 'varchar',
            'group'      => 'General',
            'required'   => false,
            'sort_order' => 140,
        ];
        return $result;
    }
}
