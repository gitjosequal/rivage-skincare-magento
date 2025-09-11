<?php
namespace XP\OrderPdf\Controller\Invoice;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use XP\OrderPdf\Controller\Order\AbstractPrint;
use XP\OrderPdf\Model\Pdf\InvoiceFactory;
use XP\OrderPdf\Model\PdfExporter;

class PrintAction extends AbstractPrint
{
    /**
     * @var InvoiceFactory
     */
    private InvoiceFactory $pdfFactory;

    /**
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     * @param PdfExporter $pdfExporter
     * @param OrderFactory $orderFactory
     * @param InvoiceFactory $pdfFactory
     */
    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        PdfExporter $pdfExporter,
        OrderFactory $orderFactory,
        InvoiceFactory $pdfFactory
    ) {
        $this->pdfFactory = $pdfFactory;
        parent::__construct($context, $orderLoader, $resultPageFactory, $pdfExporter, $orderFactory);
    }

    /**
     * @inheirtDoc
     */
    public function execute()
    {
        if (!$invoice = $this->getInvoice()) {
            return parent::execute();
        }
        $this->exportPdf($this->pdfFactory->create(), $invoice);
        exit;
    }

    /**
     * @return false|Collection
     */
    private function getInvoice()
    {
        if (!$order = $this->loadCurrentOrder()) {
            return false;
        }
        return $order->getInvoiceCollection();
    }
}
