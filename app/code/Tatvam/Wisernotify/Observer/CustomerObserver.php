<?php

namespace Tatvam\Wisernotify\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;
use Tatvam\Wisernotify\Model\SettingsEntryFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class CustomerObserver implements ObserverInterface
{
    protected $curl;
    protected $settings;
    protected $timezone;
    protected $apiKey;

    /**
     * Constructor.
     */
    public function __construct(
        Curl $curl,
        SettingsEntryFactory $settings,
        TimezoneInterface $timezone
    ) {
        $this->curl = $curl;
        $this->settings = $settings;
        $this->timezone = $timezone;
        $this->apiKey = $this->settings->create()->load(1)->getKey();
    }

    /**
     * Execute observer action.
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $getApi = $this->settings->create()->load(1);
            $this->apiKey = $getApi->getKey();
            $apiKey = $this->apiKey;
            $apiTi = $getApi->getTi();

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
            $siteUrl = $storeManager->getStore()->getBaseUrl();
            $siteUrl = str_replace('http://','',str_replace('https://','',$siteUrl));
            $siteUrl = rtrim($siteUrl, '/');

            $postdata = [];
            $customer = $observer->getEvent()->getCustomer();
            $firstname = $customer->getFirstName();
            $lastname = $customer->getLastName();

            $postdata['un'] = $firstname." ".$lastname;
            $postdata['e'] = $customer->getEmail();
            $postdata['ct'] = '';
            $postdata['st'] =  '';
            $postdata['cn'] = '';
            $postdata['i'] = '';
            $postdata['lt'] = '';
            $postdata['lg'] = '';
            $postdata['ht'] = $siteUrl;
            $postdata['fa'] = 'magento';
            $postdata['insdt'] =  $this->timezone->date()->format('Y/m/d H:i:s');

            $data = json_encode($postdata);
            $url = 'https://is.wisernotify.com/api/mg/data';
            $this->curl->addHeader('Content-type', 'application/json');
            $this->curl->addHeader('ak', $apiKey);
            $this->curl->addHeader('ti', $apiTi);
            $this->curl->post($url, $data);
            $json = $this->curl->getBody();
            $json = json_decode($json);
            return true;
        } catch (\Exception $e) {
        }
        return true;
    }
}
