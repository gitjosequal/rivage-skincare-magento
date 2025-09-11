<?php
namespace XP\OrderPdf\Controller\Shipment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use XP\OrderPdf\Controller\Order\AbstractPrint;
use XP\OrderPdf\Model\Pdf\ShipmentFactory;
use XP\OrderPdf\Model\PdfExporter;

class PrintAction extends AbstractPrint
{
    /**
     * @var ShipmentFactory
     */
    private ShipmentFactory $pdfFactory;

    /**
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     * @param PdfExporter $pdfExporter
     * @param OrderFactory $orderFactory
     * @param ShipmentFactory $shipmentFactory
     */
    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        PdfExporter $pdfExporter,
        OrderFactory $orderFactory,
        ShipmentFactory $shipmentFactory
    ) {
        $this->pdfFactory = $shipmentFactory;
        parent::__construct($context, $orderLoader, $resultPageFactory, $pdfExporter, $orderFactory);
    }

    /**
     * @inheirtDoc
     */
    public function execute()
    {
        if (!$shipment = $this->getShipment()) {
            return parent::execute();
        }
        $this->exportPdf($this->pdfFactory->create(), $shipment);
        exit();
    }

    /**
     * @return false|Collection
     */
    private function getShipment()
    {
        if (!$order = $this->loadCurrentOrder()) {
            return false;
        }
        return $order->getShipmentsCollection();
    }
}
