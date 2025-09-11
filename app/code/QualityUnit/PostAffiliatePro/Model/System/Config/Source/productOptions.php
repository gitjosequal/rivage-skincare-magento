<?php
namespace QualityUnit\PostAffiliatePro\Model\System\Config\Source;

class productOptions implements \Magento\Framework\Option\ArrayInterface {
    public function toOptionArray() {
        return array(
            array(
                    'label' => 'product ID',
                    'value' => '1'
            ),
            array(
                    'label' => 'product SKU',
                    'value' => '2'
            ),
            array(
                    'label' => 'product category',
                    'value' => '3'
            ),
            array(
                    'label' => 'customer group',
                    'value' => '4'
            )
        );
    }
}