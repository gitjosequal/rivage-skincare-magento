<?php
namespace Emizentech\ShopByBrand\Controller\Index;

use Magento\Framework\Controller\ResultFactory; 

class Filter extends \Magento\Framework\App\Action\Action
{
    protected $resource;
    protected $objectManager;
    protected $brandFactory;
    protected $resultPageFactory;
    protected $storeManager;
	protected $templateProcessor;
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\ResourceConnection  $resource,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Emizentech\ShopByBrand\Model\ItemsFactory $brandFactory,
		#\Magento\Cms\Model\Template\FilterProvider $templateProcessor,
		\Magento\Framework\UrlInterface $urlInterface,    
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_resource = $resource;
        $this->_objectManager = $objectManager;
		#$this->_templateProcessor = $templateProcessor;
        $this->_brandFactory = $brandFactory;
        $this->_storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
		$this->_urlInterface = $urlInterface;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
		$ingredients = $this->_storeManager->getStore()->getBaseUrl().'ingredients/view/index/id/';
		$url_ingredients = $this->_storeManager->getStore()->getBaseUrl().'ingredients/';
		$media_url = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$storid =  $this->_storeManager->getStore()->getStoreId();
		$key = $this->getRequest()->getParam('key');
		if($key){
			$html = '';
			$featured = $this->getBrands($key);
			$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
			$response->setHeader('Content-type', 'UTF-8');
			foreach($featured as $_fbrand){
				$html .= '<li class="col-lg-2 col-md-3 col-6">';
				if($_fbrand->getUrlKey()):
    				$html .='<a href="'.$url_ingredients.$_fbrand->getUrlKey().'">';
    			else:
    				$html .= '<a href="'.$ingredients.$_fbrand->getId().'">';
    			endif;
				$html .= '<img class="f-barnd-img" src="'.$media_url.$_fbrand->getLogo().'" style="height:150px"/>';
				if($storid == 2 || $storid == 4 || $storid == 10 || $storid == 12){
					$html .= '<span class="brand-title">'.$_fbrand->getAname().'</span>';
				} elseif($storid == 6) {
					$html .= '<span class="brand-title">'.$_fbrand->getGname().'</span>';
				} else {
					$html .= '<span class="brand-title">'.$_fbrand->getName().'</span>';	
				}
				$html .= '</a></li>';
			}
			$status = 'success';
		}
		$response->setContents(
			$this->jsonHelper->jsonEncode(
				[
					'status' => $status,
					'html' => $html,
				]
			)
		);
		return $response;
    }
	
	public function getBrands($key){
	  $storid =  $this->_storeManager->getStore()->getStoreId();
		$collection = $this->_brandFactory->create()->getCollection();
		$collection->addFieldToFilter('is_active' , \Emizentech\ShopByBrand\Model\Status::STATUS_ENABLED);
		if($key != 'all'){
		 if($storid == 2 || $storid == 4 || $storid == 10 || $storid == 12 ){
			$collection->addFieldToFilter('aname', array('like' => $key.'%'));
			} elseif($storid == 6){ 
			$collection->addFieldToFilter('gname', array('like' => $key.'%'));
			} else {
			$collection->addFieldToFilter('name', array('like' => $key.'%'));
				
			}
		}
		$collection->setOrder('name' , 'ASC');
		return $collection;
    }
}