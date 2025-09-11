<?php
namespace XP\OrderPdf\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Sales\Model\OrderFactory;
use XP\OrderPdf\Model\Pdf\OrderFactory as OrderPDFFactory;
use Magento\Sales\Api\OrderRepositoryInterfaceFactory;
use XP\OrderPdf\Model\PdfExporter;

/**
 * Print Order Action
 */
class PrintAction extends AbstractPrint
{
    /**
     * @var OrderPDFFactory
     */
    private OrderPDFFactory $pdf;

    /**
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     * @param PdfExporter $pdfExporter
     * @param OrderFactory $orderFactory
     * @param OrderPDFFactory $orderPdfFactory
     */
    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        PdfExporter $pdfExporter,
        OrderFactory $orderFactory,
        OrderPDFFactory $orderPdfFactory
    ) {
        $this->pdf = $orderPdfFactory;
        parent::__construct($context, $orderLoader, $resultPageFactory, $pdfExporter, $orderFactory);
    }

    /**
     * @inheirtDoc
     */
    public function execute()
    {
        if (!$order = $this->loadCurrentOrder()) {
            return parent::execute();
        }
        $this->exportPdf($this->pdf->create(), $order);
        exit();
    }
}
