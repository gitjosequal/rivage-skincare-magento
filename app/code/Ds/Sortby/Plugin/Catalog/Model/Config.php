<?php

namespace Ds\Sortby\Plugin\Catalog\Model;

class Config
{
    public function afterGetAttributeUsedForSortByArray(
    \Magento\Catalog\Model\Config $catalogConfig,
    $options
    ) {
	
        unset($options['name']);
        unset($options['price']);
        $options['low_to_high'] = __('Price (Low to High)');
	    $options['high_to_low'] = __('Price (High to Low)');
		$options['a_to_z'] = __('Product Name (A-Z)');
        $options['z_to_a'] = __('Product Name (Z-A)');
        return $options;

    }

}