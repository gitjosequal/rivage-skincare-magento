<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Model\Config\Source;

class Groups
{
    /** @var array $options */
    protected $options;

    /** @var \Magento\Customer\Api\GroupManagementInterface $groupManagement */
    protected $groupManagement;

    /** @var \Magento\Framework\Convert\DataObject $converter */
    protected $converter;

    /**
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Framework\Convert\DataObject $converter
     */
    public function __construct(
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Framework\Convert\DataObject $converter
    ) {
        $this->groupManagement = $groupManagement;
        $this->converter = $converter;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $groups = array_merge([$this->groupManagement->getNotLoggedInGroup()], $this->groupManagement->getLoggedInGroups());
            $this->options = $this->converter->toOptionArray($groups, 'id', 'code');
        }
        return $this->options;
    }
}