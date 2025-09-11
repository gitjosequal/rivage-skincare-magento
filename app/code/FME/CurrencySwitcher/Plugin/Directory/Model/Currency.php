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
namespace FME\CurrencySwitcher\Plugin\Directory\Model;

use Magento\Framework\Exception\InputException;
use Magento\Directory\Model\Currency as CurrencyInterface;
use FME\CurrencySwitcher\Helper\Data as Helper;

/**
 * Currency Plugin
 */
class Currency
{
    protected $helper;
    /**
     * Initialize Plugin
     *
     * @param Helper $helper
     */
    public function __construct(
        Helper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Convert and Round Price
     *
     * @param CurrencyInterface $subject
     * @param callable $proceed
     * @param float $price
     * @param mixed $toCurrency
     * @return float|string
     */
    public function afterConvert(\Magento\Directory\Model\Currency $subject, $result)
    {
        $price = $result;
        if ($this->helper->isEnabled() == 1) {
            if ($this->helper->isEnabledRoundPrices() == 1) {
                $isRoundPricesAlgo=$this->helper->isRoundPricesAlgo();
                if ($isRoundPricesAlgo == 0) {
                    return round($price, 2);
                } else if ($isRoundPricesAlgo == 1) {
                    return ceil($price);
                } else if ($isRoundPricesAlgo == 2) {
                    $remainder = $price % 10;
                    $ceilupvalue = 10 - $remainder;
                    if ($remainder >= 5) {
                        $ceilupvalue = 10 - $remainder;
                        return $price + $ceilupvalue;
                    } else {
                        return $price - $remainder;
                    }
                } else if ($isRoundPricesAlgo == 3) {
                    $remainder = $price % 10;
                    $ceilupvalue = 10 - $remainder;
                    return $price + $ceilupvalue;
                } else if ($isRoundPricesAlgo == 4) {
                    $x = $price - floor($price);
                    if ($x >= 0.5) {
                        $roundprice = floor($price);
                        return $roundprice + 0.99;
                    } else {
                        $roundprice = floor($price);
                        return $roundprice - 0.01;
                    }
                } else if ($isRoundPricesAlgo == 5) {
                    $priceceil = floor($price);
                    return $priceceil + 0.99;
                } else if ($isRoundPricesAlgo == 6) {
                    $x = $price - floor($price);
                    if ($x >= 0.5) {
                        $roundprice = floor($price);
                        return $roundprice + 0.95;
                    } else {
                        $roundprice = floor($price);
                        return $roundprice - 0.05;
                    }
                } else {
                    $priceceil = floor($price);
                    return $priceceil + 0.95;
                }
            }
            return $price;
        }
        return $price;
    }
}
