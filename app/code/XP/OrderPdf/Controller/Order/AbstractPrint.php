<?php
namespace XP\OrderPdf\Controller\Order;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\OrderRepositoryInterfaceFactory;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use XP\OrderPdf\Model\PdfExporter;

abstract class AbstractPrint extends \Magento\Sales\Controller\Order\PrintAction
{

    /**
     * @var PdfExporter
     */
    protected PdfExporter $_pdfExporter;

    protected OrderFactory $orderFactory;

    /**
     * @param Context $context
     * @param OrderLoaderInterface $orderLoader
     * @param PageFactory $resultPageFactory
     * @param PdfExporter $pdfExporter
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        OrderLoaderInterface $orderLoader,
        PageFactory $resultPageFactory,
        PdfExporter $pdfExporter,
        OrderFactory $orderFactory
    ) {
        $this->_pdfExporter = $pdfExporter;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $orderLoader, $resultPageFactory);
    }

    /**
     * @param mixed $pdfModel
     * @param mixed $orderModel
     */
    public function exportPdf($pdfModel, $orderModel): void
    {
        $this->_pdfExporter->setRtl()->exportPdf(
            $pdfModel,
            $orderModel
        );
    }

    /**
     * @return false|Order
     */
    public function loadCurrentOrder()
    {
        if (!($orderId = $this->getRequest()->getParam('order_id'))) {
            return false;
        }
        return $this->orderFactory->create()->loadByAttribute('entity_id', $orderId);
    }
}
