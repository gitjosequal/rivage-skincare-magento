<?php
namespace QualityUnit\PostAffiliatePro\Block;

class Tracking extends \Magento\Framework\View\Element\Template {
    /**
     * order IDs
     *
     * @var array()
     */
    private $_orderIds;

    /**
     * Post Affiliate Pro config
     *
     * @var \QualityUnit\PostAffiliatePro\Helper\Data
     */
    protected $_config = null;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_salesOrderCollection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
     * @param \QualityUnit\PostAffiliatePro\Helper\Data $config
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
        \QualityUnit\PostAffiliatePro\Helper\Data $config,
        array $data = []
    ) {
        $this->_config = $config;
        $this->_salesOrderCollection = $salesOrderCollection;
        parent::__construct($context, $data);
    }

    public function isCheckoutSuccess() {
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return false;
        }
        $this->_orderIds = $orderIds;
        return true;
    }

    public function getTrackerHeader() {
        if (!$this->_config->isConfigured(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return '';
        }
        else {
            $url = $this->_config->getInstallationPath();
            $tracker = 'trackjs.js';
            if ($this->_config->getHashedScript() != '') {
                $tracker = $this->_config->getHashedScript();
            }
            return '<script type="text/javascript" id="pap_x2s6df8d" src="//'.$url.'/scripts/'.$tracker.'">
</script>';
        }
    }

    public function getClickTrackingCode() {
        if (!$this->_config->isConfigured()) { // \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            return '<!-- Post Affiliate Pro plugin has not been configured yet! -->';
        }

        if (!$this->_config->isClickTrackingEnabled()) {
            return;
        }

        if ($this->_config->getTrackingMethod() != 'javascript') {
            return $this->trackApiClick();
        }

        $campaign = '';
        if ($this->_config->getCampaignID() != '') {
            $campaign = "\nvar CampaignID = '".$this->_config->getCampaignID()."';";
        }

        $result = $this->getTrackerHeader();
        $accountID = $this->_config->getAPICredential('account');
        return $result.'<script type="text/javascript">
  PostAffTracker.setAccountId(\''.$accountID.'\');'.$campaign.'
  try {
    PostAffTracker.track();
  } catch (err) { }
</script>';

    }

    private function safeString($str) {
        return str_replace("'", "\'", $str);
    }

    public function getSaleTrackingCode() {
        if (empty($this->_orderIds)) {
            return '<!-- Post Affiliate Pro plugin error: No order IDs found??? -->';
        }

        if (!$this->_config->isConfigured()) { // \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            return '<!-- Post Affiliate Pro plugin has not been configured yet! -->';
        }
        try { // make sure the connection will work
            $session = $this->_config->getSession();
        } catch (\Exception $e) {
            return '<!-- Post Affiliate Pro plugin has not been configured properly! -->';
        }

        if ($this->_config->getTrackingMethod() != 'javascript') {
            return '<!-- Post Affiliate Pro plugin: JavaScript tracking not allowed -->';
        }

        $header = $this->getTrackerHeader();

        $accountID = $this->_config->getAPICredential('account');
        $collection = $this->_salesOrderCollection->create();
        $collection->addFieldToFilter('entity_id', ['in' => $this->_orderIds]);

        $sale_tracker = "PostAffTracker.setAccountId('$accountID');\n";
        foreach ($collection as $order) {
            $items = $this->_config->getOrderSaleDetails($order);

            $arraySize = count($items)-1;
            foreach ($items as $i => $item) {
                $sale_tracker .= "var sale$i = PostAffTracker.createSale();\n";
                $sale_tracker .= "sale$i.setTotalCost('".$item['totalcost']."');\n
                    sale$i.setOrderID('".$item['orderid']."($i)');\n
                    sale$i.setProductID('".$this->safeString($item['productid'])."');\n
                    sale$i.setStatus('".$item['status']."');\n
                    sale$i.setCurrency('".$item['currency']."');\n";

                if (!empty($item['data1'])) $sale_tracker .= "sale$i.setData1('".$this->safeString($item['data1'])."');\n";
                if (!empty($item['data2'])) $sale_tracker .= "sale$i.setData2('".$this->safeString($item['data2'])."');\n";
                if (!empty($item['data3'])) $sale_tracker .= "sale$i.setData3('".$this->safeString($item['data3'])."');\n";
                if (!empty($item['data4'])) $sale_tracker .= "sale$i.setData4('".$this->safeString($item['data4'])."');\n";
                if (!empty($item['data5'])) $sale_tracker .= "sale$i.setData5('".$this->safeString($item['data5'])."');\n";

                if ($this->_config->getCouponTrack()) $sale_tracker .= "sale$i.setCoupon('".$item['couponcode']."');\n";
                if (isset($item['campaignid'])) $sale_tracker .= "sale$i.setCampaignID('".$item['campaignid']."');\n";

                if ($i != $arraySize) { // delete cookie after sale fix
                    $sale_tracker .= "if (typeof sale$i.doNotDeleteCookies === 'function') {sale$i.doNotDeleteCookies();}
                        PostAffTracker.register();\n";
                } else {
                    $sale_tracker .= "if (typeof PostAffTracker.registerOnAllFinished === 'function') {
                        PostAffTracker.registerOnAllFinished();
                    } else {
                        PostAffTracker.register();
                    }";
                }
            }
        }

        return $header.'<script type="text/javascript">'.$sale_tracker.'</script>';
    }

    private function trackApiClick() {
        $visitorId = '';
        if (isset($_COOKIE['PAPVisitorId'])) {
            $visitorId = $_COOKIE['PAPVisitorId'];
        }
        $referrer = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referrer = $_SERVER['HTTP_REFERER'];
        }

        $query = 'visitorId='.$visitorId.'&accountId='.$this->_config->getAPICredential('account').'&tracking=1&url=H_'.urlencode($_SERVER['SERVER_NAME'].'/'.$_SERVER['PHP_SELF'])
            .'&referrer='.urlencode($referrer).'&getParams='.urlencode($_SERVER['QUERY_STRING']).'&isInIframe=false&cookies=';
        $ip = $this->_config->getRemoteIp();
        if (!empty($ip)) {
            $query .= '&ip='.$ip;
        }

        try {
            $response = $this->_config->connectExternal($this->_config->getInstallationPath().'/scripts/track.php', $query, \Zend_Http_Client::GET);
            return $this->getTrackerHeader().'<script type="text/javascript">'.$response.'</script>';
        } catch (\Exception $e) {
            $this->_config->log('Error registering click: '.$e->getMessage());
            return false;
        }
    }

    protected function _toHtml() {
        if (!$this->_config->isConfigured(\Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return '<!-- Post Affiliate Pro plugin has not been configured yet! -->';
        }

        return parent::_toHtml();
    }
}