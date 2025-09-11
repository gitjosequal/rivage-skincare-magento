<?php
namespace XP\OrderPdf\Model\Pdf;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Api\OrderRepositoryInterface;
use \TCPDF;

class Invoice extends Order
{
    /**
     * @param null|\Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Invoice[] $invoices
     * @return \Zend_Pdf|TCPDF|null
     */
    public function getPdf($invoices = null)
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');
        /**
         * 1. Configure the Pdf
         */
        $this->_configurePdf();
        if (!is_array($invoices) && !$invoices instanceof AbstractDb) {
            $invoices = [$invoices];
        }
        $pdfTitle = '';
        foreach ($invoices as $invoice) {
            $order = $this->_getOrder($invoice->getOrderId());
            $this->_orderData = $invoice->toArray();
            /**
             * 2. Add Page
             */
            $this->_pdf->AddPage();
            // Add QR to the Invoice
            //$this->_addQRCode($this->_getQRCodeConfigHtml($invoice));

            $imgQrCode = $this->getQrCode($invoice);

            foreach ($this->_getPdfConfigData($order, 'invoice') as $key => $data) {
                if(isset($data['html'])){
                    $data['html'] = str_replace('__qrCode__',$imgQrCode,$data['html']);
                }
                $this->writeHTML($key, $data, ($data["new_line"] ?? true), false, true, false, '');
            }
            
            if ($pdfTitle) {
                $pdfTitle .= ",";
            }
            $pdfTitle .= " #" . $invoice->getIncrementId();
        }
        $this->_pdf->setTitle('Rivage Invoice(s) ' . $pdfTitle );
        return $this->_pdf;
    }

    protected function _getOrder($orderId)
    {
        return ObjectManager::getInstance()->create(
            OrderRepositoryInterface::class
        )->get($orderId);
    }
}
