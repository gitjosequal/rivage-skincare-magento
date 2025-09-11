<?php
namespace XP\OrderPdf\Controller\Adminhtml\Creditmemo;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use XP\OrderPdf\Controller\Adminhtml\AbstractPdfController;
use XP\OrderPdf\Model\Pdf\Creditmemo;
use XP\OrderPdf\Model\PdfExporter;

class PdfCreditmemos extends \Magento\Sales\Controller\Adminhtml\Creditmemo\Pdfcreditmemos
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Sales::creditmemo';

    /**
     * @var Creditmemo
     */
    protected $pdf;

    /**
     * @var PdfExporter
     */
    private PdfExporter $pdfExporter;

    public function __construct(
        Context                                   $context,
        Filter                                    $filter,
        \Magento\Sales\Model\Order\Pdf\Creditmemo $pdfCreditmemo,
        DateTime                                  $dateTime,
        FileFactory                               $fileFactory,
        CollectionFactory                         $collectionFactory,
        Creditmemo                                $pdf,
        PdfExporter                               $pdfExporter
    ) {
        $this->pdf = $pdf;
        $this->pdfExporter = $pdfExporter;
        parent::__construct($context, $filter, $pdfCreditmemo, $dateTime, $fileFactory, $collectionFactory);
    }

    public function massAction(AbstractCollection $collection)
    {
        $this->pdfExporter->exportPdf($this->pdf, $collection);
        return false;
    }
}
