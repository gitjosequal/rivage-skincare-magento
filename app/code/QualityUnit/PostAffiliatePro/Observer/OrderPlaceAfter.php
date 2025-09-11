<?php
namespace QualityUnit\PostAffiliatePro\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class OrderPlaceAfter implements ObserverInterface {
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
     * Create a commission for manually added order
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer) {
        if (!$this->_config->isConfigured()) {
            return false;
        }
        $order = $observer->getEvent()->getOrder();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state =  $objectManager->get('Magento\Framework\App\State');
        $areaCode = $state->getAreaCode();
        if ($areaCode == 'adminhtml') {
            // the order was created from the admin panel
            $this->_config->registerOrder($order);
    	}
    }

}
