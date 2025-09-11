<?php
namespace XP\OrderPdf\Model\Config\Pool;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;


class AbstractPool extends DataObject
{
    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $_scopeConfig;

    const XML_PATH_XP_PDF = 'xp_orderpdf';
    const XML_PATH = self::XML_PATH_XP_PDF . '/general/';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(ScopeConfigInterface $scopeConfig, array $data = [])
    {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($data);
    }

    protected function setBorderConfig($path)
    {
        if ($this->getConfig($path . 'table_border')) {
            $this->setBorder(" border: 1px solid {$this->getConfig($path . 'border_color')}; ");
        }
    }

    public function setNewLine($newLine)
    {
        $this->setData('new_line', !(bool)$newLine);
    }

    /**
     * @param $path
     * @param int|string|null $store
     * @return mixed
     */
    public function getConfig($path, $store = null)
    {
        return $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
