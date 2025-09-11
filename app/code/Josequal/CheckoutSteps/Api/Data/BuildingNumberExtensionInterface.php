<?php
namespace Josequal\CheckoutSteps\Api\Data;

interface BuildingNumberExtensionInterface
{
    /**
     * @return string
     */
    public function getBuildingNumber();

    /**
     * @param string $buildingNumber
     * @return void
     */
    public function setBuildingNumber($buildingNumber);
}
