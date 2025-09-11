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
class Cancel extends AbstractAction {
    
    public function execute() {
        $orderId = $this->getRequest()->get('orderId');
        $order =  $this->getOrderById($orderId);

        if ($order && $order->getId()) {
            $this->getLogger()->debug('Requested order cancellation by customer. OrderId: ' . $order->getIncrementId());
            $this->getCheckoutHelper()->cancelCurrentOrder("MasterCard: ".($order->getId())." was cancelled by the customer.");
            $this->getCheckoutHelper()->restoreQuote();
            $this->getMessageManager()->addWarningMessage(__("You have successfully canceled your MasterCard payment. Please click on 'Update Shopping Cart'."));
        }
        $this->_redirect('checkout/cart');
    }

}
