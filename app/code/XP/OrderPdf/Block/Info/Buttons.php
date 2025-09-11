<?php
namespace XP\OrderPdf\Block\Info;

use Magento\Customer\Model\Context;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Sales\Model\Order;
use XP\OrderPdf\Model\Pdf\AbstractPdf;

class Buttons extends \Magento\Sales\Block\Order\Info\Buttons
{
    protected string $_path = 'xporderpdf/order/print';

    /**
     * Get url for printing order
     *
     * @param Order $order
     * @return string
     */
    public function getPrintUrl($order)
    {
        if (!$this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl($this->getPrintPath(), ['order_id' => $order->getId()]);
        }
        return $this->getUrl($this->getPrintPath(), ['order_id' => $order->getId()]);
    }

    /**
     * @return string
     */
    public function getPrintPath()
    {
        return $this->_path;
    }
}
