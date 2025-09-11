<?php
namespace XP\OrderPdf\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\Order\Pdf\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use XP\OrderPdf\Model\Pdf\Shipment as ShipmentPdf;
use XP\OrderPdf\Model\PdfExporter;

class PdfShipments extends \Magento\Sales\Controller\Adminhtml\Shipment\Pdfshipments
{
    /**
     * @var ShipmentPdf
     */
    protected $pdf;

    /**
     * @var PdfExporter
     */
    private PdfExporter $pdfExporter;

    /**
     * @var string
     */
    protected $redirectUrl = 'sales/order/index';

    public function __construct(
        Context $context,
        Filter $filter,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Shipment $shipment,
        CollectionFactory $collectionFactory,
        ShipmentPdf $pdf,
        PdfExporter $pdfExporter
    ) {
        $this->pdf = $pdf;
        $this->pdfExporter = $pdfExporter;
        parent::__construct($context, $filter, $dateTime, $fileFactory, $shipment, $collectionFactory);
    }

    public function massAction(AbstractCollection $collection)
    {
        if (!$collection->getSize()) {
            $this->messageManager->addErrorMessage(__('There are no printable documents related to selected orders.'));
            return $this->resultRedirectFactory->create()->setPath($this->getComponentRefererUrl());
        }
        $this->pdfExporter->exportPdf($this->pdf, $collection);
        return false;
    }
}
