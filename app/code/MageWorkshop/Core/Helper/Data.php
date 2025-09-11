<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Core\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MODULE_ENABLED = '/general/enabled';

    const MODULE_SETTING_ENABLE = '/enabled';

    const SECTION_MAGEWORKSHOP_DETAILED_REVIEW = 'mageworkshop_detailedreview/';

    /** @var \Magento\Framework\App\State $appState */
    protected $appState;

    /** @var \Magento\Framework\App\Helper\Context $context */
    protected $context;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Helper\Context $context
    )
    {
        $this->appState = $appState;
        parent::__construct($context);
    }

    /**
     * @return bool
     * @param $moduleName
     */
    public function isModuleEnabled($moduleName)
    {
        return (bool)$this->scopeConfig->getValue(
            strtolower($moduleName) . self::MODULE_ENABLED
        );
    }

    /**
     * @param $moduleName
     * @return bool
     */
    public function isModuleEnabledInDetailedReviewSection($moduleName)
    {
        return (bool)$this->scopeConfig->getValue(
            self::SECTION_MAGEWORKSHOP_DETAILED_REVIEW . strtolower($moduleName) . self::MODULE_SETTING_ENABLE
        );
    }

    /**
     * @return bool
     */
    public function isAdminArea()
    {
        return (bool)($this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
    }

    /**
     * @param $moduleName
     * @return bool
     */
    public function isAdminAreaAndModuleEnable($moduleName)
    {
        return (bool)($this->isModuleEnabled($moduleName) && $this->isAdminArea());
    }
}