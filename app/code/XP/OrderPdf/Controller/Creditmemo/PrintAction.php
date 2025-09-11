<?php
namespace XP\OrderPdf\Controller\Creditmemo;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection;
use XP\OrderPdf\Controller\Order\AbstractPrint;
use XP\OrderPdf\Model\Pdf\CreditmemoFactory;
use XP\OrderPdf\Model\PdfExporter;

class PrintAction extends AbstractPrint
{
    private CreditmemoFactory $pdfFactory;

    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        PdfExporter $pdfExporter,
        OrderFactory $orderFactory,
        CreditmemoFactory $creditmemoFactory
    ) {
        $this->pdfFactory = $creditmemoFactory;
        parent::__construct($context, $orderLoader, $resultPageFactory, $pdfExporter, $orderFactory);
    }
    /**
     * @inheirtDoc
     */
    public function execute()
    {
        if (!$creditmemo = $this->getCreditmemo()) {
            return parent::execute();
        }
        $this->exportPdf($this->pdfFactory->create(), $creditmemo);
        exit();
    }

    /**
     * @return false|Collection
     */
    private function getCreditmemo()
    {
        if (!$order = $this->loadCurrentOrder()) {
            return false;
        }
        return $order->getCreditmemosCollection();
    }
}
