<?php
namespace XP\OrderPdf\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterfaceFactory;
use Magento\Store\Api\Data\StoreInterface;
use XP\OrderPdf\Model\Pdf\Order;
use XP\OrderPdf\Controller\Adminhtml\AbstractPdfController;
use XP\OrderPdf\Model\PdfExporter;

/**
 * Class PrintAction
 * @package XP\OrderPdf\Controller\Adminhtml\Order
 */
class PrintAction extends AbstractPdfController implements HttpGetActionInterface
{

    /**
     * @var Order
     */
    private Order $pdf;

    /**
     * @var OrderRepositoryInterfaceFactory
     */
    private OrderRepositoryInterfaceFactory $orderRepositoryFactory;

    private $order = null;

    /**
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param ForwardFactory $resultForwardFactory
     * @param ResultFactory $result
     * @param ScopeConfigInterface $scopeConfig
     * @param Order $pdf
     * @param PdfExporter $pdfExporter
     * @param OrderRepositoryInterfaceFactory $orderRepositoryFactory
     * @param StoreInterface $storeFactory
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        ResultFactory $result,
        ScopeConfigInterface $scopeConfig,
        Order $pdf,
        PdfExporter $pdfExporter,
        OrderRepositoryInterfaceFactory $orderRepositoryFactory
    ) {
        $this->pdf = $pdf;
        $this->orderRepositoryFactory = $orderRepositoryFactory;
        parent::__construct($context, $fileFactory, $resultForwardFactory, $result, $scopeConfig, $pdfExporter);
    }

    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->getRequest()->getParam('usd', false)
            || !$orderId = $this->getRequest()->getParam('order_id')) {
            return parent::dispatch($request);
        }

        /**
         * Set Locale for PRINT USD, to allow admin print based on order.
         */
        $order = $this->getOrder($orderId);
        if ($redirect = $this->_redirectToLocaleSettings($order)) {
            return $redirect;
        }
        return parent::dispatch($request);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$orderId = $this->getRequest()->getParam('order_id')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        $this->pdfExporter->setRtl($this->rtl)->exportPdf(
            $this->pdf,
            $this->getOrder($orderId)
        );
        return false;
    }

    /**
     * @param int $orderId
     * @return OrderInterface|Order
     */
    private function getOrder($orderId)
    {
        if ($this->order == null) {
            $this->order = $this->orderRepositoryFactory->create()->get($orderId);
        }
        return $this->order;
    }
}
