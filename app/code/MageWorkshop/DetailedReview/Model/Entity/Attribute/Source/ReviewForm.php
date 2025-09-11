<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Model\Entity\Attribute\Source;

class ReviewForm extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /** @var \MageWorkshop\DetailedReview\Helper\Attribute $helper */
    protected $helper;

    /**
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $helper
     */
    public function __construct(\MageWorkshop\DetailedReview\Helper\Attribute $helper)
    {
        $this->helper = $helper;
    }

    public function getAllOptions()
    {
        if (empty($this->_options)) {
            $options = [[
                'label' => 'Use Parent',
                'value' => '0'
            ]];

            /** @var \Magento\Eav\Api\Data\AttributeSetInterface $attributeSet */
            foreach ($this->helper->getAttributeSets() as $attributeSet) {
                $options[] = [
                    'label' => $attributeSet->getAttributeSetName(),
                    'value' => $attributeSet->getAttributeSetId()
                ];
            }

            $this->_options = $options;
        }

        return $this->_options;
    }
}
