<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Attribute\Edit\Tab\Options;

class Visual extends \Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual
{
    /**
     * Get JSON config
     * This method overrides the default one because in Magento 2.1 the iframe target URL is now set here
     * and we should rewrite it to our own controller
     *
     * @return string
     */
    public function getJsonConfig()
    {
        // parent::getJsonConfig() available since Magento v2.1.0 and is called only since that version
        // @IGNORE parent::getJsonConfig()
        $config = json_decode(parent::getJsonConfig(), true);
        if (isset($config['uploadActionUrl'])) {
            $config['uploadActionUrl'] = $this->getUrl('mageworkshop_detailedreview/iframe/show');
        }
        return json_encode($config);
    }
}
