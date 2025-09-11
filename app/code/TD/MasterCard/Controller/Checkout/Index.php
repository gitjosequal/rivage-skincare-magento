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
class Index extends AbstractAction
{

    private function getPayload($order)
    {
        if ($order == null) {
            $this->getLogger()->debug('Unable to get order from last lodged order id. Possibly related to a failed database call');
            $this->_redirect('checkout/onepage/error', array('_secure' => false));
        }

        $data['vpc_Version'] = $this->getDataHelper()->getVpcVersion();
        $data['vpc_Command'] = $this->getDataHelper()->getCommandPay();
        $data['vpc_AccessCode'] = substr($this->getGatewayConfig()->getAccessCode(), 0, 8);
        $data['vpc_MerchTxnRef'] = substr($order->getIncrementId(), 0, 40);
        $data['vpc_Merchant'] = substr($this->getGatewayConfig()->getMerchantId(), 0, 16);
        $data['vpc_OrderInfo'] = substr('Order ' . $order->getIncrementId(), 0, 34);
        $data['vpc_Amount'] = $this->getDataHelper()->getGrandTotal($order);
        $data['vpc_Locale'] = substr($this->getDataHelper()->getLocaleResolver()->getLocale(), 0, 2);
        $data['vpc_ReturnURL'] = $this->getDataHelper()->getCompleteUrl();
        $securesecret = $this->getGatewayConfig()->getSecureSecret();
        $data['vpc_SecureHash'] = $this->getCryptoHelper()->getHash($data, $securesecret);
        $data['vpc_SecureHashType'] = 'SHA256';
        return $data;
    }

    private function postToCheckout($checkoutUrl, $payload)
    {
        echo
        "<html>
            <body>
            <form id='form' action='$checkoutUrl' method='post'>";
        foreach ($payload as $key => $value) {
            echo "<input type='hidden' id='$key' name='$key' value='" . htmlspecialchars($value, ENT_QUOTES) . "'/>";
        }
        echo
        '</form>
            </body>';
        echo
        '<script>
                var form = document.getElementById("form");
                form.submit();
            </script>
        </html>';
    }

    /**
     *
     *
     * @return void
     */
    public function execute()
    {
        try {
            $order = $this->getOrder();
            if ($order->getState() === Order::STATE_PENDING_PAYMENT) {
                $payload = $this->getPayload($order);
                $this->postToCheckout($this->getDataHelper()->getVpcUrl(), $payload);
            } else if ($order->getState() === Order::STATE_CANCELED) {
                $errorMessage = $this->getCheckoutSession()->getMasterCardErrorMessage();
                if ($errorMessage) {
                    $this->getMessageManager()->addWarningMessage($errorMessage);
                    $errorMessage = $this->getCheckoutSession()->unsMasterCardErrorMessage();
                }
                $this->getCheckoutHelper()->restoreQuote();
                $this->_redirect('checkout/cart');
            } else {
                $this->getLogger()->debug('Order in unrecognized state: ' . $order->getState());
                $this->_redirect('checkout/cart');
            }
        } catch (Exception $ex) {
            $this->getLogger()->debug('An exception was encountered in mastercard/checkout/index: ' . $ex->getMessage());
            $this->getLogger()->debug($ex->getTraceAsString());
            $this->getMessageManager()->addErrorMessage(__('Unable to start MasterCard Checkout.'));
        }
    }
}