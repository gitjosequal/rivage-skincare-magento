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
namespace FME\CurrencySwitcher\Model\ResourceModel\CurrencySwitcher;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
/**
 *  Class Collection
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    /**
     *  _construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'FME\CurrencySwitcher\Model\CurrencySwitcher',
            'FME\CurrencySwitcher\Model\ResourceModel\CurrencySwitcher'
        );
    }
}
