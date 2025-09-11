<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Block\Json;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;

class Downloadfile extends Field
{
    /**
     * @var string
     */
    protected $jsonInitiationtUrl;

    /**
     * @var string
     */
    protected $jsonAccessUrl;

    /**
     * Constructor for Downloadfile block.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsonInitiationUrl = $urlBuilder->getUrl('czgtmextension/json/initiate');
        $this->jsonAccessUrl = $urlBuilder->getUrl('czgtmextension/json/access');
        $this->setTemplate('Rivage_GtmExtension::json/export_file.phtml');
    }

    /**
     * Retrieves the URL to initiate JSON items.
     *
     * @return string
     */
    public function getUrlToInitiateJsonItems()
    {
        return $this->jsonInitiationUrl;
    }

    /**
     * Retrieves the URL to access JSON items.
     *
     * @return string
     */
    public function getUrlToAccessJsonItems()
    {
        return $this->jsonAccessUrl;
    }

    /**
     * Render the HTML content for the custom configuration field element.
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $customHtml = $this->_toHtml();

        return $customHtml;
    }
}
