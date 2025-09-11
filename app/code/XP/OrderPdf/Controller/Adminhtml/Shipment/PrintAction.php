<?php
namespace XP\OrderPdf\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\ShipmentRepositoryInterfaceFactory;
use XP\OrderPdf\Controller\Adminhtml\AbstractPdfController;
use XP\OrderPdf\Model\Pdf\Shipment;
use XP\OrderPdf\Model\PdfExporter;

class PrintAction extends AbstractPdfController implements HttpGetActionInterface
{
    private Shipment $shipment;

    private ShipmentRepositoryInterfaceFactory $shipmentFactory;

    /**
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param ForwardFactory $resultForwardFactory
     * @param ResultFactory $result
     * @param ScopeConfigInterface $scopeConfig
     * @param Shipment $pdf
     * @param PdfExporter $pdfExporter
     * @param ShipmentRepositoryInterfaceFactory $shipmentFactory
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        ResultFactory $result,
        ScopeConfigInterface $scopeConfig,
        Shipment $pdf,
        PdfExporter $pdfExporter,
        ShipmentRepositoryInterfaceFactory $shipmentFactory
    ) {
        $this->pdf = $pdf;
        $this->shipmentFactory = $shipmentFactory;
        parent::__construct($context, $fileFactory, $resultForwardFactory, $result, $scopeConfig, $pdfExporter);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        if (!$shipmentId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        $shipment = $this->shipmentFactory->create()->get($shipmentId);
        $pdfOut = $this->pdfExporter->setRtl($this->rtl)->setOutputName(
            $shipment,
            'Shipment#' . $shipment->getIncrementId()
        )->exportPdf($this->pdf, $shipment);
        if (!$pdfOut) {
            $this->messageManager->addErrorMessage(__('No related order was found.'));
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
        return false;
    }
}
