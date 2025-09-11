<?php

namespace Tatvam\Wisernotify\Model\System\Message;

class InstallMessage implements \Magento\Framework\Notification\MessageInterface
{
    protected $settings;

    /**
     * Constructor.
     */
    public function __construct(\Tatvam\Wisernotify\Model\SettingsEntryFactory $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Message identity.
     *
     * @return message identity key
     */
    public function getIdentity()
    {
        // Retrieve unique message identity
        return 'tatvam_wisernotify_installmessage';
    }

    /**
     * Should display message.
     *
     * @return true if needs to be displayed, false otherwise
     */
    public function isDisplayed()
    {
        // Return true to show your message, false to hide it
        $apiKey = $this->settings->create()->load(1)->getKey();

        return empty($apiKey);
    }

    /**
     * Get message text.
     *
     * @return message text
     */
    public function getText()
    {
        // Retrieve message text
        return 'Wisernotify is not configured! Go to System -> Wisernotify -> Settings';
    }

    /**
     * Get message severity.
     *
     * @return message severity
     */
    public function getSeverity()
    {
        // Possible values: SEVERITY_CRITICAL, SEVERITY_MAJOR, SEVERITY_MINOR, SEVERITY_NOTICE
        return self::SEVERITY_MAJOR;
    }
}
