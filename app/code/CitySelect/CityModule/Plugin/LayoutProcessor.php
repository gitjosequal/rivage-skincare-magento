<?php

namespace CitySelect\CityModule\Plugin;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use function PHPUnit\Framework\assertIsCallable;

class LayoutProcessor extends \Magento\Framework\View\Element\Template
{
    protected $storeManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }
    
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) { 
        
        if($this->getStoreId() != 11 && $this->getStoreId() != 12){
            return $result;
        }else{
            
            //For shipping form
            $result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['city'] = [
                'component' => 'Magento_Ui/js/form/element/select',
                'config' => [
                    'customScope' => 'shippingAddress',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/select',
                    'id' => 'drop-down',
                ],
                'dataScope' => 'shippingAddress.city',
                'label' => __('City'),
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [],
                'sortOrder' => 70,
                'id' => 'drop-down',
                'options' => $this->getCitiesDropdown()
            ];
    
            //For Billing Form
            foreach ($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                     ['payment']['children']['payments-list']['children'] as $key => $payment) {
                if (isset($payment['children']['form-fields']['children']['city'])) {
                    $result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                    ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                    ['city'] = [
                        'component' => 'Magento_Ui/js/form/element/select',
                        'config' => [
                            'customScope' => 'shippingAddress',
                            'template' => 'ui/form/field',
                            'elementTmpl' => 'ui/form/element/select',
                            'id' => 'drop-down',
                        ],
                        'label' => __('City'),
                        'provider' => 'checkoutProvider',
                        'visible' => true,
                        'validation' => [],
                        'sortOrder' => 70,
                        'id' => 'drop-down',
                        'options' => $this->getCitiesDropdown()
                    ];
                }
            }
    
            return $result;

            
        }
        
    }

    public function getCitiesDropdown()
    {
        if($this->getStoreId() == 12){
            return [
            ['value' => "amman", "label" => "عمان", "is_default" => true], 
            ['value' => "zarqa", "label" => "الزرقاء"],
            ['value' => "irbid", "label" => "اربد"], 
            ['value' => "alkarak", "label" => "الكرك"], 
            ['value' => "ramtha", "label" => "الرمثا"], 
            ['value' => "jerash", "label" => "جرش"], 
            ['value' => "ajloun", "label" => "عجلون"], 
            ['value' => "aqaba", "label" => "العقبة"], 
            ['value' => "ma'an", "label" => "معان"], 
            ['value' => "madaba", "label" => "مادبا"], 
            ['value' => "salt", "label" => "السلط"], 
            ['value' => "mafrak", "label" => "المفرق"],
            ['value' => "tafilah", "label" => "الطفيلة"]];
        }else{
            return [
            ['value' => "amman", "label" => "Amman", "is_default" => true], 
            ['value' => "zarqa", "label" => "Az Zarqa"],
            ['value' => "irbid", "label" => "Irbid"], 
            ['value' => "alkarak", "label" => "Al Karak"], 
            ['value' => "ramtha", "label" => "Ar Ramtha"], 
            ['value' => "jerash", "label" => "Jerash"], 
            ['value' => "ajloun", "label" => "Ajloun"], 
            ['value' => "aqaba", "label" => "Aqaba"], 
            ['value' => "ma'an", "label" => "Ma'an"], 
            ['value' => "madaba", "label" => "Madaba"], 
            ['value' => "salt", "label" => "Salt"],
            ['value' => "almafrak", "label" => "Al Mafrak"],
            ['value' => "tafilah", "label" => "Tafilah"]];
        }
    }

}