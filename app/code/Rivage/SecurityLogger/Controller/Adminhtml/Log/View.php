<?php
namespace Rivage\SecurityLogger\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class View extends Action
{
    protected $resultPageFactory;
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        return $this->resultPageFactory->create();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Rivage_SecurityLogger::log_viewer');
    }
}
