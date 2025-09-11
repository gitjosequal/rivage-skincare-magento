<?php
/**
 * FME Extensions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the fmeextensions.com license that is
 * available through the world-wide-web at this URL:
 * https://www.fmeextensions.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  FME
 * @package   FME_CurrencySwitcher
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */ 
namespace FME\CurrencySwitcher\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\ObjectManagerInterface;
/**
 * Class Button
 */
class Button extends \Magento\Config\Block\System\Config\Form\Field 
{

    /**
     * Path to block template
     */
    const CHECK_TEMPLATE = 'system/config/button.phtml';

    public function __construct(
        Context $context, 
        \FME\CurrencySwitcher\Helper\Data $helper,
        \Magento\Framework\UrlInterface $url,
        $data = array()
    )
    {
        parent::__construct($context, $data);
        $this->Helper = $helper;
        $this->_url = $url;
    }

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::CHECK_TEMPLATE);
        }
        return $this;
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    /**
     * _getElementHtml
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return Html
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->addData(
            [
                'url' => $this->getUrl(),
                'html_id' => $element->getHtmlId(),
            ]
        );

        return $this->_toHtml();
    }
    /**
     * getButtonUrl
     *
     * @return string
     */
    public function getButtonUrl()
    {
        return $this->_url->getUrl("currencyswitcher/import/import");
        //return $this->Helper->getBaseUrl()."admin/currencyswitcher/import/import"; //This is your real url you want to redirect when click on button
    }
}
