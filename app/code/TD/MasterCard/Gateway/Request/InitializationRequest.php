<?php
/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */
namespace TD\MasterCard\Gateway\Request;

use Magento\Sales\Model\Order;
use Magento\Payment\Gateway\Data\Order\OrderAdapter;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Checkout\Model\Session;
use TD\MasterCard\Gateway\Config\Config;
use Psr\Log\LoggerInterface;

class InitializationRequest implements BuilderInterface
{
    private $_logger;
    private $_session;
    private $_gatewayConfig;

    /**
     * @param   Config $config
     * @param   LoggerInterface $logger
     * @param   Session $session
     */
    public function __construct(
        Config          $gatewayConfig,
        LoggerInterface $logger,
        Session         $session
    ) {
        $this->_gatewayConfig = $gatewayConfig;
        $this->_logger = $logger;
        $this->_session = $session;
    }

    /**
     * Builds ENV request
     * From: https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Payment/Model/Method/Adapter.php
     * The $buildSubject contains:
     * 'payment' => $this->getInfoInstance()
     * 'paymentAction' => $paymentAction
     * 'stateObject' => $stateObject
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject) {

        $payment = $buildSubject['payment'];
        $stateObject = $buildSubject['stateObject'];

        $order = $payment->getOrder();

        if($this->validateQuote($order)) {
            $stateObject->setState(Order::STATE_PENDING_PAYMENT);
            $stateObject->setStatus(Order::STATE_PENDING_PAYMENT);
            $stateObject->setIsNotified(false);
        } else {
            $stateObject->setState(Order::STATE_CANCELED);
            $stateObject->setStatus(Order::STATE_CANCELED);
            $stateObject->setIsNotified(false);
        }

        return [ 'IGNORED' => [ 'IGNORED' ] ];

    }

    /**
     * Checks the quote for validity
     * @throws Mage_Api_Exception
     */
    private function validateQuote(OrderAdapter $order) {
        return true;
    }
}