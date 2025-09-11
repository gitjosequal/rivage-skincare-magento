<?php
/**
* FME Extensions
*
* NOTICE OF LICENSE 
*
* This source file is subject to the fmeextensions.com license that is
* available through the world-wide-web at this URL:
* https://www.fmeextensions.com/LICENSE.txt
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to newer
* version in the future.
*
* @category FME
* @package FME_CurrencySwitcher
* @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
* @license https://fmeextensions.com/LICENSE.txt
*/
namespace FME\CurrencySwitcher\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 */
class Data extends AbstractHelper
{
    /**
     * default Config Path
     */
    const XML_CONFIG_ENABLE_CURRENCYSWITCHER = 'genralsection/globalsetting/enable_currencyswitcher';
    const XML_CONFIG_ENABLE_ROUNDPRICES = 'genralsection/globalsetting/enable_roundprices';
    const XML_CONFIG_ENABLE_BASEROUNDPRICES = 'genralsection/globalsetting/enable_baseroundprices';
    const XML_CONFIG_ENABLE_ROUNDPRICESAlgo = 'genralsection/globalsetting/roundpricesalgo';
    const XML_CONFIG_CURRENCY_CURRENCYSWITCHER = 'genralsection/currencyswitcher_country_specific/country_currency';
    const XML_CONFIG_DISABLE_BOT = 'genralsection/currencyswitcher_restriction/disable_bot';
    //const XML_CONFIG_ENABLE_CLOUDFLAREIP = 'geo/cloudflaredatabase/enable_cloudflareip';

    protected $serialize;
    private \Magento\Store\Model\StoreManagerInterface $_storeManager;
    private \Magento\Framework\Locale\CurrencyInterface $currencyInterface;
    private \Magento\Framework\App\ResourceConnection $_resource;

    /**
     * __constructor
     *
     * @return void
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storemanager,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Magento\Framework\Locale\CurrencyInterface $currencyInterface
    )
    {
        $this->_storeManager =  $storemanager;
        $this->currencyInterface = $currencyInterface;
        $this->_resource = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\App\ResourceConnection');
        $this->serialize = $serialize;
        parent::__construct(
            $context
        );
    }
    /**
     * get BaseUrl
     *
     * @return string
     * 
     */
    public function getBaseUrl(){
        return $this->_storeManager->getStore()->getBaseUrl();
    }
    /**
     * get Allowed Currencies
     *
     * @return Array
     * 
     */
    public function getAllowedCurrency(){
        $availableCurrencies = $this->_storeManager->getStore()->getAvailableCurrencyCodes();
        foreach ($availableCurrencies as $currencyCode) {
            $options[] = ['value' => $currencyCode, 'label' => $this->currencyInterface->getCurrency($currencyCode)->getName()];
        }
        return $options;
    }
    /**
     * get Media Path
     *
     * @param string $type
     * @return string
     * 
     */
    public function getMediaType($type = 'url')
    {
        $media = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
        if ($type == 'path') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $dir = $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList');
            $media = $dir->getPath($dir::MEDIA);
        }
        return $media;
    }
    /**
     * get Complete Path with file name
     *
     * @param string $fileName
     * @return string
     * 
     */
    public function prepareCsv($fileName = '')
    {
        $file = '/geoipcurrencyswitcher/' . $fileName . '.csv';
        $media = $this->getMediaType('path');
        $csvPath = $media . $file;
        return $csvPath;
    }
    /**
     * check extension enable
     *
     * @return int|''
     * 
     */
    public function isEnabled(){
        return $this->_getConfig(self::XML_CONFIG_ENABLE_CURRENCYSWITCHER);
    }
    /**
     * check round price enable
     *
     * @return int|''
     * 
     */
    public function isEnabledRoundPrices(){
        return $this->_getConfig(self::XML_CONFIG_ENABLE_ROUNDPRICES);
    }
    /**
     * check base round price enable
     *
     * @return int|''
     * 
     
    public function isEnabledBaseRoundPrices(){
        return $this->_getConfig(self::XML_CONFIG_ENABLE_BASEROUNDPRICES);
    }*/
    /**
     * get round price algorithm
     *
     * @return int
     * 
     */
    public function isRoundPricesAlgo(){
        return $this->_getConfig(self::XML_CONFIG_ENABLE_ROUNDPRICESAlgo);
    }
    /**
     * get user-agent
     *
     * @return string
     * 
     */
    public function isDisableBot(){
        return $this->_getConfig(self::XML_CONFIG_DISABLE_BOT);
    }

    /*public function isEnabledCloudFlare(){
        return $this->_getConfig(self::XML_CONFIG_ENABLE_CLOUDFLAREIP);
    }*/
    /**
     * get Currency & Country
     *
     * @return Array
     * 
     */
    public function currencyCountry(){
        $currencyconfig = $this->serialize->unserialize($this->scopeConfig->getValue(
        'genralsection/currencyswitcher_country_specific/country_currency',
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
        return $currencyconfig;
    }
    /**
     * get location information
     *
     * @param $remoteIp
     * @return Array
     * 
     */
    public function getInfoByIp($remoteIp)
    {
        $result = [];

        if (filter_var($remoteIp, FILTER_VALIDATE_IP)) {
            $read = $this->_resource->getConnection('core_read');
            $select = $read->select()
                    ->from(['gcsv' => $this->_resource->getTableName('geoip_csv')])
                    ->where('INET_ATON(gcsv.end_ip) >= INET_ATON(?)', $remoteIp)
                    ->limit(1);

            return $read->fetchRow($select);
        }
        return $result;
    }
    /**
     * check Web Crawler
     *
     * @param $request
     * @return boolen
     * 
     */
    public function isWebCrawler($request)
    {

        $exceptionUrlList = ['/api/', '/paypal/'];

        $remoteAddress = new \Magento\Framework\Http\PhpEnvironment\RemoteAddress($request);
        //robot ip address is assigned to $ipaddress
        $ipaddress = $remoteAddress->getRemoteAddress();

        //hostname is assigned to $hostname
        $hostname = $remoteAddress->getRemoteHost();
        $restrictedBot = $this->isDisableBot();
        $restrictedBotArray = explode(',', $restrictedBot);
        /*if(preg_match('/apple|bot|robot|baidu|bingbot|facebookexternalhit|googlebot|-google|ia_archiver|msnbot|naverbot|pingdom|seznambot|slurp|teoma|twitter|yandex|yeti/i', $_SERVER['HTTP_USER_AGENT'])){}*/
        if (in_array($hostname, $restrictedBotArray)) {
            return true;
        }
        
        
        foreach ($exceptionUrlList as $url) {
            if (preg_match($url, $hostname)) {
                return true;
            }
        }
        return false;
    }
    /**
     * @param $path
     * @return int
     */
    protected function _getConfig($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}
