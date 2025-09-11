<?php
namespace MageGuide\AlphaBank\Controller\Index;
use Magento\Framework\App\Action\Context;
class Cancel extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
 	protected $_model;
	protected $_helper;
	protected $_checkoutSession;
	
    public function __construct(
	Context $context, 
	\Magento\Framework\View\Result\PageFactory $resultPageFactory,
	\Magento\Checkout\Model\Session $checkoutSession,
	\MageGuide\AlphaBank\Model\Alphabank $model,
	\MageGuide\AlphaBank\Helper\Data $helper
	)
    {
        $this->_resultPageFactory 	= $resultPageFactory;
		$this->_checkoutSession  	= $checkoutSession;
		$this->_model				= $model;
		$this->_helper 				= $helper;
        parent::__construct($context);
    }
 
    public function execute()
    {
		$post = $this->getRequest()->getParams();
		
		if(isset($post['orderid'])){
			$response = $post;
			$this->_helper->log('--------------Cancel Order Response Starts---------------',$response['orderid']);
			$this->_helper->log($response,$response['orderid']);
			$this->_helper->log('--------------Cancel Order Response Ends---------------',$response['orderid']);
		}
		$incrementId = $this->_checkoutSession->getLastRealOrderId();
		
        if ($incrementId) 
		{
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($incrementId);
            if ($order->getId()) 
			{
                try {
                    $quoteRepository = $this->_objectManager->create('Magento\Quote\Api\CartRepositoryInterface');
                    $quote = $quoteRepository->get($order->getQuoteId());
                    $quote->setIsActive(1)->setReservedOrderId(null);
                    $quoteRepository->save($quote);
                    $this->_checkoutSession->replaceQuote($quote);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                }

                $this->_checkoutSession->unsetData('quote_id');
	            $order->registerCancellation(__('Could not complete the payment. Please try again.'))->save();
				$this->messageManager->addErrorMessage("Could not complete the payment. Please try again.");
            }
		}
		$this->_redirect('checkout/cart');
    }
}