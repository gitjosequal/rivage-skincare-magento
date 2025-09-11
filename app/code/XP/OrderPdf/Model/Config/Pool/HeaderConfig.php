<?php
namespace XP\OrderPdf\Model\Config\Pool;

use XP\OrderPdf\Model\ConfigInterface;

class HeaderConfig extends AbstractPool implements ConfigInterface
{
    /**
     * @inheirtDoc
     */
    public function getPDFConfig(): array
    {
        $this->setBorderConfig(self::XML_PATH);
        $this->setTbl("width: 100%; padding-left: 0; padding-right: 0;{$this->getBorder()}");
        $this->setTbl($this->getTbl() . "color: {$this->getConfig(self::XML_PATH . 'header_text_color')};");
        $this->setTd("line-height: 1; width: 40%;");
        $additionalConfig = $this->getAdditionalConfig();
        $additionalConfig["put_order_id"] =[
            "invoice" => $this->getConfig("sales_pdf/invoice/put_order_id"),
            "shipment" => $this->getConfig("sales_pdf/shipment/put_order_id"),
            "creditmemo" => $this->getConfig("sales_pdf/creditmemo/put_order_id"),
        ];
        $this->setNewLine($this->getConfig(self::XML_PATH . 'new_line'));
        return $this->getData();
    }
}
