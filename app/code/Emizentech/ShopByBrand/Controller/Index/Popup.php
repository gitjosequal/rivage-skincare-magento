<?php
namespace Emizentech\ShopByBrand\Controller\Index;

use Magento\Framework\Controller\ResultFactory; 

class Popup extends \Magento\Framework\App\Action\Action
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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_resource = $resource;
        $this->_objectManager = $objectManager;
		#$this->_templateProcessor = $templateProcessor;
        $this->_brandFactory = $brandFactory;
        $this->_storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
		$media_url = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$storid =  $this->_storeManager->getStore()->getStoreId();
		$id = $this->getRequest()->getParam('id');
		$html = '<div class="popup_main_content_list">';
		$response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');
        if ($id) {
			$content = $this->_brandFactory->create()->load($id);
			if($storid == 2){
				if($content->getAdescription() != ''){
					$description = $content->getAdescription();
				}else{
					$description = '';
				}
				$html .= '<div class="popup_content_1"><p class="popup_label">'.$content->getAname().'</p>';
				$html .= '<p class="popup_description">'.$description.'</p></div>';
				if($content->getMainimage()!=''){
				$html .= '<div class="popup_content_2"><img src="'.$media_url.$content->getMainimage().'"/></div>';
				}
			}else{
				if($content->getDescription() != ''){
					$description = $content->getDescription();
				}else{
					$description = '';
				}
				$html .= '<div class="popup_content_1"><p class="popup_label">'.$content->getName().'</p>';
				$html .= '<p class="popup_description">'.$description.'</p></div>';
				if($content->getMainimage()!=''){
				$html .= '<div class="popup_content_2"><img src="'.$media_url.$content->getMainimage().'"/></div>';
				}
			}
			$status = 'success';
		}else{
			$status = 'fail';
		}
		$html .= '</div>';
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
	
	public function filterOutputHtml($value='') 
	{
		$html = $this->_filterProvider->getPageFilter()->filter($value);
        return $html;
		
	}
}