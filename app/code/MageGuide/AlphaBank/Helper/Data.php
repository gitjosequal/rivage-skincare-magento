<?php
namespace MageGuide\AlphaBank\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const MAGENTO_GREEK_LANGUAGE 		= 'el_GR';
	const GREEK_LANGUAGE_CODE 			= 'el';
	const US_LANGUAGE_CODE    			= 'en';
    const TYPE_SALE						= 'SALE';
	const TYPE_AUTHORIZATION 			= 'AUTHORIZE';
	const TYPE_SETTLE 					= 'SETTLE';
	const TYPE_REFUND					= 'REFUND';
	const CURRENCY_CODE  				= 'EUR';

	protected $_catalogSession;
    protected $_customerSession;
    protected $_checkoutSession;
	protected $_orderFactory;
	protected $_orderCollectionFactory;
	protected $_storeManager;
	protected $_dir;
	protected $_io;
	protected $date;
	protected $_store;

    public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
    	\Magento\Framework\App\Helper\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Catalog\Model\Session $catalogSession,
        \Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Sales\Model\OrderFactory $orderFactory,
		\Magento\Framework\Filesystem\DirectoryList $dir,
		\Magento\Framework\Filesystem\Io\File $io,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\App\State $state,
		\Magento\Framework\Locale\Resolver $store,
		\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
	)
    {
		parent::__construct($context);
		$this->_customerSession  	= $customerSession;
		$this->_catalogSession   	= $catalogSession;
		$this->_checkoutSession  	= $checkoutSession;
		$this->_orderFactory 		= $orderFactory;
		$this->_storeManager	 	= $storeManager;
		$this->_dir 			 	= $dir;
		$this->_io				 	= $io;
		$this->_state			 	= $state;
		$this->date 			 	= $date;
		$this->_store 				= $store;
		$this->_orderCollectionFactory = $orderCollectionFactory;
    }


	public function isModuleEnabled()
    {
        $isEnabled = $this->getConfig('payment/alphabank_directpost/active');
        return $isEnabled;
    }

    public function getTitle()
    {
        return $this->getConfig('payment/alphabank_directpost/title');
    }

	public function getMid()
    {
        return $this->getConfig('payment/alphabank_directpost/mid');
    }

	public function getAlphabankPaymentAction()
    {
        return $this->getConfig('payment/alphabank_directpost/payment_action');
    }

	public function getPendingOrderStatus()
	{
		return $this->getConfig('payment/alphabank_directpost/pending_order_status');
	}

	public function getProcessingOrderStatus()
	{
		return $this->getConfig('payment/alphabank_directpost/processing_order_status');
	}

	public function getAlphabankGatewayUrl()
    {
        return $this->getConfig('payment/alphabank_directpost/gatewayurl');
    }

	public function getSecretkey()
    {
        return $this->getConfig('payment/alphabank_directpost/secretkey');
    }

	public function isInstallmentEnable()
	{
		return $this->getConfig('payment/alphabank_directpost/enable_installment');
	}

	public function getInstallmentPeriod()
	{
		return $this->getConfig('payment/alphabank_directpost/extInstallmentperiod');
	}

	public function getInstallmentOffset()
	{
		return $this->getConfig('payment/alphabank_directpost/extInstallmentoffset');
	}

	public function getStartUrl()
	{
	    $url = $this->_urlBuilder->getUrl('alphabank/index/start', $paramsHere = array());
	    $url = trim($url,'/');
		return $url;
	}
	public function getSuccessUrl()
	{
	    $url = $this->_urlBuilder->getUrl('alphabank/index/success', $paramsHere = array());
	    $url = trim($url,'/');
		return $url;
	}

	public function getCancelUrl()
	{
	    $url = $this->_urlBuilder->getUrl('alphabank/index/cancel', $paramsHere = array());
	    $url = trim($url,'/');
		return $url;
	}

	public function getCurrencyCode()
	{
		return self::CURRENCY_CODE;
	}

	public function isLogEnabled()
	{
		return $this->getConfig('payment/alphabank_directpost/log');
	}

	public function getLogDir()
	{
		return BP . '/var/log/mageguide/alphabank/';
	}

	public function getLanguageCode()
	{
		$locale_code = $this->_store->getLocale();
		if($locale_code == self::MAGENTO_GREEK_LANGUAGE)
			return self::GREEK_LANGUAGE_CODE;
		else
			return self::US_LANGUAGE_CODE;
	}

	public function isPaymentMethodAvailable()
	{
		$mid		   = $this->getMid();
		$secretKey	   = $this->getSecretkey();
		$gatewayUrl    = trim($this->getAlphabankGatewayUrl());
		if($gatewayUrl!='' && $secretKey!='' && $mid!='')
			return true;
	 	return false;
	}

	public function getInstallmentOptions()
	{
		$installmentsArr = array();
		if($this->isInstallmentEnable()){
			$installmentsStr = $this->getInstallmentPeriod();
			if($installmentsStr){
				$installmentsStrArr = explode(",",$installmentsStr);
				$cart_total = $this->getQuote()->getGrandTotal();
				for($i=0;$i<count($installmentsStrArr);$i++){
					$installmentStrArr2 = explode(":",$installmentsStrArr[$i]);
					if(count($installmentStrArr2)==2){
						if(is_numeric($installmentStrArr2[0]) && is_numeric($installmentStrArr2[1])){
							if($cart_total>=$installmentStrArr2[0])
								$installmentsArr[] = $installmentStrArr2[1];
						}
					}
				}
			}
			sort($installmentsArr);
		}
		return $installmentsArr;
	}

	public function getNumberofInstallments()
	{
		$installmentArray = array();
		$installment = $this->getInstallmentOptions();
		for($i=0;$i<count($installment);$i++)
		{
			$installmentArray[] = array('value' => $installment[$i],'installment' => $installment[$i].' Installments');
		}
		return $installmentArray;
	}

	public function getCurrentQuoteCustomerId()
	{
		$quote = $this->getQuote();
		if($quote->getCustomerId())
			return $quote->getCustomerId();
		return 0;
	}

	public function getSession()
	{
		if ($this->_state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            return $this->_backendQuoteSession;
        } else {
            return $this->_checkoutSession;
        }
 	}

	public function getQuote()
 	{
  		return $this->getSession()->getQuote();
 	}

	public function getOrder()
	{
		$orderId = '';
		$orderId = $this->_checkoutSession->getLastRealOrderId();
		if($orderId!='')
		{
			$order = $this->_orderFactory->create()->loadByIncrementId($orderId);
			if($order && $order->getId())
			{
				return $order;
			}
		}
		return '';
	}

	public function getOrderIdFromSession()
	{
		return $this->_checkoutSession->getLastRealOrderId();
	}

	public function getDigset($form_data_array)
	{
		$secret_key = $this->getSecretkey();
		$inputString = '';
		foreach($form_data_array as $key =>$val)
		{
			$inputString = $inputString.$val;
		}
		$form_data = $inputString.$secret_key;
		$digest = base64_encode(sha1($form_data,true));
		return $digest;
	}

	public function getDateTimeFormat($format = null, $input = null)
	{
		return $this->date->gmtDate($format,$input);
	}

	public function getConfig($config_path)
	{
    	return $this->scopeConfig->getValue($config_path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	public function log($message,$OrderNumber)
    {
		if($this->isLogEnabled()){
			$file = $OrderNumber.'.log';

			if($message){
				$logDir  = $this->getLogDir();
				$logFile = $logDir.$file;
				if(!is_dir($logDir)){
					$this->_io->mkdir($logDir,0777);
				}
				$writer = new \Zend\Log\Writer\Stream($logFile);
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($message);
			}
		}
	}
}