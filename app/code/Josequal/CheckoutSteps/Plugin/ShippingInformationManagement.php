<?php

namespace Josequal\CheckoutSteps\Plugin;

use Magento\Quote\Api\CartRepositoryInterface;

class ShippingInformationManagement
{
    public $cartRepository;

    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    public function beforeSaveAddressInformation($subject, $cartId, $addressInformation)
    {
        $quote = $this->cartRepository->getActive($cartId);

        $buildingNumber = $addressInformation->getShippingAddress()->getExtensionAttributes()->getBuildingNumber();
        $quote->setBuildingNumber($buildingNumber);
        $this->cartRepository->save($quote);
        return [$cartId, $addressInformation];
    }
}