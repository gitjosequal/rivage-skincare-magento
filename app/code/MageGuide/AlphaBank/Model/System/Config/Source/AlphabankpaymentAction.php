<?php
namespace MageGuide\AlphaBank\Model\System\Config\Source;
class AlphabankpaymentAction implements \Magento\Framework\Option\ArrayInterface
{
	const AUTHORIZE 		= '00';
	const SALE 				= '02';
	public function toOptionArray()
	{
		return array(
    		array('value' => self::AUTHORIZE, 'label' => __('Authorize Only')),
		    array('value' => self::SALE, 'label' => __('Sale'))
  		);
 	}
}
