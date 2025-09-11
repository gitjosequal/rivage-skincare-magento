<?php
/**
 * Copyright © Rivage(info@rivage.com) All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rivage\GtmExtension\Helper\Data as GtmExtensionHelper;

class GtmCodeSnippet implements ArgumentInterface
{
    /**
     * @var GtmExtensionHelper
     */
    private $gtmextensionHelper;

    /**
     * Constructor
     *
     * @param GtmExtensionHelper $gtmextensionHelper
     */
    public function __construct(
        GtmExtensionHelper $gtmextensionHelper
    ) {
        $this->gtmextensionHelper = $gtmextensionHelper;
    }

     /**
      * Get Module Status
      *
      * @return string
      */
    public function isModuleEnabled()
    {
        return $this->gtmextensionHelper->isModuleEnabled();
    }

    /**
     * Get GTM Js code snippet
     *
     * @return string
     */
    public function getGtmJsCode()
    {
        return $this->gtmextensionHelper->getGtmJsCode();
    }

    /**
     * Get GTM Non Js code snippet
     *
     * @return string
     */
    public function getGtmNonJsCode()
    {
        return $this->gtmextensionHelper->getGtmNonJsCode();
    }
}
