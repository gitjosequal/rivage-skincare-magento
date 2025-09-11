<?php
/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */

/**
 * Image config field renderer
 */
namespace TD\MasterCard\Block\System\Config\Form\Field;

/**
 * Class Image Field
 * @method getFieldConfig()
 * @method setFieldConfig()
 */
class Image extends \Magento\Config\Block\System\Config\Form\Field 
{
    /**
     * Get selector html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '';
        if (!(string)$element->getValue()) {
            $defaultImage = $this->getViewFileUrl('TD_MasterCard::images/mastercard_logo.png');
            $html .= '<img src="' . $defaultImage . '" alt="MasterCard logo" height="50" width="85" class="small-image-preview v-middle" />';
            $html .= '<p class="note"><span>Upload a new image if you wish to replace this logo.</span></p>';
        }
        $html .= str_replace('height="22" width="22"', 'height="50"', parent::_getElementHtml($element));

        return $html;
    }
}
