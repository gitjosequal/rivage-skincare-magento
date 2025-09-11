<?php
namespace XP\OrderPdf\Model\Pdf;

use Magento\Framework\Data\Collection\AbstractDb;
use TCPDF;

class Creditmemo extends Invoice
{
    /**
     * @param null|\Magento\Sales\Model\Order\Creditmemo|\Magento\Sales\Model\Order\Creditmemo[] $creditmemos
     * @return \Zend_Pdf|TCPDF|null
     */
    public function getPdf($creditmemos = null)
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('creditmemo');
        /**
         * 1. Configure the Pdf
         */
        $this->_configurePdf();
        if (!is_array($creditmemos) && !$creditmemos instanceof AbstractDb) {
            $creditmemos = [$creditmemos];
        }
        $pdfTitle = [];
        foreach ($creditmemos as $creditmemo) {
            $order = $this->_getOrder($creditmemo->getOrderId());
            $this->_orderData = $creditmemo->getData();
            /**
             * 2. Add Page
             */
            $this->_pdf->AddPage();
            $creditmemoData = $this->_getPdfConfigData($order, 'creditmemo');
            foreach ($creditmemoData as $key => $data) {
                if(isset($data['html'])){
                    $data['html'] = str_replace('__qrCode__','',$data['html']);
                }
                $this->writeHTML($key, $data, ($data["new_line"] ?? true), false, true, false, '');
            }

            $pdfTitle []= " #" . $creditmemo->getIncrementId();
        }
        $this->_pdf->setTitle(__('Little Flora Creditmemo' . (count($pdfTitle) > 1 ? "s" : "" ))
            . implode(",", $pdfTitle)
        );
        return $this->_pdf;
    }
}
