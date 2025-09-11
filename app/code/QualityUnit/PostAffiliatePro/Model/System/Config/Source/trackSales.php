<?php
namespace QualityUnit\PostAffiliatePro\Model\System\Config\Source;

class trackSales implements \Magento\Framework\Option\ArrayInterface {
    public function toOptionArray() {
        return array(
            array('label'=>'JavaScript tracking', 'value'=>'javascript'),
            array('label'=>'API tracking', 'value'=>'api')
        );
    }
}