<?php
namespace XP\OrderPdf\Controller\Adminhtml\Invoice;

class PdfInvoices extends AbstractInvoices
{
    /**
     * Print selected Invoices
     *
     * @inheritDoc
     */
    public function execute()
    {
        $invoicesCollection = $this->collectionFactory->create()->setOrderFilter(['in' => $this->getRequest()->getParam('selected')]);
        if (!$invoicesCollection->getSize()) {
            $this->messageManager->addErrorMessage(__('There are no printable documents related to selected orders.'));
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        $this->pdfExporter->exportPdf($this->pdf, $invoicesCollection);
        return false;
    }
}
