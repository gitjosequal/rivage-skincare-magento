<?php
namespace QualityUnit\PostAffiliatePro\Plugin;

class Order {
    /**
     * @var EventManager
     */
    private $_eventManager;

    /**
     * @param \Magento\Framework\Event\Manager $eventManager
     */
    public function __construct(\Magento\Framework\Event\Manager $eventManager) {
        $this->_eventManager = $eventManager;
    }

    /**
     * Set order state
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    public function afterSetState(\Magento\Sales\Model\Order $order, $result) {
        $state = $order->getData($order::STATE);
        $this->_eventManager->dispatch('order_status_changed_after', ['order' => $result, 'state' => $state]);

        return $result;
    }
}