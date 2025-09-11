<?php

namespace Tatvam\Wisernotify\Controller\Adminhtml\Settings;

use Magento\Framework\HTTP\Client\Curl;

class Index extends \Magento\Backend\App\Action
{
    protected $curl;
    protected $PageFactory;
    protected $settingsFactory;
    protected $_publicActions = ['index'];

    /**
     * Constructor.
     */
    public function __construct(
        Curl $curl,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $PageFactory,
        \Tatvam\Wisernotify\Model\SettingsEntryFactory $settingsFactory
    ) {
        parent::__construct($context);
        $this->curl = $curl;
        $this->PageFactory = $PageFactory;
        $this->settingsFactory = $settingsFactory;

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $siteUrl = $storeManager->getStore()->getBaseUrl();
        $siteUrl = str_replace('http://', '', str_replace('https://', '', $siteUrl));
        $siteUrl = rtrim($siteUrl, '/');

        $post = (array) $this->getRequest()->getPost();
        if (!empty($post) && !empty($post['apiKey'])) {
            $apiKey = $post['apiKey'];
            $postdata = [
                'ak' => $apiKey,
                "fa" => "mg",
                "status" => "1",
                "ht" => $siteUrl
            ];
            $data = json_encode($postdata);
            $url = 'https://is.wisernotify.com/api/verifyAPI';
            $this->curl->addHeader('Content-type', 'application/json');
            $this->curl->post($url, $data);
            $json = $this->curl->getBody();
            $json = json_decode($json);
            if (!is_object($json) || empty($json->msg)) {
                $this->messageManager->addErrorMessage('Your API key is wrong & Please, Enter valid API key.');
            } else {
                $item = $this->settingsFactory->create()->load(1);
                $item->setKey($apiKey)->setTi($json->ti)->setPt($json->pt)->save();
                $this->sendRecentOrder($siteUrl, $apiKey, $json->ti);
                $this->messageManager->addSuccessMessage('Congratulation! Your API key is verified & Also, Pixel tag is added successfully on your site.');
            }
        }
    }

    /**
     * Load the page defined in view/adminhtml/layout/tatvam_apikey_index.xml.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        return $PageFactory = $this->PageFactory->create();
    }

    /**
     * Send Recent 30 Orders On Activattion.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function sendRecentOrder($siteUrl, $apiKey, $apiTi)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore();
        $ordercollectionObject = $objectManager->get('\Magento\Sales\Model\ResourceModel\Order\CollectionFactory');

        $orderCollections = $ordercollectionObject->create()->addFieldToSelect(array('*'));
        $orderCollections->setOrder('entity_id', 'DESC');
        $orderCollections->setPageSize(30);
        $orderCollections->setCurPage(1);

        if (count($orderCollections) > 0) {
            foreach ($orderCollections as $key => $value) {
                $order = $objectManager->create('\Magento\Sales\Model\Order')->load($value->getId());
                /*$orderItems = $order->getAllItems();
                foreach ($orderItems as $item) {
                    $product_name = $item->getName();
                    $product_id = $item->getProductId();
                    echo "<pre>";
                    print_r($item->getData());
                }*/
                $firstname = $order->getBillingAddress()->getFirstName();
                $lastname = $order->getBillingAddress()->getLastName();
                $countryFactoryObj = $objectManager->get('\Magento\Directory\Model\CountryFactory');
                $country = $countryFactoryObj->create()->loadByCode($order->getBillingAddress()->getCountryId());
                if ($country) {
                    $countryName = $country->getName();
                }
                $postdata['un'] = $firstname . " " . $lastname;
                $postdata['e'] = $order->getCustomerEmail();
                $postdata['ct'] = $order->getBillingAddress()->getCity();
                $postdata['st'] =  $order->getBillingAddress()->getRegionCode();
                $postdata['cn'] = $countryName;
                $postdata['i'] = $order->getRemoteIp();
                $postdata['lt'] = '';
                $postdata['lg'] = '';
                $postdata['ht'] = $siteUrl;
                $postdata['fa'] = 'magento';
                $timezoneObj = $objectManager->get('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
                $postdata['insdt'] =  $timezoneObj->date()->format('Y/m/d H:i:s');
                $products = $order->getAllItems();
                foreach ($products as $product) {
                    $productData = $objectManager
                        ->get('Magento\Catalog\Model\Product')
                        ->load($product->getProductId());
                    $pname = $product->getName();
                    $link = $productData->getProductUrl();
                    $image = $storeManager->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $productData->getImage();
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
        }
    }
}
