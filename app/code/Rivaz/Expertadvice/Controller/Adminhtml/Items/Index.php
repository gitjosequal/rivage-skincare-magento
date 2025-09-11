<?php


namespace Rivaz\Expertadvice\Controller\Adminhtml\Items;

class Index extends \Rivaz\Expertadvice\Controller\Adminhtml\Items
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Rivaz_Expertadvice::expertadvice');
        $resultPage->getConfig()->getTitle()->prepend(__('Rivage Expertadvice'));
        $resultPage->addBreadcrumb(__('Rivaz'), __('Rivaz'));
        $resultPage->addBreadcrumb(__('Expertadvice'), __('Expertadvice'));
        return $resultPage;
    }
}
