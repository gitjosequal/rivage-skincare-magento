<?php
/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */

namespace TD\MasterCard\Model\Source;

class Cctype extends \Magento\Payment\Model\Source\Cctype
{
    /**
     * Allowed credit card types
     *
     * @return string[]
     */
    public function getAllowedTypes()
    {
        return array('VI', 'MC', 'AE', 'DI', 'JCB', 'OT');
    }
}
