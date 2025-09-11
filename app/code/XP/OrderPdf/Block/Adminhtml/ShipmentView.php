<?php
namespace XP\OrderPdf\Block\Adminhtml;

class ShipmentView extends \Magento\Shipping\Block\Adminhtml\View
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->getShipment()->getId()) {
            $this->buttonList->add(
                'xporderpdf_print',
                [
                    'label' => __('OrderPDF Print'),
                    'class' => 'save',
                    'onclick' => 'setLocation(\'' . $this->getOrderPdfPrintUrl() . '\')'
                ]
            );
        }
    }
    /**
     * @return string
     */
    public function getOrderPdfPrintUrl()
    {
        return $this->getUrl('xporderpdf/shipment/printaction', ['shipment_id' => $this->getShipment()->getId()]);
    }
}
