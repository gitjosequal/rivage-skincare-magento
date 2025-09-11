<?php
namespace MageGuide\AlphaBank\Model;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote\Payment;
class Alphabank extends \Magento\Payment\Model\Method\Cc
{
    const CODE 								= 'alphabank_directpost';
	const CcShouldPayInstalment 			= 'cc_should_pay_with_installments';
	const CcNumberOfInstalment 				= 'cc_number_of_installments';
	protected $_code 						= self::CODE;
	protected $_minOrderTotal 				= 0;
    protected $_supportedCurrencyCodes 		= array('USD','GBP','EUR');
	protected $_countryFactory;
	protected $_minAmount = null;
    protected $_maxAmount = null;
	protected $_scopeConfig;
    protected $_helper;
	protected $_isOffline = false;
	protected $_invoiceService;
	protected $_transaction;
	protected $_InvoiceSender;
	protected $_transactionBuilder;

    public function __construct(
		\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
		\Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
		\MageGuide\AlphaBank\Helper\Data $helper,
		\Magento\Sales\Model\Order\Email\Sender\InvoiceSender $InvoiceSender,
		\Magento\Sales\Model\Order\Payment\Transaction\Builder $transactionBuilder,
		array $data = array()
    ) {
		parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $moduleList,
            $localeDate,
			null,
			null,
            $data
        );
		$this->_code 			= self::CODE;
		$this->_helper 			= $helper;
		$this->_scopeConfig 	= $scopeConfig;
		$this->_invoiceService 	= $invoiceService;
        $this->_transaction 	= $transaction;
		$this->_InvoiceSender   = $InvoiceSender;
		$this->_transactionBuilder = $transactionBuilder;
    }

	public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null){
			return $this->_helper->isPaymentMethodAvailable();

		return false;
    }

	public function validate()
	{
		return true;
	}

	public function getOrderPlaceRedirectUrl()
	{
		return $this->_helper->getStartUrl();
	}

	public function assignData(\Magento\Framework\DataObject $data)
    {
		$additionalData = $data->getData('additional_data');
		$info = $this->getInfoInstance();
		$info->setAdditionalInformation(
            self::CcShouldPayInstalment,
            isset($additionalData[self::CcShouldPayInstalment]) ? $additionalData[self::CcShouldPayInstalment] : ''
        );

		$info->setAdditionalInformation(
            self::CcNumberOfInstalment,
            isset($additionalData[self::CcNumberOfInstalment]) ? $additionalData[self::CcNumberOfInstalment] : ''
        );
		return $this;
	}

	public function addRedirectFormPostFields($form)
	{
		$order 				= $this->_helper->getOrder();
		
		$order->setState(\Magento\Sales\Model\Order::STATE_NEW, true);
		$order->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
					
		$billingAddress 	= $order->getBillingAddress();
		$shippingAddress 	= $order->getShippingAddress();
		$billingStreet	 	= $billingAddress->getStreet();
		$shippingStreet 	= $shippingAddress->getStreet();
		$payment 			= $order->getPayment();
		$numberofInstallments = $extInstallmentoffset = '';
		if($payment->getAdditionalInformation('cc_should_pay_with_installments')==1 && $payment->getAdditionalInformation('cc_should_pay_with_installments')!='')
		{
			$numberofInstallments	= $payment->getAdditionalInformation('cc_number_of_installments');
			$extInstallmentoffset	= $this->_helper->getInstallmentOffset();
		}
		$form_field_array = array
								(
									"mid" 					=> $this->_helper->getMid(),
									"lang" 					=> $this->_helper->getLanguageCode(),
									"deviceCategory" 		=> "",
									"orderid" 				=> $this->_helper->getOrderIdFromSession(),
									"orderDesc" 			=> "",
									"orderAmount" 			=> number_format((float) $order->getGrandTotal(), 2, ',', ''),
									"currency" 				=> $this->_helper->getCurrencyCode(),
									"payerEmail" 			=> $order->getCustomerEmail(),
									"payerPhone" 			=> $shippingAddress->getTelephone(),
									"billCountry" 			=> $billingAddress->getCountryId(),
									"billState" 			=> $billingAddress->getRegion(),
									"billZip" 				=> $billingAddress->getPostcode(),
									"billCity" 				=> $billingAddress->getCity(),
									"billAddress" 			=> $billingStreet[0],
									"shipCountry" 			=> $shippingAddress->getCountryId(),
									"shipState" 			=> $shippingAddress->getRegion(),
									"shipZip" 				=> $shippingAddress->getPostcode(),
									"shipCity" 				=> $shippingAddress->getCity(),
									"shipAddress" 			=> $shippingStreet[0],
									"weight" 				=> "",
									"dimensions" 			=> "",
									"addFraudScore" 		=> "",
									"maxPayRetries" 		=> "",
									"reject3dsU" 			=> "",
									"payMethod" 			=> "",
									"trType" 				=> $this->_helper->getAlphabankPaymentAction(),
									"extInstallmentoffset" 	=> $extInstallmentoffset,
									"extInstallmentperiod" 	=> $numberofInstallments,
									"extRecurringfrequency" => "",
									"extRecurringenddate" 	=> "",
									"blockScore" 			=> "",
									"cssUrl" 				=> "",
									"confirmUrl" 			=> $this->_helper->getSuccessUrl(),
									"cancelUrl" 			=> $this->_helper->getCancelUrl()
								);
		$this->_helper->log('--------------Order Request Starts---------------',$this->_helper->getOrderIdFromSession());
		foreach($form_field_array as $key =>$val)
		{
			$form->addField($key, 'hidden', array('name' => $key,'value' => $val));
		}
		$digest = $this->_helper->getDigset($form_field_array);
		$form->addField("digest", 'hidden', array('name' => "digest" ,'value' => $digest));
		$this->_helper->log($form_field_array,$this->_helper->getOrderIdFromSession());
		$this->_helper->log("Digest--:-".$digest,$this->_helper->getOrderIdFromSession());
		$this->_helper->log('--------------Order Request Ends---------------',$this->_helper->getOrderIdFromSession());
		return $form;
	}

	public function processOrder($response)
	{
		$order = $this->_helper->getOrder();
		print_R($order);die;
		if($order){
			try{
				if($this->_helper->getAlphabankPaymentAction()==\MageGuide\AlphaBank\Model\System\Config\Source\AlphabankpaymentAction::SALE){
					$this->createInvoice($order);
					$transactionType = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE;
					$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
					$order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
				}
				else
				{
					$order->setState(\Magento\Sales\Model\Order::STATE_NEW, true);
					$order->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
					$transactionType = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH;
				}

				if(isset($response['txId'])){
					$payment = $order->getPayment();
					$payment->setTransactionId($response['txId']);
					$payment->setTransactionAdditionalInfo("raw_details_info",$response);
					$payment->setIsClosed(1);

					$transaction = $this->_transactionBuilder->setPayment($payment)
						->setOrder($order)
						->setTransactionId($response['txId'])
						->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array)$response])
						->setFailSafe(true)
						->build($transactionType);
					$formatedPrice = $order->getBaseCurrency()->formatTxt($order->getGrandTotal());
					$message = __('The Captured amount is %1.', $formatedPrice);
					$payment->addTransactionCommentsToOrder($transaction,$message);
					$payment->setParentTransactionId(null);
					$transaction->save();
					$payment->save();
					$order->save();
				}
				$this->_helper->getSession()->getQuote()->setIsActive(false)->save();
				return true;
			}
			catch(\Exception $e){}
		}
		return false;
	}

	protected function createInvoice($order)
	{
		if($order){
			$processingOrderStatus = $this->_helper->getProcessingOrderStatus();
			if(!$order->hasInvoices()){
				if($order->canInvoice())
				{
					$invoice = $this->_invoiceService->prepareInvoice($order);
					$invoice->register();
					$invoice->save();
					$transactionSave = $this->_transaction->addObject(
						$invoice
					)->addObject(
						$invoice->getOrder()
					);
					$transactionSave->save();
					$this->_InvoiceSender->send($invoice);
					$order->addStatusHistoryComment(
						__('Notified customer about invoice #%1.', $invoice->getId())
					)
					->setIsCustomerNotified(true)
					->save();
				}
			}
			$order->setState($processingOrderStatus,$processingOrderStatus, '',true)->save();
		}
	}
}
