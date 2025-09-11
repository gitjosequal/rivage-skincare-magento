<?php
namespace MageGuide\AlphaBank\Controller\Index;
use Magento\Framework\App\Action\Context;
class Start extends \Magento\Framework\App\Action\Action
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
		try
		{
			$resultPage = $this->_resultPageFactory->create();
		    $resultPage->getConfig()->getTitle()->prepend(__('Alpha Bank Payment'));

		    $block = $resultPage->getLayout()
        		->createBlock('MageGuide\AlphaBank\Block\Redirect')
                ->toHtml();
		    $this->getResponse()->setBody($block);
			return;
		}
		catch(\Exception $e){}
		$this->_redirect($this->_helper->getCancelUrl());
    }
}