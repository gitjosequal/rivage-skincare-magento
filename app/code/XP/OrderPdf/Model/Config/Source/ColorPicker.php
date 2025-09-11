<?php
namespace XP\OrderPdf\Model\Config\Source;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class ColorPicker extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();

        $html .= '<script type="text/x-magento-init">
                {
                    "#'. $element->getHtmlId() . '": {
                        "XP_OrderPdf/js/color-picker": {
                            "color":"' . $element->getData("value") . '"
                        }
                    }
                }
                </script>';
        return $html;
    }
}
