<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Adminhtml\Review;

class AdditionalFieldsContainer
    extends \Magento\Framework\View\Element\AbstractBlock
    implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $htmlId = $element->getHtmlId();
        return "<div id='$htmlId'></div>";
    }
}
