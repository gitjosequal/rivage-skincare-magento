<?php
namespace XP\OrderPdf\Block\Adminhtml;

use Magento\Store\Model\ScopeInterface;
use XP\OrderPdf\Model\Pdf\AbstractPdf;

class PrintOrderButton extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration -- Magento 2 core use
    protected function _construct()
    {
        // Add order print button
        $this->addButton(
            'print_order',
            [
                'label'     => __('Print PDF Order'),
                'onclick'   => 'window.open(\'' . $this->getPrintOrderUrl("order_id/" . $this->getOrderId()) . '\', \'_blank\')',
                'class'     => 'print'
            ]
        );
        // Add order print button
        $this->addButton(
            'print_order_arabic',
            [
                'label'     => __('Arabic Print'),
                'onclick'   => 'window.open(\'' . $this->getPrintOrderUrl("locale_param/ar/order_id/" . $this->getOrderId()) . '\', \'_blank\')',
                'class'     => 'print'
            ]
        );

        // Add USD button
        if ($this->_scopeConfig->getValue(
                AbstractPdf::XML_PATH_XP_PDF . '/general/enable_usd_invoice',
                ScopeInterface::SCOPE_STORE
            )
        ) {
            // Add print Invoice button
            $this->addButton(
                'print_usd_order',
                [
                    'label'     => __('Print USD'),
                    'onclick'   => 'window.open(\'' . $this->getPrintOrderUrl(
                        "order_id/{$this->getOrderId()}/usd/1"
                        ) . '\', \'_blank\')',
                    'class'     => 'print'
                ]
            );
        }

        parent::_construct();
    }

    /**
     * Ship URL getter
     *
     * @param string|null $param
     * @return string
     */
    public function getPrintOrderUrl(?string $param = ''): string
    {
        return $this->getUrl('xporderpdf/order/printaction/' . $param);
    }

    /**
     * @return integer
     */
    public function getOrderId()
    {
        return $this->coreRegistry->registry('sales_order')->getId();
    }
}
