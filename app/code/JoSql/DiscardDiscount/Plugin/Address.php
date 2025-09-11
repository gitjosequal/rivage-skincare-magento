<?php
namespace JoSql\DiscardDiscount\Plugin;

class Address
{
        public function afterLoadAttributeOptions(\Magento\SalesRule\Model\Rule\Condition\Address $subject)
    {
        $attributes = $subject->getAttributeOption();
        $attributes['base_subtotal_with_discount'] = __('Subtotal With Discount');
        $subject->setAttributeOption($attributes);
        return $subject;
    }

     public function afterGetInputType(\Magento\SalesRule\Model\Rule\Condition\Address $subject)
    {
        switch ($subject->getAttribute()) {
            case 'base_subtotal':
            case 'base_subtotal_with_discount':
            case 'weight':
            case 'total_qty':
                return 'numeric';

            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
                return 'select';
        }

        return 'string';
    }
}