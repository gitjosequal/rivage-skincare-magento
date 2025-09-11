<?php declare(strict_types=1);
/**
 *
 *           a88888P8
 *          d8'
 * .d8888b. 88        .d8888b. 88d8b.d8b. .d8888b. .dd888b. .d8888b.
 * 88ooood8 88        88'  `88 88'`88'`88 88ooood8 88'    ` 88'  `88
 * 88.  ... Y8.       88.  .88 88  88  88 88.  ... 88       88.  .88
 * `8888P'   Y88888P8 `88888P' dP  dP  dP `8888P'  dP       `88888P'
 *
 *           Copyright © eComero Management AB, All rights reserved.
 *
 */
namespace Ecomero\Analytics\Block\System\Config\Form\Field;

class ExchangeRates extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected $columns = [];
    protected $productCategoryRenderer;
    protected $attributeSetRenderer;
    protected $addAfter = true;
    protected $addButtonLabel;

    protected function _construct()
    {
        parent::_construct();
        $this->addButtonLabel = __('Add');
    }

    protected function _prepareToRender() : void
    {
        $this->addColumn('currency', ['label' => __('Currency')]);
        $this->addColumn('rate', ['label' => __('Exchange Rate')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
