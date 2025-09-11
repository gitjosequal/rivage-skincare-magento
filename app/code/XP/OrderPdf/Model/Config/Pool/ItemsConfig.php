<?php
namespace XP\OrderPdf\Model\Config\Pool;

use XP\OrderPdf\Model\ConfigInterface;

class ItemsConfig extends AbstractPool implements ConfigInterface
{
    const XML_PATH = self::XML_PATH_XP_PDF . '/items/';

    /**
     * @inheirtDoc
     */
    public function getPDFConfig(): array
    {
        $this->setBorderConfig(self::XML_PATH);
        $this->setTbl("width: 100%; padding: 10px;{$this->getBorder()}");
        $this->setTr("{$this->getBorder()}");
        $this->setTd(
            "{$this->getBorder()}color: {$this->getConfig(self::XML_PATH . 'column_text_color')};"
            . "background-color: {$this->getConfig(self::XML_PATH . 'column_bg_color')};"
        );
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
