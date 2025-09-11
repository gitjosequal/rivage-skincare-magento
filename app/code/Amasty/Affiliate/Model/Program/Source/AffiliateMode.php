<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\Program\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AffiliateMode implements OptionSourceInterface
{
    public const CONSTANT_COMMISSION = 0;

    public function toOptionArray(): array
    {
        return [['value' => self::CONSTANT_COMMISSION, 'label' => __('Constant Commission')]];
    }
}
