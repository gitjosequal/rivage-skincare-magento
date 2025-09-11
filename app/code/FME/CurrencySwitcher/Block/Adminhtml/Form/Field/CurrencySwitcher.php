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
 * @category  FME
 * @package   FME_CurrencySwitcher
 * @copyright Copyright (c) 2019 FME (http://fmeextensions.com/)
 * @license   https://fmeextensions.com/LICENSE.txt
 */ 
namespace FME\CurrencySwitcher\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

/**
 * Class CurrencySwitcher
 */
class CurrencySwitcher extends AbstractFieldArray
{
    /**
     * @var Countries
     */
    protected $countryRenderer = null;

    /**
     * @var CcTypes
     */
    protected $ccTypesRenderer = null;

    /**
     * Returns renderer for country element
     *
     * @return Countries
     */
    protected function getCountryRenderer()
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                Countries::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->countryRenderer;
    }

    /**
     * Returns renderer for country element
     *
     * @return CcTypes
     */
    protected function getCcTypesRenderer()
    {
        if (!$this->ccTypesRenderer) {
            $this->ccTypesRenderer = $this->getLayout()->createBlock(
                CcTypes::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->ccTypesRenderer;
    }

    /**
     * Prepare to render
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'country_id',
            [
                'label'     => __('Country'),
                'renderer'  => $this->getCountryRenderer(),
            ]
        );
        $this->addColumn(
            'cc_types',
            [
                'label' => __('Allowed Currency'),
                'renderer'  => $this->getCcTypesRenderer(),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $country = $row->getCountryId();
        $options = [];
        if ($country) {
            $options['option_' . $this->getCountryRenderer()->calcOptionHash($country)]
                = 'selected="selected"';

            $ccTypes = $row->getCcTypes();
            // print_r($row->getCcTypes());
            // exit;
            if($ccTypes){	
				$options['option_' . $this->getCcTypesRenderer()->calcOptionHash($ccTypes)] = 'selected="selected"';
        	}
            //$options['option_' . $this->getCcTypesRenderer()->calcOptionHash($ccTypes)] = 'selected="selected"';	

        }
        $row->setData('option_extra_attrs', $options);
    }
}
