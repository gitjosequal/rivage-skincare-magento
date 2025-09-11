<?php
namespace XP\OrderPdf\Model\Config\Pool;

use XP\OrderPdf\Model\ConfigInterface;

class TotalsConfig extends AbstractPool implements ConfigInterface
{
    const XML_PATH = self::XML_PATH_XP_PDF . '/totals/';

    /**
     * @inheirtDoc
     */
    public function getPDFConfig(): array
    {
        if (!$this->getConfig(self::XML_PATH . 'use_default_config')) {
            $this->setBorderConfig(self::XML_PATH);
            $this->setTd(
                "color: {$this->getConfig(self::XML_PATH . 'text_color')};"
                . " background-color: {$this->getConfig(self::XML_PATH . 'bg_color')};"
            );
            $this->setTd("{$this->getBorder()}");
            $grandTotalStyle =  "color: {$this->getConfig(self::XML_PATH . 'grandtotal_color')};"
                . " background-color: {$this->getConfig(self::XML_PATH . 'grandtotal_bg_color')};";
            if ($this->getConfig(self::XML_PATH . 'grandtotal_border')) {
                $grandTotalStyle .= " border: 1px solid {$this->getConfig(self::XML_PATH . 'grandtotal_border_color')}; ";
            }
            $this->setGrandTotalStyle($grandTotalStyle);
        }
        $this->setTbl("margin-top: 45px; width: 100%; padding: 10px;");
        return $this->getData();
    }
}
