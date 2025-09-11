<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Plugin\Ui\Component;

use Magento\Ui\Component\MassAction;
use MageWorkshop\CustomerPermissions\Model\Module\DetailsData;

class MassActionPlugin
{
    /** configuration array keys for url generation */
    const URL_PATH_KEY = 'urlPath';
    const URL_KEY = 'url';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /** @var \MageWorkshop\Core\Helper\Data */
    protected $coreHelper;

    /**
     * namespace to find the proper MassAction object
     * @var string
     */
    protected $namespace;

    /**
     * @var array
     */
    protected $additionalActionsArray = [];

    /**
     * MassActionPlugin constructor.
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \MageWorkshop\Core\Helper\Data $coreHelper
     * @param array $additionalActionsArray
     * @param string $namespace
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageWorkshop\Core\Helper\Data $coreHelper,
        $additionalActionsArray = [],
        $namespace = ''
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->additionalActionsArray = $additionalActionsArray;
        $this->namespace = $namespace;
        $this->coreHelper = $coreHelper;
    }

    /**
     * Additional actions are added to the configuration array
     *
     * @param MassAction $massAction
     */
    public function afterPrepare(MassAction $massAction)
    {
        // Don't add Permission's mass actions if the DR module is disabled
        if (!$this->coreHelper->isModuleEnabledInDetailedReviewSection(DetailsData::MODULE_CODE)) {
            return;
        }

        if ($this->namespace === $massAction->getContext()->getNamespace()) {
            $configurationArray = $massAction->getConfiguration();

            foreach ($this->additionalActionsArray as $actionItemArray) {
                if (array_key_exists(self::URL_PATH_KEY, $actionItemArray)) {
                    $actionItemArray[self::URL_KEY] = $this->urlBuilder->getUrl($actionItemArray[self::URL_PATH_KEY]);
                }
                $configurationArray['actions'][] = $actionItemArray;
            }

            $massAction->setData('config', $configurationArray);
        }
    }
}