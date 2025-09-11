<?php
/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */
namespace TD\MasterCard\Controller\Checkout;

use Magento\Sales\Model\Order;
use TD\MasterCard\Helper\Crypto;
use TD\MasterCard\Helper\Data;
use TD\MasterCard\Gateway\Config\Config;
use TD\MasterCard\Controller\Checkout\AbstractAction;

/**
 * @package TD\MasterCard\Controller\Checkout
 */
class Success extends AbstractAction {

    public function execute() {

        $isValid = $this->getCryptoHelper()->isValidSignature($this->getRequest()->getParams(), $this->getGatewayConfig()->getSecureSecret());
        $result = $this->getRequest()->get("vpc_TxnResponseCode");
        $orderId = $this->getRequest()->get("vpc_MerchTxnRef");
        $transactionId = $this->getRequest()->get("vpc_TransactionNo");
        $amount = $this->getRequest()->get("vpc_Amount");

        if(!$isValid) {
            $this->getLogger()->debug('Possible site forgery detected: invalid response signature.');
            $this->_redirect('checkout/onepage/error', array('_secure'=> false));
            return;
        }

        if(!$orderId) {
            $this->getLogger()->debug("MasterCard returned a null order id. This may indicate an issue with the MasterCard payment gateway.");
            $this->_redirect('checkout/onepage/error', array('_secure'=> false));
            return;
        }

        $order = $this->getOrderById($orderId);
        if(!$order) {
            $this->getLogger()->debug("MasterCard returned an id for an order that could not be retrieved: $orderId");
            $this->_redirect('checkout/onepage/error', array('_secure'=> false));
            return;
        }

        if($result == 0 && $order->getState() === Order::STATE_PROCESSING) {
            $this->_redirect('checkout/onepage/success', array('_secure'=> false));
            return;
        }

        if($result != 0 && $order->getState() === Order::STATE_CANCELED) {
            $this->_redirect('checkout/onepage/failure', array('_secure'=> false));
            return;
        }

        if ($result == 0) {
            $orderState = Order::STATE_PROCESSING;

            $orderStatus = $this->getGatewayConfig()->getMasterCardApprovedOrderStatus();
            if (!$this->statusExists($orderStatus)) {
                $orderStatus = $order->getConfig()->getStateDefaultStatus($orderState);
            }

            $emailCustomer = $this->getGatewayConfig()->isEmailCustomer();

            $order->setState($orderState)
                ->setStatus($orderStatus)
                ->addStatusHistoryComment("MasterCard capture success. Transaction #$transactionId")
                ->setIsCustomerNotified($emailCustomer);

            $order->save();

            $invoiceAutomatically = $this->getGatewayConfig()->isAutomaticInvoice();
            if ($invoiceAutomatically) {
                $this->invoiceOrder($order, $transactionId);
            }
            
            $this->getMessageManager()->addSuccessMessage(__("Your payment with MasterCard is complete"));
            $this->_redirect('checkout/onepage/success', array('_secure'=> false));
        } else {
            $this->getCheckoutHelper()->cancelCurrentOrder("Order #".($order->getId())." was rejected by mastercard. Transaction #$transactionId.");
            $this->getCheckoutHelper()->restoreQuote();
            $this->getMessageManager()->addErrorMessage(__("There was an error in the MasterCard payment: ".$this->getRequest()->getParam('vpc_Message')));
            $this->_redirect('checkout/cart', array('_secure'=> false));
        }

    }

    private function statusExists($orderStatus)
    {
        $statuses = $this->getObjectManager()
            ->get('Magento\Sales\Model\Order\Status')
            ->getResourceCollection()
            ->getData();
        foreach ($statuses as $status) {
            if ($orderStatus === $status["status"]) return true;
        }
        return false;
    }

    private function invoiceOrder($order, $transactionId)
    {
        if(!$order->canInvoice()){
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Cannot create an invoice.')
                );
        }
        
        $invoice = $this->getObjectManager()
            ->create('Magento\Sales\Model\Service\InvoiceService')
            ->prepareInvoice($order);
        
        if (!$invoice->getTotalQty()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
        }
        
        /*
         * Look Magento/Sales/Model/Order/Invoice.register() for CAPTURE_OFFLINE explanation.
         * Basically, if !config/can_capture and config/is_gateway and CAPTURE_OFFLINE and 
         * Payment.IsTransactionPending => pay (Invoice.STATE = STATE_PAID...)
         */
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
        $invoice->register();

        $transaction = $this->getObjectManager()->create('Magento\Framework\DB\Transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());
        $transaction->save();
    }

}
