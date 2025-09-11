<?php
namespace QualityUnit\PostAffiliatePro\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class OnOrderSuccess implements ObserverInterface {
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_salesOrderCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
     protected $_customerSession;

    /**
     * our configs
     *
     * @var \QualityUnit\PostAffiliatePro\Helper\Data
     */
    protected $_config = null;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
     * @param \QualityUnit\PostAffiliatePro\Helper\Data $config
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
        \QualityUnit\PostAffiliatePro\Helper\Data $config,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_config = $config;
        $this->_layout = $layout;
        $this->_salesOrderCollection = $salesOrderCollection;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
    }

    /**
     * Set order details to block of success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer) {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        if (!$this->_config->isConfigured()) {
            return false;
        }

        $collection = $this->_salesOrderCollection->create();
        $collection->addFieldToFilter('entity_id', ['in' => $orderIds]);
        if ($this->_config->getTrackingMethod() != 'javascript') {
            // track with API
            $ip = $this->_config->getRemoteIp();
            foreach ($collection as $order) {
                $cookies = '';
                if (isset($_COOKIE['PAPVisitorId'])) {
                    $cookies = $_COOKIE['PAPVisitorId'];
                }
                $this->_config->registerOrder($order, $cookies, $ip);
            }
        }
        else {
            // track with JavaScript
            $block = $this->_layout->getBlock('pap_tracking');
            if ($block) {
                $block->setOrderIds($orderIds);
            }
        }

        if  ($this->_config->isCreateAffiliateEnabled()) {
            foreach ($collection as $order) {
                $this->_config->createAffiliate($order, $this->_customerSession);
                break; // we only need this once
            }
        }
    }
}
