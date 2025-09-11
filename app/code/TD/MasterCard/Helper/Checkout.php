<?php
/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */

namespace TD\MasterCard\Helper;
use Magento\Sales\Model\Order;
use Magento\Checkout\Model\Session;

/**
 * Checkout workflow helper
 *
 * Class Checkout
 * @package TD\MasterCard\Helper
 */
class Checkout
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;

    /**
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        Session $session
    )
    {
        $this->session = $session;
    }

    /**
     * Cancel last placed order with specified comment message
     *
     * @param string $comment Comment appended to order history
     * @return bool True if order cancelled, false otherwise
     */
    public function cancelCurrentOrder($comment)
    {
        $order = $this->session->getLastRealOrder();
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }

    /**
     * Restores quote (restores cart)
     *
     * @return bool
     */
    public function restoreQuote()
    {
        return $this->session->restoreQuote();
    }
}
