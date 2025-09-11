<?php
namespace XP\OrderPdf\Model\Config\Pool;

use XP\OrderPdf\Model\ConfigInterface;

class AddressConfig extends AbstractPool implements ConfigInterface
{
    const XML_PATH = self::XML_PATH_XP_PDF . '/addresses/';

    /**
     * @inheirtDoc
     */
    public function getPDFConfig(): array
    {
        $this->setBorderConfig(self::XML_PATH);
        $this->setTbl("margin-top: -2px; padding: 10px; width: 100%;{$this->getBorder()}");
        $this->setTr("color: {$this->getConfig(self::XML_PATH . 'heading_text_color')}; padding-top: 5px; ");
        $this->setTr("{$this->getTr()}{$this->getBorder()}background-color: {$this->getConfig(self::XML_PATH . 'heading_bg_color')};");
        $this->setTd("{$this->getBorder()}");
        $this->setTrContent("{$this->getBorder()}");
        $this->setTdContent("{$this->getBorder()}");
        if (!$this->getConfig(self::XML_PATH . 'default_text')) {
            $this->setTrContent(
                $this->getTrContent()
                . "color: {$this->getConfig(self::XML_PATH . 'content_text_color')};"
                . " background-color: {$this->getConfig(self::XML_PATH . 'content_bg_color')};"
            );
        }
        $this->setNewLine($this->getConfig(self::XML_PATH . 'new_line'));
        return $this->getData();
    }
}
