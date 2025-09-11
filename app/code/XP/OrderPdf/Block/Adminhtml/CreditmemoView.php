<?php
namespace XP\OrderPdf\Block\Adminhtml;

class CreditmemoView extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\View
{
    /**
     * Add & remove control buttons
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        if ($this->getCreditmemo()->getId()) {
            $this->buttonList->add(
                'orderpdf_print',
                [
                    'label' => __('OrderPDF Print'),
                    'class' => 'print',
                    'onclick' => 'setLocation(\'' . $this->getOrderpdfPrintUrl() . '\')'
                ]
            );
        }
    }
    /**
     * Get print url
     *
     * @return string
     */
    public function getOrderpdfPrintUrl()
    {
        return $this->getUrl('xporderpdf/creditmemo/printaction/', ['creditmemo_id' => $this->getCreditmemo()->getId()]);
    }
}
