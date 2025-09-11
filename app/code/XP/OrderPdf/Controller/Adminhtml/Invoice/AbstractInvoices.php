<?php
namespace XP\OrderPdf\Controller\Adminhtml\Invoice;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use XP\OrderPdf\Model\Pdf\Invoice;
use XP\OrderPdf\Model\PdfExporter;

class AbstractInvoices extends \Magento\Sales\Controller\Adminhtml\Invoice\Pdfinvoices
{
    /**
     * @var Invoice
     */
    protected $pdf;

    /**
     * @var PdfExporter
     */
    protected PdfExporter $pdfExporter;

    /**
     * @var ResultFactory
     */
    protected ResultFactory $resultRedirect;

    /**
     * @var bool
     */
    protected bool $rtl = false;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        DateTime $dateTime,
        FileFactory $fileFactory,
        \Magento\Sales\Model\Order\Pdf\Invoice $pdfInvoice,
        ResultFactory $result,
        Invoice $pdf,
        PdfExporter $pdfExporter
    ) {
        $this->pdf = $pdf;
        $this->pdfExporter = $pdfExporter;
        $this->resultRedirect = $result;
        parent::__construct($context, $filter, $dateTime, $fileFactory, $pdfInvoice, $collectionFactory);
    }

    protected function _redirectToLocaleSettings($orderModel, $path = 'xporderpdf/invoice/orderpdfinvoices/locale_param/ar')
    {
        $this->_localeResolver
            ->emulate($orderModel->getStoreId());
        $localeParam = $this->getRequest()->getParam('locale_param', false);
        if ($this->_localeResolver->getLocale() == "ar_SA" && !$localeParam) {
            $this->rtl = true;
            $this->getRequest()->setParam('locale', $this->_localeResolver->getLocale());
            $this->getRequest()->setParam('locale_param', $this->_localeResolver->getLocale());
            $this->_processLocaleSettings();
            $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($path, $this->getRequest()->getParams());
            return $resultRedirect;
        }

        if ($localeParam) {
            // Forcefully set back to en.
            $this->getRequest()->setParam('locale', 'en_US');
            $this->_processLocaleSettings();
        }
        return false;
    }
}
