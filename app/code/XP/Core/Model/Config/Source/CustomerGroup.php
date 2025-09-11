<?php
namespace XP\Core\Model\Config\Source;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Remove ALL Groups value.
 */
class CustomerGroup extends \Magento\Customer\Model\Customer\Source\Group
{
    /**
     * Return array of customer groups
     *
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        $optionArray = parent::toOptionArray();
        if (!$optionArray) {
            return $optionArray;
        }

        $customerGroups = $optionArray;
        foreach ($optionArray as $key => $option) {
            if ($option["value"] != (string)GroupInterface::CUST_GROUP_ALL) {
                continue;
            }
            unset($customerGroups[$key]);
        }

        return $customerGroups;
    }
}
