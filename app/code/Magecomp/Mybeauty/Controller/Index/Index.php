<?php 
namespace Magecomp\Mybeauty\Controller\Index;
 
use Magento\Customer\Model\Session as CustomerSession;

class Index extends \Magento\Framework\App\Action\Action
{
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory		
    )
    {
		$this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
	public function execute()
    {
	    $customerSession = $this->_customerSession->get('Magento\Customer\Model\Session');
        $customerId = $this->_customerSession->getCustomerId();
        if(!empty($customerId) && $customerId != '' && $customerId != null) {
            $resultPage = $this->resultPageFactory->create();
			//$resultPage->getConfig()->getTitle()->prepend(__('Beauty Profile'));
			return $resultPage;
        }else{
            $this->_redirect('customer/account/login/');
        }
    }
}