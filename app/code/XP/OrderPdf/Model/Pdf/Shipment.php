<?php
namespace XP\OrderPdf\Model\Pdf;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Model\Order;
use \TCPDF;

class Shipment extends Invoice
{
    /**
     * @param null|\Magento\Sales\Model\Order\Shipment|\Magento\Sales\Model\Order\Shipment[] $shipments
     * @return \Zend_Pdf|TCPDF|null
     */
    public function getPdf($shipments = null)
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('shipment');
        /**
         * 1. Configure the Pdf
         */
        $this->_configurePdf();
        if (!is_array($shipments) && !$shipments instanceof AbstractDb) {
            $shipments = [$shipments];
        }
        $pdfTitle = [];
        foreach ($shipments as $shipment) {
            $order = $this->_getOrder($shipment->getOrderId());
            $this->_orderData = $shipment->toArray();
            $this->_orderData["tacking"] = $shipment->getTracksCollection();
            /**
             * 2. Add Page
             */
            $this->_pdf->AddPage();
            $shipmentData = $this->_getPdfConfigData($order, 'shipment', ['items']);
            foreach ($shipmentData as $key => $data) {
                if(isset($data['html'])){
                    $data['html'] = str_replace('__qrCode__','',$data['html']);
                }
                $this->writeHTML($key, $data, ($data["new_line"] ?? true), false, true, false, '');
            }
            $pdfTitle []= " #" . $shipment->getIncrementId();
        }
        $this->_pdf->setTitle(__('Little Flora Shipment' . (count($pdfTitle) > 1 ? "s" : "" ))
            . implode(",", $pdfTitle)
        );
        return $this->_pdf;
    }

    protected function _getShippingDescription(Order $order, array $config = [])
    {
        $description = parent::_getShippingDescription($order);
        if (isset($this->_orderData["tacking"])) {
            /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection $tracking */
            $tracking = $this->_orderData["tacking"];
            foreach ($tracking->getItems() as $track) {
                $description .= '<p>Title : ' . $track->getTitle() . '<br />';
                $description .= 'Tracking Number : ' . $track->getTrackNumber() . '<br />';
                $description .= 'Carrier : ' . $track->getCarrierCode() . '<br />';
                if ($track->getDescription()) {
                    $description .= '<br />' . $track->getDescription();
                }
                $description .= '</p>';
            }
        }
        return $description;
    }
}
