<?php
/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */
namespace TD\MasterCard\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use TD\MasterCard\Gateway\Config\Config;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Payment\Helper\Data as PaymentData;

/**
 * Class TD_MasterCardments_Helper_Data
 *
 * Provides helper methods for retrieving data for the mastercard plugin
 */
class Data extends AbstractHelper
{


    const VPC_VERSION = '1';
    const COMMAND_PAY = 'pay';
    const COMMAND_CAPTURE = 'capture';
    const VPC_URL = 'https://migs.mastercard.com.au/vpcpay';

    /**
     * @var \TD\MasterCard\Gateway\Config\Config
     */
    protected $_gatewayConfig;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \TD\MasterCard\Gateway\Config\Config $gatewayConfig
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager ,
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        Config $gatewayConfig,
        ObjectManagerInterface $objectManager,
        Context $context,
        PaymentData $paymentData,
        StoreManagerInterface $storeManager,
        ResolverInterface $localeResolver
    )
    {
        $this->_gatewayConfig = $gatewayConfig;
        $this->_objectManager = $objectManager;
        $this->_paymentData = $paymentData;
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_scopeConfig = $context->getScopeConfig();

        parent::__construct($context);
    }

    /**
     * Creates an Instance of the Helper
     * @param  \Magento\Framework\ObjectManagerInterface $objectManager
     * @return \TD\MasterCard\Helper\Data
     */
    public static function getInstance($objectManager)
    {
        return $objectManager->create(
            get_class()
        );
    }

    protected function getGatewayConfig()
    {
        return $this->_gatewayConfig;
    }

    /**
     * Get an Instance of the Magento Object Manager
     * @return \Magento\Framework\ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * Get an Instance of the Magento Store Manager
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    protected function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * Get an Instance of the Magento UrlBuilder
     * @return \Magento\Framework\UrlInterface
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * Get an Instance of the Magento Scope Config
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected function getScopeConfig()
    {
        return $this->_scopeConfig;
    }

    /**
     * Get an Instance of the Magento Core Locale Object
     * @return \Magento\Framework\Locale\ResolverInterface
     */
    public function getLocaleResolver()
    {
        return $this->_localeResolver;
    }

    /**
     * get the URL of the configured mastercard gateway checkout
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getGatewayConfig()->getGatewayUrl();
    }

    /**
     * @return string
     */
    public function getCompleteUrl()
    {
        return $this->getStoreManager()->getStore()->getBaseUrl() . 'mastercard/checkout/success';
    }


    /**
     * Get Store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->getStoreManager()->getStore()->getCode();
    }


    /**
     * Get Merchant Secure Secret Key
     *
     * @return string
     */
    public function getVpcVersion()
    {
        return self::VPC_VERSION;
    }

    /**
     * Get Command Pay
     *
     * @return string
     */
    public function getCommandPay()
    {
        return self::COMMAND_PAY;
    }

    /**
     * Get Command Capture
     *
     * @return string
     */
    public function getCommandCapture()
    {
        return self::COMMAND_CAPTURE;
    }

    /**
     * Get VPC Url
     *
     * @return string
     */
    public function getVpcUrl()
    {
        return self::VPC_URL;
    }

    /**
     * Decide grand total
     * @param $order Mage_Sales_Model_Order
     * @return int
     */
    public function getGrandTotal($order)
    {
        // For MasterCard: always use BaseGrandTotal
        // Currency is set at Merchant account level
        $amount = $order->getBaseGrandTotal();
        return round($amount * 100);
    }
}
