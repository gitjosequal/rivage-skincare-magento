<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml\Iframe;

/**
 * Class to show swatch image and save it on disk
 */
class Show extends \Magento\Swatches\Controller\Adminhtml\Iframe\Show
{
    const RESOURCE = 'mageworkshop_detailedreview::review_attribute';

    /**
     * Check if user has enough privileges
     *
     * @codeCoverageIgnore
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::RESOURCE);
    }
}
