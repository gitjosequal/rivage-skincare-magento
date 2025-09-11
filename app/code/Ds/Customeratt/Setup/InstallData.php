<?php

namespace Ds\Customeratt\Setup;
 
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
 

    protected $customerSetupFactory;
    private $attributeSetFactory;
    
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
    
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
         
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
         
        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
		
		
		$customerSetup->addAttribute(Customer::ENTITY, 'my_skin', [
            'type' => 'varchar',
            'label' => 'My Skin',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' =>999,
            'system' => 0,
        ]);
         
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'my_skin')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer'],['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
        ]);
         
        $attribute->save();
		
		$customerSetup->addAttribute(Customer::ENTITY, 'my_hair_skin', [
            'type' => 'varchar',
            'label' => 'My Hair Skin',
            'input' => 'text',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' =>999,
            'system' => 0,
        ]);
         
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'my_hair_skin')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer'],['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
        ]);
         
        $attribute->save();
		
    }
}