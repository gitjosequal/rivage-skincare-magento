<?php
namespace XP\OrderPdf\Model\Config\Pool;

use XP\OrderPdf\Model\ConfigInterface;

class OrderInfoConfig extends AbstractPool implements ConfigInterface
{
    const XML_PATH = self::XML_PATH_XP_PDF . '/order_info/';

    /**
     * @inheirtDoc
     */
    public function getPDFConfig(): array
    {
        $this->setBorderConfig(self::XML_PATH);
        $this->setTbl(
            "margin-top: 35px; width: 100%; line-height: 0; padding: 15px 10px;{$this->getBorder()}"
            . "background-color: {$this->getConfig(self::XML_PATH . 'bg_color')};"
            . " color: {$this->getConfig(self::XML_PATH . 'text_color')};"
        );
        $this->setNewLine($this->getConfig(self::XML_PATH . 'new_line'));
        return $this->getData();
    }
}
