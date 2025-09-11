<?php
namespace XP\OrderPdf\Model\Pdf;

use Magento\Framework\Data\Collection\AbstractDb;
use TCPDF;

class Order extends AbstractPdf
{

    /**
     * @param null|Order|Order[] $order
     * @return \Zend_Pdf|TCPDF|null
     */
    public function getPdf($orders = null)
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('order');
        /**
         * 1. Configure the Pdf
         */
        $this->_configurePdf();

        if (!is_array($orders) && !($orders instanceof AbstractDb)) {
            $orders = [$orders];
        }
        $pdfTitle = '';
        foreach ($orders as $order) {
            $this->_orderData = $order->toArray();
            /**
             * 2. Add Page
             */
            $this->_pdf->AddPage();
            foreach ($this->_getPdfConfigData($order) as $key => $data) {
                if(isset($data['html'])){
                    $data['html'] = str_replace('__qrCode__','',$data['html']);
                }
                $this->writeHTML($key, $data, $data["new_line"], false, true, false, '');
            }
            if ($pdfTitle) {
                $pdfTitle .= ",";
            }
            $pdfTitle .= " #" . $order->getIncrementId();
        }

        $this->_pdf->setTitle('Little Flora Order(s) ' . $pdfTitle );
        return $this->_pdf;
    }
}
