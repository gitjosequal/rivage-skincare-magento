<?php

namespace Tatvam\Wisernotify\Block;

use Tatvam\Wisernotify\Model\SettingsEntryFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Settings extends Template
{
    protected $settingsFactory;

    /**
     * Constructor.
     *
     * @param $context the context
     * @param $settingsFactory
     */
    public function __construct(
        Context $context,
        SettingsEntryFactory $settingsFactory
    ) {
        $this->settingsFactory = $settingsFactory;
        parent::__construct($context);
    }

    /**
     * Api Key Getter.
     *
     * @return api key
     */
    public function getApiKey()
    {
        $item = $this->settingsFactory->create()->load(1);
        return $item->getKey();
    }

    public function getApiPt()
    {
        $item = $this->settingsFactory->create()->load(1);
        return $item->getPt();
    }
}
