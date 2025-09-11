<?php

namespace Tatvam\Wisernotify\Observer;

use Magento\Customer\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Client\Curl;
use Tatvam\Wisernotify\Model\SettingsEntryFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Directory\Model\CountryFactory;

class PurchaseObserver implements ObserverInterface
{
    protected $curl;
    protected $checkoutSession;
    protected $order;
    protected $settings;
    protected $timezone;
    protected $apiKey;
    protected $countryFactory;

    /**
     * Constructor.
     */
    public function __construct(
        Curl $curl,
        Session $checkoutSession,
        Order $order,
        SettingsEntryFactory $settings,
        TimezoneInterface $timezone,
        CountryFactory $countryFactory
    ) {
        $this->curl = $curl;
        $this->checkoutSession = $checkoutSession;
        $this->order = $order;
        $this->settings = $settings;
        $this->timezone = $timezone;
        $this->apiKey = $this->settings->create()->load(1)->getKey();
        $this->countryFactory = $countryFactory;
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
            $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore();
            $siteUrl = $storeManager->getBaseUrl();
            $siteUrl = str_replace('http://','',str_replace('https://','',$siteUrl));
            $siteUrl = rtrim($siteUrl, '/');

            $postdata = [];
            $countryName = '';
            $orderids = $observer->getEvent()->getOrderIds();
            foreach($orderids as $orderid){
                $order = $this->order->load($orderid);
                $firstname = $order->getBillingAddress()->getFirstName();
                $lastname = $order->getBillingAddress()->getLastName();
                $country = $this->countryFactory->create()->loadByCode($order->getBillingAddress()->getCountryId());
                if ($country) {
                    $countryName = $country->getName();
                }
                $postdata['un'] = $firstname." ".$lastname;
                $postdata['e'] = $order->getCustomerEmail();
                $postdata['ct'] = $order->getBillingAddress()->getCity();
                $postdata['st'] =  $order->getBillingAddress()->getRegionCode();
                $postdata['cn'] = $countryName;
                $postdata['i'] = $order->getRemoteIp();
                $postdata['lt'] = '';
                $postdata['lg'] = '';
                $postdata['ht'] = $siteUrl;
                $postdata['fa'] = 'magento';
                $postdata['insdt'] =  $this->timezone->date()->format('Y/m/d H:i:s');
                $products = $order->getAllItems();
                foreach ($products as $product)
                {
                    $productData = $objectManager
                        ->get('Magento\Catalog\Model\Product')
                        ->load($product->getProductId());
                    $pname = $product->getName();
                    $link = $productData->getProductUrl();
                    $image = $storeManager->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$productData->getImage();
					$postdata['psku'] = $productData->getSku();
                    $postdata['pn'] = $pname;
                    $postdata['pu'] = $link;
                    $postdata['piu'] = $image;
                    $data = json_encode($postdata);
                    $url = 'https://is.wisernotify.com/api/mg/data';
                    $this->curl->addHeader('Content-type', 'application/json');
                    $this->curl->addHeader('ak', $apiKey);
                    $this->curl->addHeader('ti', $apiTi);
                    $this->curl->post($url, $data);
                    $json = $this->curl->getBody();
                    $json = json_decode($json);
                }
            }
            return true;
        } catch (\Exception $e) {
        }
        return true;
    }
}
