<?php

namespace Tatvam\Wisernotify\Model;

class Api
{
    protected $settingsFactory;

    /**
     * Constructor.
     */
    public function __construct(SettingsEntryFactory $settingsFactory)
    {
        $this->settingsFactory = $settingsFactory;
    }

    /**
     * Get API Key.
     *
     * @return api key
     */
    public function getApiKey()
    {
        $item = $this->settingsFactory->create()->load(1);

        return $item->getKey();
    }
}
