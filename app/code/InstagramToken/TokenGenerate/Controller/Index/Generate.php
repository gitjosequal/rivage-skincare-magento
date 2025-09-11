<?php
namespace InstagramToken\TokenGenerate\Controller\Index;

use Mageplaza\InstagramFeed\Helper\Data;

class Generate extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	private $instagram;
	private $storeManager;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{

	    $obj = \Magento\Framework\App\ObjectManager::getInstance();
	    $this->storeManager = $obj->get('\Magento\Store\Model\StoreManagerInterface');
	    
	    $stores = $this->storeManager->getStores(true);

        $this->instagram = $obj->get('\Mageplaza\InstagramFeed\Helper\Data');
        foreach($stores as $store){
            $accessToken =  $this->instagram->getAccessToken($store->getId());
            if(!$accessToken){
                continue;
            }
            
    		$curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token='.$accessToken,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            $response = json_decode($response);
            if(isset($response->error)){
                echo "Error: " . $response->error->message;
                $this->sendMail($store,$response->error);
            }else{
                $configInterface = $obj->get('\Magento\Framework\App\Config\ConfigResource\ConfigInterface');
                $configInterface->saveConfig('mpinstagramfeed/general/access_token', $response->access_token, 'default', $store->getId());
                echo "Updated successfully " . $store->getName() . "<br>";
                
            }
            
        }
        
        $cacheManager = $obj->get('\Magento\Framework\App\Cache\Manager');
        $cacheManager->flush($cacheManager->getAvailableTypes());
        
        echo "Done";
	}
	
	private function sendMail($store,$error){
	    $to = "yousef@josequal.com";
        $subject = "Instagram Error " . $store->getName();
        
        $message = 'Hello, <br>';
        $message .= "Server can not update instagram access token <br>";
        $message .= "Website: " . $store->getName() . "<br>";
        $message .= "Url: " . $store->getBaseUrl() . "<br>";
        $message .= "Error: " . $error->message;
        
        // Always set content-type when sending HTML email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        // More headers
        $headers .= 'From: <server@rivage.co>' . "\r\n";
        //$headers .= 'Cc: myboss@example.com' . "\r\n";
        
        mail($to,$subject,$message,$headers);
	}
}