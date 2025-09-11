<?php
namespace MageGuide\AlphaBank\Controller\Index;
use Magento\Framework\App\Action\Context;
class Success extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
 	protected $_model;
	protected $_helper;

    public function __construct(
	Context $context,
	\Magento\Framework\View\Result\PageFactory $resultPageFactory,
	\MageGuide\AlphaBank\Model\Alphabank $model,
	\MageGuide\AlphaBank\Helper\Data $helper
	)
    {
        $this->_resultPageFactory 	= $resultPageFactory;
		$this->_model				= $model;
		$this->_helper 				= $helper;
        parent::__construct($context);
    }

    public function execute()
    {
		$post = $this->getRequest()->getParams();
		$post = $this->getRequest()->getPostValue();
		if(isset($post['status']) && isset($post['txId'])){
			$response = $post;
			if($response['status']=='AUTHORIZED' || $response['status']=='CAPTURED'){
				try{
					$this->_helper->log($response,$response['orderid']);
					$result = $this->_model->processOrder($response);
					if($result){
		        		$this->_redirect('checkout/onepage/success', array('_secure'=>true));
						return;
					}
				}
				catch(\Exception $e){}
			}
		}
		$this->_redirect($this->_helper->getCancelUrl());
    }
}