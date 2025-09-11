<?php

namespace Tatvam\Wisernotify\Model;

use Magento\FrameWork\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class SettingsEntry extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'tatvam_wisernotify_settingsentry';

    /**
     * Constructor.
     */
    public function _construct()
    {
        $this->_init('Tatvam\Wisernotify\Model\ResourceModel\SettingsEntry');
    }

    /**
     * Model identity.
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }
}
