<?php
namespace XP\OrderPdf\Controller\Adminhtml\Invoice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class OrderPdfInvoices extends AbstractInvoices
{
    public function massAction(AbstractCollection $collection)
    {
        if ($invoice = $collection->getFirstItem()) {
            if ($redirect = $this->_redirectToLocaleSettings($invoice)) {
                return $redirect;
            }
        }
        $this->pdfExporter->setRtl($this->rtl)->exportPdf($this->pdf, $collection);
        exit();
    }
}
