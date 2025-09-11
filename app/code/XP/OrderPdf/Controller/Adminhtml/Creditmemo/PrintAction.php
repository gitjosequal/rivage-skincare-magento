<?php
namespace XP\OrderPdf\Controller\Adminhtml\Creditmemo;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterfaceFactory;
use XP\OrderPdf\Controller\Adminhtml\AbstractPdfController;
use XP\OrderPdf\Model\Pdf\Creditmemo;
use XP\OrderPdf\Model\PdfExporter;

class PrintAction extends AbstractPdfController implements HttpGetActionInterface
{
    /**
     * @var Creditmemo
     */
    private Creditmemo $pdf;

    /**
     * @var CreditmemoRepositoryInterfaceFactory
     */
    private CreditmemoRepositoryInterfaceFactory $creditmemoRepositoryFactory;

    /**
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param ForwardFactory $resultForwardFactory
     * @param ResultFactory $result
     * @param ScopeConfigInterface $scopeConfig
     * @param Creditmemo $pdf
     * @param PdfExporter $pdfExporter
     * @param CreditmemoRepositoryInterfaceFactory $creditmemoRepository
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        ResultFactory $result,
        ScopeConfigInterface $scopeConfig,
        Creditmemo $pdf,
        PdfExporter $pdfExporter,
        CreditmemoRepositoryInterfaceFactory $creditmemoRepository
    ) {
        $this->pdf = $pdf;
        $this->creditmemoRepositoryFactory = $creditmemoRepository;
        parent::__construct($context, $fileFactory, $resultForwardFactory, $result, $scopeConfig, $pdfExporter);
    }

    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            return parent::dispatch($request);
        }

        /**
         * Set Locale for PRINT USD, to allow admin print based on order.
         */
        $creditmemo = $this->getCreditMemo($creditmemoId);
        if ($redirect = $this->_redirectToLocaleSettings($creditmemo, 'xporderpdf/creditmemo/printaction/locale_param/ar')) {
            return $redirect;
        }
        return parent::dispatch($request);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (!$creditmemoId = $this->getRequest()->getParam('creditmemo_id')) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        $creditmemo = $this->getCreditMemo($creditmemoId);
        $this->pdfExporter->setRtl($this->rtl)->setOutputName(
            $creditmemo,
            'Credit_Memo#' . $creditmemo->getIncrementId()
        )->exportPdf($this->pdf, $creditmemo);
        return false;
    }

    private function getCreditMemo($creditmemoId)
    {
        return $this->creditmemoRepositoryFactory->create()->get($creditmemoId);
    }
}
