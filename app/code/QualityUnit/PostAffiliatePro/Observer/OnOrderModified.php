<?php
namespace QualityUnit\PostAffiliatePro\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class OnOrderModified implements ObserverInterface {
    /**
     * our configs
     *
     * @var \QualityUnit\PostAffiliatePro\Helper\Data
     */
    protected $_config = null;

    /**
     * @param \QualityUnit\PostAffiliatePro\Helper\Data $config
     */
    public function __construct(\QualityUnit\PostAffiliatePro\Helper\Data $config) {
        $this->_config = $config;
    }

    /**
     * Change commission based on a new status
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer) {
        $order = $observer->getData('order');
        $newStatus = $observer->getData('state');

        if (!$this->_config->isConfigured()) {
            return false;
        }

        $this->_config->setOrderStatus($order, $newStatus);
        return true;
    }
}
