<?php
namespace QualityUnit\PostAffiliatePro\Helper;
use Magento\Store\Model\Store;
use Magento\Sales\Model\Order;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    private $url;
    private $username;
    private $password;
    private $accountid;

    protected $_curlFactory;
    protected $papSession;
    protected $_logger;
    protected $_request;

    public $declined = 'D';
    public $pending = 'P';
    public $approved = 'A';

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
     */
    public function __construct(
            \Magento\Framework\App\Helper\Context $context,
            \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
            \Magento\Framework\HTTP\PhpEnvironment\Request $request) {
        parent::__construct($context);
        $this->_logger = $context->getLogger();
        $this->_curlFactory = $curlFactory;
        $this->url = $this->scopeConfig->getValue('postaffiliatepro/api/url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->username = $this->scopeConfig->getValue('postaffiliatepro/api/username', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->password = $this->scopeConfig->getValue('postaffiliatepro/api/password', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->accountid = $this->scopeConfig->getValue('postaffiliatepro/api/accountid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_request = $request;
    }

	public function log($msg) { // log location: var/log/system.log
        if ($this->_logger !== null && $this->_logger !== '') {
            $this->_logger->debug('PostAffiliatePro: '.$msg);
        }
    }

    public function isConfigured() {
        if (($this->password != '') && ($this->username != '') && ($this->url != '')) {
            return true;
        }
        $this->log('The module has not been configured yet.');
        return false;
    }

    public function isCreateAffiliateEnabled() {
        if ($this->scopeConfig->getValue('postaffiliatepro/affiliate/createaff', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return true;
        }
        return false;
    }

    public function getCreateAffiliateProducts() {
        $products = $this->scopeConfig->getValue('postaffiliatepro/affiliate/createaffproducts', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($products == '' || $products == null) {
            return array();
        }
        if (strpos($products, ',') !== false) {
            $products = str_replace(array(', ', ' ,'), ',', $products);
        }
        return explode(',',$products);
    }

    public function getAPICredential($name) {
        switch ($name) {
            case 'username':
                return $this->username;
                break;
            case 'pass':
                return $this->password;
                break;
            case 'account':
                return $this->accountid;
                break;
        }
        return null;
    }

    public function getInstallationPath() {
        if (!$this->url) {
            $this->log('The installation URL has not been configured yet.');
            return '';
        }

        return self::getDomainOnly($this->url);
    }

    public static function getDomainOnly($url) {
        $url = str_replace('https://', '', $url);
        $url = str_replace('http://', '', $url);
        if (substr($url,-1) == '/') $url = substr($url,0,-1);

        return $url;
    }

    public function getAPIPath() {
        return 'http://'.$this->getInstallationPath().'/scripts/server.php';
    }

    public function getTrackingMethod() {
        return $this->scopeConfig->getValue('postaffiliatepro/tracking/trackingmethod', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getData($n) {
        return $this->scopeConfig->getValue('postaffiliatepro/tracking/data'.$n, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getHashedScript() {
        return $this->scopeConfig->getValue('postaffiliatepro/tracking/hash', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getCampaignID() {
        return $this->scopeConfig->getValue('postaffiliatepro/tracking/trackforcampaign', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getPerProduct() {
        if ($this->scopeConfig->getValue('postaffiliatepro/tracking/perproduct', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return true;
        }
        return false;
    }

    public function getPerProductOptions() {
        return $this->scopeConfig->getValue('postaffiliatepro/tracking/productoptions', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAutoStatusChange() {
        if ($this->scopeConfig->getValue('postaffiliatepro/tracking/autostatuschange', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return true;
        }
        return false;
    }

    public function getCouponTrack() {
        if ($this->scopeConfig->getValue('postaffiliatepro/tracking/coupontrack', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return true;
        }
        return false;
    }

    public function isClickTrackingEnabled() {
        if ($this->scopeConfig->getValue('postaffiliatepro/tracking/trackclicks', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return true;
        }
        return false;
    }

    public function getSession() {
        if (($this->papSession != '') && ($this->papSession != null)) {
            return $this->papSession;
        }

        $query = 'D='.urlencode('{"C":"Gpf_Api_AuthService","M":"authenticate","fields":[["name","value","values","error"],["username","'
            .$this->getAPICredential('username').'",null,""],["password","'.$this->getAPICredential('pass').'",null,""],["roleType","M",null,""],["isFromApi","Y",null,""],["apiVersion","",null,""]]}');
        try {
            $response = $this->connectExternal($this->getAPIPath(), $query);
            $response = json_decode($response);
        } catch (\Exception $e) {
            throw $e;
            return false;
        }

        if (!isset($response->success) || $response->success != 'Y') {
            if (isset($response->message)) {
                throw new \Exception('Connection problem at '.$this->getAPIPath().': '.$response->message);
            }
            throw new \Exception('Error connecting to '.$this->getAPIPath());
            return false;
        }

        $session = '';
        foreach ($response->fields as $field) {
            if ($field[0] === 'S') {
                $session = $field[1];
                break;
            }
        }
        if (empty($session)) {
            return false;
        }

        $this->papSession = $session;
        return $this->papSession;
    }

    public function createAffiliate($order, $customerSession) {
        if (!$this->isCreateAffiliateEnabled()) {
            $this->log('Affiliate creation is not enabled.');
            return false;
        }

        $products = $this->getCreateAffiliateProducts();
        if (sizeof($products) > 0) {
            // conditional only
            $items = $order->getAllVisibleItems();
            $search = false;
            foreach ($items as $i => $item) {
                if (in_array($item->getProductId(), $products)) {
                    $search = true;
                    break; // end of search, we have it
                }
            }
            if (!$search) {
                return false;
            }
        }

        // create affiliate
        $customer = $customerSession->getData();
        $this->log('Starting affiliate creation...');
        try {
            $session = $this->getSession();
        } catch (\Exception $e) {
            return false;
        }

        $query = 'D=' . urlencode('{"C":"Gpf_Rpc_Server", "M":"run", "requests":[{"C":"Pap_Signup_AffiliateForm", "M":"add",' . '"fields":[["name","value"],["Id",""],["username","' . $order->getCustomerEmail() . '"],["firstname","' . $order->getCustomerFirstname() . '"],["lastname","' . $order->getCustomerLastname() . '"],["agreeWithTerms","Y"],');

        if (isset($_COOKIE['PAPVisitorId'])) {
            $query .= '["visitorId","' . $_COOKIE['PAPVisitorId'] . '"],';
        }

        $address = $customer->getPrimaryAddress('default_billing');
        if (!empty($address)) {
            $addressArray = $address->getData();
            $query .= urlencode('["data3","' . $addressArray['street'] . '"],["data4","' . $addressArray['city'] . '"],' . '["data5","' . $addressArray['region'] . '"],["data6","' . $addressArray['country_id'] . '"],' . '["data7","' . $addressArray['postcode'] . '"],["data8","' . $addressArray['telephone'] . '"]');
        }

        $query .= urlencode(']}], "S":"' . $session . '"}');
        try {
            $response = $this->connectExternal($this->getAPIPath(), $query);
            $response = json_decode($response);
        } catch (\Exception $e) {
            throw $e;
            return false;
        }
    }

    private function getStatus($state) {
        if ($state === \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT ||
        		$state === \Magento\Sales\Model\Order::STATE_NEW ||
        		$state === \Magento\Sales\Model\Order::STATE_PROCESSING ||
        		$state === \Magento\Sales\Model\Order::STATE_HOLDED) {
            return $this->pending;
        }
        if ($state === \Magento\Sales\Model\Order::STATE_COMPLETE) {
            return $this->approved;
        }
        return $this->declined;
    }

    public function setOrderStatus($order, $newStatus) {
        if (!$this->getAutoStatusChange()) {
            $this->log('Changing status of commissions is not enabled.');
            return;
        }
        $this->log('Changing status of order '.$order->getIncrementId()." after '$newStatus' received");
        try {
            $session = $this->getSession();
        } catch (\Exception $e) {
            return false;
        }
        $status = '';

        if (empty($session)) {
            $this->log('The module has not been configured yet.');
            return;
        }

        $refunded = array();
        switch ($newStatus) {
            case 'holded':
            case 'pending':
                $status = $this->pending;
                $loadingStatus = 'A';
                break;
            case 'canceled':
                $status = $this->declined;
                $loadingStatus = 'A,P';
                break;
            case 'closed':
                $status = 'refund';
                break;
            case 'complete':
                if ($order->getBaseTotalPaid() > 0) { // was paid
                    if ($order->getBaseTotalRefunded() > 0) { // partial refund handling
                        $refunded = $this->getRefundedItemIDs($order);
                    }
                    $status = $this->approved;
                    $loadingStatus = 'P';
                }
                else { // completed but not paid
                    $status = $this->pending;
                    $this->log('Despite the status is "complete" there was nothing paid yet.');
                    return;
                }
                break;
        }

        if ($status === 'refund') {
            $this->refundCommissions($order, $session);
            return;
        }
        if ($status == '') {
            // if we are here, it's probably a partial refund
            if ($order->getBaseTotalRefunded() > 0 || $order->getBaseTotalCanceled() > 0) {
                $refunded = $this->getRefundedItemIDs($order);
                $status = $this->declined;
                $loadingStatus = 'A,P';
            }
        }

        if ($status == '' && empty($refunded)) {
            return false;
        }

        $query = 'D='.urlencode('{"C":"Gpf_Rpc_Server","M":"run","requests":[{"C":"Pap_Merchants_Transaction_TransactionsGrid","M":"getRows",'
            .'"sort_col":"dateinserted","sort_asc":false,"offset":0,"limit":900,"filters":[["t_orderid","L","'.$order->getIncrementId()
            .'("],["rstatus","IN","'.$loadingStatus.'"]],"columns":[["id"],["id"],["commission"],["totalcost"],["t_orderid"],["productid"],["dateinserted"],["name"],["rtype"],["tier"],'
            .'["commissionTypeName"],["rstatus"],["payoutstatus"],["firstname"],["lastname"],["userid"],["channel"],["actions"]]}], "S":"'.$session.'"}');
        try {
            $response = $this->connectExternal($this->getAPIPath(), $query);
            $response = json_decode($response);
        } catch (\Exception $e) {
            $this->log('An API error while searching for the order with postfix: '.$e->getMessage());
            return false;
        }

        $ids = array();
        $refundIDs = array();
        $approveIDs = array();
        $i = 1;
        foreach($response[0]->rows as $record) {
            // ["id","userid","commission","totalcost","t_orderid","productid","dateinserted","name","rtype","commissionTypeName","tier","firstname","lastname","rstatus","payoutstatus","channel"]
            if ($i === 1) {
                $i++;
                continue;
            }
            if (count($refunded)) {
                if ($status === 'A') {
                    if (in_array($record[5], $refunded)) {
                        $refundIDs[] = $record[0];
                    }
                    else {
                        $approveIDs[] = $record[0];
                    }
                    continue;
                }
                elseif ($status == 'D') {
                    if (in_array($record[5], $refunded)) {
                        $refundIDs[] = $record[0];
                    }
                    continue;
                }
            }
            $ids[] = $record[0];
        }

        if (count($refundIDs) == 0 && count($approveIDs) == 0 && count($ids) == 0) {
            $items = $order->getAllVisibleItems();
            foreach ($items as $i => $item) {
                $product = $item->getProduct();
                
                if (empty($product)) continue;

                switch ($this->getPerProductOptions()) {
                    case '1': $trackingProductID = $product->getId(); break;
                    case '2': $trackingProductID = $product->getSku(); break;
                    case '3': $trackingProductID = $product->getCategoryId(); break;
                    case '4': $trackingProductID = $order->getCustomerGroupId(); break;
                }

                if ($status == $this->approved) {
                    if (count($refunded) && in_array($trackingProductID, $refunded)) { // if we are refunding only specific order items
                        $this->changeStatusByOrderId($session, $order->getIncrementId()."($i)", 'D');
                        continue;
                    }
                    $this->changeStatusByOrderId($session, $order->getIncrementId()."($i)", 'A');
                }
                if ($status == $this->declined) {
                    if (count($refunded) && !in_array($trackingProductID, $refunded)) { // if we are refunding only specific order items
                        continue;
                    }
                    $this->changeStatusByOrderId($session, $order->getIncrementId()."($i)", 'D');
                }
            }
            $this->log('Status (of unprocessed commissions) has been changed.');
            return;
        }

        try {
            if (!empty($refundIDs)) {
                $query = $this->getJSONRequestChangeStatus('D', $refundIDs, $session);
                try {
                    $response = $this->connectExternal($this->getAPIPath(), $query);
                    $response = json_decode($response);
                } catch (\Exception $e) {
                    $this->log('Error occurred when changing status: '.$e->getMessage());
                    //return false;
                }
            }
            if (!empty($approveIDs)) {
                $query = $this->getJSONRequestChangeStatus('A', $approveIDs, $session);
                try {
                    $response = $this->connectExternal($this->getAPIPath(), $query);
                    $response = json_decode($response);
                } catch (\Exception $e) {
                    $this->log('Error occurred when changing status: '.$e->getMessage());
                    //return false;
                }
            }

            $query = $this->getJSONRequestChangeStatus($status, $ids, $session);
            try {
                $response = $this->connectExternal($this->getAPIPath(), $query);
                $response = json_decode($response);
            } catch (\Exception $e) {
                $this->log('Error occurred when changing status: '.$e->getMessage());
                return false;
            }

            $this->log('Status has been changed.');
            return true;
        }
        catch (\Exception $e) {
            $this->log('An API error while status changing: '.$e->getMessage());
            return false;
        }
    }

    private function refundCommissions($order, $session) {
        $this->log('Starting refund... searching for commission '.$order->getIncrementId());

        $query = 'D=' . urlencode('{"C":"Gpf_Rpc_Server","M":"run","requests":[{"C":"Pap_Merchants_Transaction_TransactionsGrid","M":"getRows",'
                . '"sort_col":"dateinserted","sort_asc":false,"offset":0,"limit":900,"filters":[["t_orderid","L","' . $order->getIncrementId()
                . '("]],"columns":[["id"],["id"],["commission"],["totalcost"],["t_orderid"],["productid"],["dateinserted"],["name"],["rtype"],'
                . '["tier"],["commissionTypeName"],["rstatus"],["payoutstatus"],["firstname"],["lastname"],["userid"],["channel"],["actions"]]}],'
                . '"S":"' . $session . '"}');

        try {
            $response = $this->connectExternal($this->getAPIPath(), $query);
            $response = json_decode($response);
        } catch (\Exception $e) {
            $this->log('An API error while searching for order to refund: '.$e->getMessage());
            return false;
        }

        $refundIDs = array();
        $i = 1;
        foreach ($response[0]->rows as $record) {
            // ["id","userid","commission","totalcost","t_orderid","productid","dateinserted","name","rtype","commissionTypeName","tier","firstname","lastname","rstatus","payoutstatus","channel"]
            if ($i == 1) { // skip header
                $i++;
                continue;
            }
            $refundIDs[] = $record[0];
        }

        if (empty($refundIDs)) {
            $this->log('There is nothing to refund!');
            return true;
        }

        $query = 'D=' . urlencode('{"C":"Gpf_Rpc_Server", "M":"run", "requests":[{"C":"Pap_Merchants_Transaction_TransactionsForm",' .
                '"M":"makeRefundChargeback", "status":"R", "merchant_note":"refunded from Magento API", "refund_multitier":"Y",' .
                '"ids":["' . implode('","', $refundIDs) . '"]}], "S":"' . $session . '"}');
        try {
            $response = $this->connectExternal($this->getAPIPath(), $query);
            $response = json_decode($response);
        } catch (\Exception $e) {
            $this->log('An API error while refunding order(s) '.implode('","', $refundIDs).': '.$e->getMessage());
            return false;
        }

        if (!isset($response[0]->success) || $response[0]->success != 'Y') {
            $err = '';
            if (isset($response[0]->message)) {
                $err = ': ' . $response[0]->message;
            }
            $this->log('An error occurred while refunding' . $err);
            return false;
        }
        $this->log('Refund successful');
        return true;
    }

    private function changeStatusByOrderId($session, $orderid, $status) {
        $query = $this->getJSONRequestChangeStatus($status, $orderid, $session, true);

        try {
            json_decode($this->connectExternal($this->getAPIPath(), $query));
        } catch (\Exception $e) {
            $this->log('Error occurred when changing status: '.$e->getMessage());
            return false;
        }
    }

    public function registerOrder($order, $visitorID = '', $ip = '') {
        $this->log('Registering order '.$order->getIncrementId().'.');
        $items = $this->getOrderSaleDetails($order);
        $this->registerSaleDetails($items, $visitorID, $ip);
    }

    public function registerSaleDetails($items, $visitorID = '', $ip = '') {
        $arraySize = count($items)-1;
        foreach ($items as $i => $item) {
            $sale = '[{"ac":"","t":"'.$item['totalcost'].'","o":"'.$item['orderid']."($i)".'","cr":"'.$item['currency'].'","p":"'.$this->safeString($item['productid'])
                .'","s":"'.$item['status'].'"';
            if ($item['couponcode']) $sale .= ',"cp":"'.$item['couponcode'].'"';
            if ($item['data1']) $sale .= ',"d1":"'.$this->safeString($item['data1']).'"';
            if ($item['data2']) $sale .= ',"d2":"'.$this->safeString($item['data2']).'"';
            if ($item['data3']) $sale .= ',"d3":"'.$this->safeString($item['data3']).'"';
            if ($item['data4']) $sale .= ',"d4":"'.$this->safeString($item['data4']).'"';
            if ($item['data5']) $sale .= ',"d5":"'.$this->safeString($item['data5']).'"';
            if ($item['campaignid']) $sale .= ',"c":"'.$this->safeString($item['campaignid']).'"';

            if ($i != $arraySize) { // delete cookie after sale fix
                $sale .= ',"dndc":"Y"}]';
            } else {
                $sale .= '}]';
            }

            $query = 'visitorId='.$visitorID.'&accountId='.$this->getAPICredential('account').'&tracking=1&url=H_'.urlencode($this->getServerValue('SERVER_NAME').'/'.$this->getServerValue('PHP_SELF'))
                .'&referrer='.urlencode($this->getServerValue('HTTP_REFERER')).'&getParams='.urlencode($this->getServerValue('QUERY_STRING')).'&isInIframe=false'
                .'&sale='.urlencode($sale).'&cookies=';
            if (!empty($ip)) {
                $query .= '&ip='.$ip;
            }

            try {
                $response = $this->connectExternal($this->getInstallationPath().'/scripts/track.php', $query, \Zend_Http_Client::GET);
                $response = json_decode($response);
            } catch (\Exception $e) {
                $this->log('Error registering sale: '.$e->getMessage());
                return false;
            }
        }
    }

    private function getServerValue(string $name) {
        return $this->_request->getServerValue($name);
    }

    public function getRemoteIp() {
        $serverForwarder = $this->getServerValue('HTTP_X_FORWARDED_FOR');
        if (isset($serverForwarder) && !empty($serverForwarder)) {
            $ip = $serverForwarder;
            $ipAddresses = explode(',', $ip);
            foreach ($ipAddresses as $ipAddress) {
                $ipAddress = trim($ipAddress);
                if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ipAddress;
                }
            }
        }
        $serverRemote = $this->getServerValue('REMOTE_ADDR');
        if (isset($serverRemote) && !empty($serverRemote)) {
            return $serverRemote;
        }
        return '';
    }

    private function getRefundedItemIDs($order) {
        $refunded = array();
        $items = $order->getAllVisibleItems();

        foreach($items as $i=>$item) {
            if ($item->getStatus() === 'Refunded') {
                $product = $item->getProduct();
                
                if (empty($product)) continue;

                switch ($this->getPerProductOptions()) {
                    case '1': $trackingProductID = $product->getId(); break;
                    case '2': $trackingProductID = $product->getSku(); break;
                    case '3': $trackingProductID = $product->getCategoryId(); break;
                    case '4': $trackingProductID = $order->getCustomerGroupId(); break;
                }
                $refunded[$i] = $trackingProductID;
            }
        }
        return $refunded;
    }

    public function getOrderSaleDetails($order) {
        $sales = array();
        $status = $this->getStatus($order->getState());

        if ($this->getPerProduct()) { // per product tracking
            $items = $order->getAllVisibleItems();

            foreach($items as $i=>$item) {
                $product = $item->getProduct();
                
                if (empty($product)) continue;

                $sales[$i] = array();
                $subtotal = ($item->getBaseRowTotal() == '') ? $item->getBasePrice() : $item->getBaseRowTotal();
                $discount = abs($item->getBaseDiscountAmount());

                switch ($this->getPerProductOptions()) {
                    case '1': $trackingProductID = $product->getId(); break;
                    case '2': $trackingProductID = $product->getSku(); break;
                    case '3': $trackingProductID = $product->getCategoryId(); break;
                    case '4': $trackingProductID = $order->getCustomerGroupId(); break;
                }

                $sales[$i]['totalcost'] = $subtotal - $discount;
                $sales[$i]['orderid'] = $order->getIncrementId();
                $sales[$i]['productid'] = $trackingProductID;
                $sales[$i]['couponcode'] = ($this->getCouponTrack())?$order->getCouponCode():'';
                $sales[$i]['status'] = $status;
                $sales[$i]['currency'] = $order->getBaseCurrencyCode();
                $sales[$i]['campaignid'] = $this->getCampaignID();

                for ($n = 1; $n < 6; $n++) {
                    if ($this->getData($n)) {
                        $sales[$i]['data'.$n] = $this->changeExtraData($this->getData($n), $order, $item, $product);
                    }
                }
            }
        }
        else { // per order tracking
            $sales[0] = array();

            $subtotal = $order->getBaseSubtotal();
            $discount = abs($order->getBaseDiscountAmount());

            $trackingProductID = null;
            if ($this->getPerProductOptions() === '4') {
                $trackingProductID = $order->getCustomerGroupId();
            }

            $sales[0]['totalcost'] = $subtotal - $discount;
            $sales[0]['orderid'] = $order->getIncrementId();
            $sales[0]['productid'] = $trackingProductID;
            $sales[0]['couponcode'] = ($this->getCouponTrack())?$order->getCouponCode():'';
            $sales[0]['status'] = $status;
            $sales[0]['currency'] = $order->getBaseCurrencyCode();
            $sales[0]['campaignid'] = $this->getCampaignID();

            for ($n = 1; $n < 6; $n++) {
                if ($this->getData($n)) {
                    $sales[0]['data'.$n] = $this->changeExtraData($this->getData($n), $order);
                }
            }
        }

        return $sales;
    }

    private function getJSONRequestChangeStatus($status, $ids, $session, $perOrderId = false) {
        if ($perOrderId) {
            $fields = '"orderid":"'.$ids.'"'; // only one order ID
            $method = 'changeStatusPerOrderId';
        }
        else {
            $fields = '"ids":["'.implode('","', $ids).'"]'; // array of IDs
            $method = 'changeStatus';
        }
        return 'D='.urlencode('{"C":"Gpf_Rpc_Server","M":"run","requests":[{"C":"Pap_Merchants_Transaction_TransactionsForm","M":"'.$method.'","merchant_note":"status changed automatically","status":"'.$status.'", '.$fields.'}],"S":"'.$session.'"}');
    }

    public function safeString($str) {
        return str_replace(array('\\', '"'), array('/', '\"'), $str);
    }

    public function changeExtraData($data, $order, $item = '', $product = '') {
        switch ($data) {
          case 'empty':
              return null;
              break;
          case 'itemName':
              return (!empty($item)) ? $item->getName() : null;
              break;
          case 'itemQuantity':
              return (!empty($item)) ? $item->getQtyOrdered() : null;
              break;
          case 'itemPrice':
              if (!empty($item)) {
                  $rowtotal = $item->getBaseRowTotal();
                  if (empty($rowtotal)) {
                      return $item->getBasePrice();
                  }
                  return $rowtotal;
              }
              return null;
              break;
          case 'itemSKU':
              return (!empty($item)) ? $item->getSku() : null;
              break;
          case 'itemWeight':
              return (!empty($item)) ? $item->getWeight() : null;
              break;
          case 'itemWeightAll':
              return (!empty($item)) ? $item->getRowWeight() : null;
              break;
          case 'itemCost':
              return (!empty($item)) ? $item->getCost() : null;
              break;
          case 'itemDiscount':
              return (!empty($item)) ? abs($item->getBaseDiscountAmount()) : null;
              break;
          case 'itemDiscountPercent':
              return (!empty($item)) ? $item->getDiscountPercent() : null;
              break;
          case 'itemTax':
              return (!empty($item)) ? $item->getTaxAmount() : null;
              break;
          case 'itemTaxPercent':
              return (!empty($item)) ? $item->getTaxPercent() : null;
              break;
          case 'productCategoryID':
              return (!empty($product)) ? $product->getCategoryId() : null;
              break;
          case 'productURL':
              return (!empty($product)) ? $product->getProductUrl(false) : null;
              break;
          case 'storeID':
              return (!empty($order)) ? $order->getStoreId() : null;
              break;
          case 'internalOrderID':
              return (!empty($order)) ? $order->getId() : null;
              break;
          case 'customerID':
              return (!empty($order) && $order->getCustomerId()) ? $order->getCustomerId() : null;
              break;
          case 'customerEmail':
              return (!empty($order) && $order->getCustomerEmail()) ? $order->getCustomerEmail() : null;
              break;
          case 'customerName':
              return (!empty($order))?$order->getCustomerName() : null;
              break;
          case 'couponCode':
              return (!empty($order) && $order->getCouponCode())?$order->getCouponCode() : null;
              break;
          default: return $data;
        }
    }

    public function connectExternal($url, $query, $method = \Zend_Http_Client::POST) {
        $httpAdapter = $this->_curlFactory->create();
        $httpAdapter->setConfig(array('header' => false));
        $httpAdapter->setOptions(array(CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => 1, CURLOPT_POSTREDIR => 3));

        if ($method === \Zend_Http_Client::GET) {
            $url .= '?'.$query;
        }
        $httpAdapter->write($method, $url, '1.1', [], $query);

        try {
            $response = $httpAdapter->read();
        } catch (\Exception $e) {
            throw $e;
        }
        return $response;
    }
}
