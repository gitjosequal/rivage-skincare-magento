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
* @category FME
* @package FME_CurrencySwitcher
* @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
* @license https://fmeextensions.com/LICENSE.txt
*/
namespace FME\CurrencySwitcher\Model\Config\Source;

/**
 * Class RoundAlgo
 */
class RoundAlgo implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('Round (default)')], ['value' => 1, 'label' => __('Ceil')], ['value' => 2, 'label' => __('Round X')], ['value' => 3, 'label' => __('Ceil X')], ['value' => 4, 'label' => __('Round 0.99')], ['value' => 5, 'label' => __('Ceil 0.99')], ['value' => 6, 'label' => __('Round 0.95')], ['value' => 7, 'label' => __('Ceil 0.95')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Round (default)'), 1 => __('Ceil'), 2 => __('Round X'), 3 => __('Ceil X'), 4 => __('Round 0.99'), 5 => __('Ceil 0.99'), 6 => __('Round 0.95'), 7 => __('Ceil 0.95')];
    }
}