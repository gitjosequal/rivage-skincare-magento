<?php
namespace XP\OrderPdf\Block\Adminhtml;

use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Store\Model\ScopeInterface;

/**
 * Class OrderView
 * @package XP\OrderPdf\Block\Adminhtml
 */
class OrderView extends View
{
    protected function _construct()
    {
        parent::_construct();
        // Add order print button
        $this->addButton(
            'print_order',
            [
                'label'     => __('Print PDF Order'),
                'onclick'   => 'window.open(\'' . $this->getPrintOrderUrl("order_id/{$this->getOrderId()}") . '\', \'_blank\')',
                'class'     => 'print'
            ]
        );
        // Add order print button
        $this->addButton(
            'print_order_arabic',
            [
                'label'     => __('Print Order In Arabic'),
                'onclick'   => 'window.open(\'' . $this->getPrintOrderUrl("order_id/{$this->getOrderId()}/locale_param/ar") . '\', \'_blank\')',
                'class'     => 'print'
            ]
        );
    }

    /**
     * Ship URL getter
     *
     * @param string $param
     * @return string
     */
    public function getPrintOrderUrl($param = '')
    {
        return $this->getUrl('xporderpdf/order/printaction/' . $param);
    }
}
