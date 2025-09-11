<?php 
namespace Magecomp\Mybeauty\Controller\Index;
 
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\ResultFactory;

class Submit extends \Magento\Framework\App\Action\Action
{
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Model\CustomerFactory $customerModel,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory		
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerModel = $customerModel;
		$this->_customerSession = $customerSession;
        parent::__construct($context);
    }
	public function execute()
    {
		$my_skin = $this->getRequest()->getParam('my_skin');
        $my_hair_skin = $this->getRequest()->getParam('my_hair_skin');
		$customerId = $this->_customerSession->getCustomerId();
		#$resource = $this->_objectManager->create('\Magento\Framework\App\ResourceConnection');
		#$connection = $resource->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION');
		$resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$customer_table = $resource->getTableName('customer_entity');
		$update = "UPDATE `{$customer_table}` SET `my_skin` ='{$my_skin}', `my_hair_skin`='{$my_hair_skin}' WHERE `entity_id`='{$customerId}'"; 
		$connection->query($update);
		$redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$redirect->setUrl($this->_redirect->getRefererUrl());
		$this->messageManager->addSuccess( __('Your beauty profile has been submitted.') );
		return $redirect;
	}

}