<?php
namespace XP\OrderPdf\Block\Adminhtml;

use Magento\Sales\Block\Adminhtml\Order\Invoice\View;
use Magento\Store\Model\ScopeInterface;
use XP\OrderPdf\Model\Pdf\AbstractPdf;

class InvoiceView extends View
{
    protected function _construct()
    {
        parent::_construct();
        if ($this->getInvoice()->getId()) {
            $this->buttonList->add(
                'orderpdf_print',
                [
                    'label' => __('OrderPDF Print'),
                    'class' => 'print',
                    'onclick'   => 'window.open(\'' . $this->getOrderPdfPrintUrl() . '\', \'_blank\')',
                ]
            );
            $invoice = $this->getInvoice();
            if ($invoice->getOrder()->getOrderCurrency()->getCode() != "USD"
               && $this->_scopeConfig->getValue(
                   AbstractPdf::XML_PATH_XP_PDF . '/general/enable_usd_invoice',
                   ScopeInterface::SCOPE_STORE
                )
            ) {
                // Add print Invoice button
                $this->addButton(
                    'print_usd_invoice',
                    [
                        'label'     => __('Print USD'),
                        'onclick'   => 'window.open(\''
                            . $this->getOrderPdfPrintUrl([
                                "usd" => 1,
                                "render_tmpl" => "invoice"
                            ])
                            . '\', \'_blank\')',
                        'class'     => 'print'
                    ]
                );
            }
        }
    }
    /**
     * Get print url
     *
     * @return string
     */
    public function getOrderPdfPrintUrl($additionalParams = [])
    {
        return $this->getUrl('xporderpdf/invoice/printaction/',
            array_merge(['invoice_id' => $this->getInvoice()->getId()], $additionalParams)
        );
    }
}
