<?php
namespace XP\OrderPdf\Controller\Adminhtml\Invoice;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterfaceFactory;
use XP\OrderPdf\Controller\Adminhtml\AbstractPdfController;
use XP\OrderPdf\Model\Pdf\Invoice;
use XP\OrderPdf\Model\PdfExporter;

class PrintAction extends AbstractPdfController implements HttpGetActionInterface
{
    /**
     * @var Invoice
     */
    private Invoice $pdf;

    /**
     * @var InvoiceRepositoryInterfaceFactory
     */
    private InvoiceRepositoryInterfaceFactory $invoiceRepositoryFactory;

    /**
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param ForwardFactory $resultForwardFactory
     * @param ResultFactory $result
     * @param ScopeConfigInterface $scopeConfig
     * @param Invoice $pdf
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        ResultFactory $result,
        ScopeConfigInterface $scopeConfig,
        Invoice $pdf,
        PdfExporter $pdfExporter,
        InvoiceRepositoryInterfaceFactory $invoiceRepositoryFactory
    ) {
        $this->pdf = $pdf;
        $this->invoiceRepositoryFactory = $invoiceRepositoryFactory;
        parent::__construct($context, $fileFactory, $resultForwardFactory, $result, $scopeConfig, $pdfExporter);
    }

    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$invoiceId = $this->getRequest()->getParam('invoice_id')) {
            return parent::dispatch($request);
        }

        /**
         * Set Locale for PRINT USD, to allow admin print based on order.
         */
        $invoice = $this->getInvoice($invoiceId);
        if ($redirect = $this->_redirectToLocaleSettings($invoice, 'xporderpdf/invoice/printaction/locale_param/ar')) {
            return $redirect;
        }
        return parent::dispatch($request);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$invoiceId = $this->getRequest()->getParam('invoice_id')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        $invoice = $this->getInvoice($invoiceId);
        $this->pdfExporter->setRtl($this->rtl)->setOutputName(
            $invoice,
            'Invoice#' . $invoice->getIncrementId()
        )->exportPdf($this->pdf, $invoice);
        return false;
    }

    /**
     * @param int $invoiceId
     * @return InvoiceInterface
     */
    private function getInvoice($invoiceId): InvoiceInterface
    {
        return $this->invoiceRepositoryFactory->create()->get($invoiceId);
    }
}
