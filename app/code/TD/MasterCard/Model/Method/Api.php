<?php
/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */

namespace TD\MasterCard\Model\Method;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;

class Api extends \Magento\Payment\Model\Method\Cc
{
    const CODE                          = 'mastercard_api';
    const COMMAND_PAY                   = 'pay';
    const COMMAND_CAPTURE               = 'capture';
    const COMMAND_REFUND                = 'refund';
    const VPC_URL                       = 'https://migs.mastercard.com.au/vpcdps';

    protected $_code                    = self::CODE;
    protected $_isGateway               = true;
    /**
     * @var bool
     */
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;

    protected $_countryFactory;

    /**
     * @var \Magento\Framework\HTTP\Adapter\Curl
     */
    protected $_curl;

    /**
     * API call HTTP headers
     *
     * @var array
     */
    protected $_headers = [];
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var TransactionCollectionFactory
     */
    protected $_salesTransactionCollectionFactory;

    protected $_merchantId = null;
    protected $_accessCode = null;
    protected $_operatorId = null;
    protected $_operatorPassword = null;


    protected $_debugReplacePrivateDataKeys = ['number', 'exp_month', 'exp_year', 'cvc'];

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
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\HTTP\Adapter\Curl $curl,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        TransactionCollectionFactory $salesTransactionCollectionFactory,
        array $some = array(),
        array $data = array()
    )
    {
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

        $this->_countryFactory = $countryFactory;
        $this->_merchantId = $this->getConfigData('merchant_id');
        $this->_accessCode = $this->getConfigData('access_code');
        $this->_operatorId = $this->getConfigData('operator_id');
        $this->_operatorPassword = $this->getConfigData('operator_password');
        $this->_curl = $curl;
        $this->_encryptor = $encryptor;
        $this->_salesTransactionCollectionFactory = $salesTransactionCollectionFactory;
    }

    /**
     * Do the API call
     *
     * @param string $methodName Method name
     * @param array  $request    Request data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _callAPI(\Magento\Payment\Model\InfoInterface $payment, $type)
    {
        $amount = $this->getAmount() * 100;
        // Create expiry date in format "YY/MM"
        $dateExpiry = substr($payment->getCcExpYear(), 2, 2) . str_pad($payment->getCcExpMonth(), 2, '0', STR_PAD_LEFT);

        $request = array();
        $request['vpc_Version'] = '1';
        if ($type == self::COMMAND_CAPTURE) {
            $request['vpc_Command'] = self::COMMAND_CAPTURE;
        } elseif ($type == self::COMMAND_REFUND) {
            $request['vpc_Command'] = self::COMMAND_REFUND;
        } else {
            $request['vpc_Command'] = self::COMMAND_PAY;
        }
        $request['vpc_MerchTxnRef'] = $payment->getOrder()->getIncrementId();
        $request['vpc_Merchant'] = htmlentities($this->_merchantId);
        $request['vpc_AccessCode'] = htmlentities($this->_accessCode);
        $request['vpc_OrderInfo'] = $payment->getOrder()->getIncrementId();
        if ($type != self::COMMAND_REFUND) {
            $request['vpc_CardNum'] = htmlentities($payment->getCcNumber());
            $request['vpc_CardExp'] = htmlentities($dateExpiry);
            $request['vpc_CardSecurityCode'] = htmlentities($payment->getCcCid());
        }


        $request['vpc_Amount'] = htmlentities($amount);
        $request['vpc_CSCLevel'] = 'N';
        $request['vpc_TicketNo'] = '';
        $request['vpc_TransNo'] = $payment->getLastTransId();

        if ($type == self::COMMAND_CAPTURE) {
            $collection = $this->_salesTransactionCollectionFactory->create()
                ->addFieldToFilter('payment_id', $payment->getId())
                ->addFieldToFilter('txn_type', PaymentTransaction::TYPE_CAPTURE);

            $collection->load();
            if ($collection->getSize() > 0) {
                $collection = $this->_salesTransactionCollectionFactory->create()
                    ->addPaymentIdFilter($payment->getId())
                    ->addTxnTypeFilter(PaymentTransaction::TYPE_AUTH)
                    ->setOrder('created_at', \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
                    ->setOrder('transaction_id', \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
                    ->setPageSize(1)
                    ->setCurPage(1);
                $authTransaction = $collection->getFirstItem();
                if (!$authTransaction->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Can not find original authorization transaction for capture')
                    );
                } else {
                    $request['vpc_TransNo'] = $authTransaction->getTxnId();
                }

            } else {
                $request['vpc_TransNo'] = $payment->getLastTransId();
            }
        }
        if ($type == self::COMMAND_CAPTURE || $type == self::COMMAND_REFUND) {
            $request['vpc_User'] = $this->_operatorId;
            $request['vpc_Password'] = $this->_encryptor->decrypt(trim($this->_operatorPassword));
        }
        if ($type == self::COMMAND_REFUND) {
            $transactionId = $payment->getParentTransactionId();
            $request['vpc_TransNo'] = $transactionId;
        }
        try {
            $http = $this->_curl;
            $config = ['timeout' => 30, 'verifypeer' => false, 'verifyhost' => 0];
            $http->setConfig($config);
            $http->write(
                \Zend_Http_Client::POST,
                self::VPC_URL,
                '1.1',
                $this->_headers,
                $this->_buildQuery($request)
            );
            $response = $http->read();

        } catch (\Exception $e) {
            $debugData['http_error'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->_debug($debugData);
            throw $e;
        }

        if ($http->getErrno()) {
            $this->_logger->critical(
                new \Exception(
                    sprintf('MasterCard gateway connection error #%s: %s', $http->getErrno(), $http->getError())
                )
            );
            $http->close();

            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t connect to the MasterCard gateway.'));
        }

        // Close the connection
        $http->close();

        // Strip out header tags
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        // Fill out the results
        $result = array();
        $pieces = explode('&', $response);
        foreach ($pieces as $piece) {
            $tokens = explode('=', $piece);
            $result[$tokens[0]] = $tokens[1];
        }
        return $result;
    }

    /**
     * Do the API call
     *
     * @param string $methodName Method name
     * @param array  $request    Request data
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _call(array $request)
    {

        $curlURL = 'https://migs.mastercard.com.au/vpcdps';
        try {
            $http = $this->_curl;
            $config = ['timeout' => 30, 'verifypeer' => false, 'verifyhost' => 0];
            $http->setConfig($config);
            $http->write(
                \Zend_Http_Client::POST,
                $curlURL,
                '1.1',
                $this->_headers,
                $this->_buildQuery($request)
            );
            $response = $http->read();

        } catch (\Exception $e) {
            $debugData['http_error'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->_debug($debugData);
            throw $e;
        }

        if ($http->getErrno()) {
            $this->_logger->critical(
                new \Exception(
                    sprintf('MasterCard gateway connection error #%s: %s', $http->getErrno(), $http->getError())
                )
            );
            $http->close();

            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t connect to the MasterCard gateway.'));
        }

        // Close the connection
        $http->close();

        // Strip out header tags
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        // Fill out the results
        $result = array();
        $pieces = explode('&', $response);
        foreach ($pieces as $piece) {
            $tokens = explode('=', $piece);
            $result[$tokens[0]] = $tokens[1];
        }

        return $result;
    }

    /**
     * Build query string from request
     *
     * @param array $request request data
     * @return string
     */
    protected function _buildQuery($request)
    {
        return http_build_query($request);
    }

    /**
     * Authorizes specified amount
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws LocalizedException
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->setAmount($amount)
            ->setPayment($payment);
        try {
            $result = $this->_callAPI($payment, self::COMMAND_PAY);
            if ($result['vpc_TxnResponseCode'] == '0') {
                $payment->setTransactionId($result['vpc_TransactionNo']);
                $payment->setIsTransactionClosed(0);

            } else {
                $this->_logger->error(__($result['vpc_TxnResponseCode']));
                throw new \Magento\Framework\Validator\Exception(__($result['vpc_Message']));
            }
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            throw new \Magento\Framework\Validator\Exception(__($e->getMessage()));
        }

        return $this;
    }

    /**
     * Payment capturing
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float                                $amount
     * @return $this
     * @throws \Magento\Framework\Validator\Exception
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->setAmount($amount)
            ->setPayment($payment);
        if (!$payment->getTransactionId()) {
            //$this->authorize($payment, $amount);
            //$result = $this->_callAPI($payment, self::COMMAND_PAY);
            try {
                $result = $this->_callAPI($payment, self::COMMAND_PAY);
                if ($result['vpc_TxnResponseCode'] == '0') {
                    $payment->setTransactionId($result['vpc_TransactionNo']);
                    $payment->setIsTransactionClosed(1);

                } else {
                    $this->_logger->error(__($result['vpc_TxnResponseCode']));
                    throw new \Magento\Framework\Validator\Exception(__($result['vpc_Message']));
                }
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
                throw new \Magento\Framework\Validator\Exception(__($e->getMessage()));
            }
        } else {
            try {
                $result = $this->_callAPI($payment, self::COMMAND_CAPTURE);
                if ($result['vpc_TxnResponseCode'] == '0') {
                    $payment->setTransactionId($result['vpc_TransactionNo']);
                    $payment->setIsTransactionClosed(1);

                } else {
                    $this->_logger->error(__($result['vpc_TxnResponseCode']));
                    throw new \Magento\Framework\Validator\Exception(__($result['vpc_Message']));
                }
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
                throw new \Magento\Framework\Validator\Exception(__($e->getMessage()));
            }
        }


        return $this;
    }

    /**
     * Payment refund
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Framework\Validator\Exception
     */
    public function refund(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $this->setAmount($amount)
            ->setPayment($payment);
        $transactionId = $payment->getParentTransactionId();
        try {
            $result = $this->_callAPI($payment, self::COMMAND_REFUND);
            if ($result['vpc_TxnResponseCode'] == '0') {
                $payment
                    ->setTransactionId($result['vpc_TransactionNo'])
                    //->setTransactionId($transactionId . '-'
                    //    . \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND)
                    ->setParentTransactionId($transactionId)
                    ->setIsTransactionClosed(1)
                    ->setShouldCloseParentTransaction(1);

            } else {
                $this->_logger->error(__($result['vpc_TxnResponseCode']));
                throw new \Magento\Framework\Validator\Exception(__($result['vpc_Message']));
            }
        } catch (\Exception $e) {
            $this->debugData(['transaction_id' => $transactionId, 'exception' => $e->getMessage()]);
            $this->_logger->error($e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }


        return $this;
    }

    /**
     * Determine method availability based on quote amount and config data
     *
     * @param \Magento\Quote\Api\Data\CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(\Magento\Quote\Api\Data\CartInterface $quote = null)
    {
        return parent::isAvailable($quote);
    }

}