<?php
namespace XP\OrderPdf\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use XP\OrderPdf\Controller\Adminhtml\AbstractPdfController;
use XP\OrderPdf\Model\Pdf\Order;
use XP\OrderPdf\Model\PdfExporter;

class PdfOrders extends AbstractPdfController
{
    const ADMIN_RESOURCE = 'Magento_Sales::sales_order';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Order
     */
    protected $pdf;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param ForwardFactory $resultForwardFactory
     * @param ResultFactory $result
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory
     * @param Order $pdf
     * @param PdfExporter $pdfExporter
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        ResultFactory $result,
        ScopeConfigInterface $scopeConfig,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        Order $pdf,
        PdfExporter $pdfExporter
    ) {
        $this->pdf = $pdf;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $fileFactory, $resultForwardFactory, $result, $scopeConfig, $pdfExporter);
    }

    /**
     * Print selected orders
     *
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            if (!$this->pdfExporter->setRtl($this->rtl)->exportPdf($this->pdf, $collection)) {
                throw new LocalizedException(__('No related orders were found.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
        return false;
    }
}
