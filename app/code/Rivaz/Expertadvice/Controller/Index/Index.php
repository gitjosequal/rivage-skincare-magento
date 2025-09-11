<?php 

namespace Rivaz\Expertadvice\Controller\Index; 
 
class Index extends \Magento\Framework\App\Action\Action {
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;
    /** @param \Magento\Framework\App\Action\Context $context */
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)     {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
    	$resultPage = $this->resultPageFactory->create();
    	//$resultPage->getConfig()->getTitle()->prepend(__('Expert Advice'));
		$resultPage->getConfig()->getTitle()->set("Expert Advice for Your Skin or Hair – Rivage");
    	return $resultPage;
    }
}
 