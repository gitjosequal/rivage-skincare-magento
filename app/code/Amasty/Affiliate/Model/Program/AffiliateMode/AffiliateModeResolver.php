<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Model\Program\AffiliateMode;

use Amasty\Affiliate\Api\Data\ProgramInterface;
use Amasty\Affiliate\Model\Program\AffiliateMode\CommissionProcessor\CommissionProcessorInterface;
use Amasty\Affiliate\Model\Program\AffiliateMode\CommissionProcessor\ConstantCommissionProcessorFactory;
use Amasty\Affiliate\Model\Program\AffiliateMode\CommissionProcessor\ConstantCommissionProcessor;

class AffiliateModeResolver
{
    /**
     * @var CommissionProcessorInterface[]
     */
    private $processors;

    /**
     * @var ConstantCommissionProcessor
     */
    private $defaultProcessor;

    public function __construct(
        ConstantCommissionProcessorFactory $constantCommissionProcessorFactory,
        array $processors
    ) {
        $this->validateCommissionProcessors($processors);
        $this->processors = $processors;
        $this->defaultProcessor = $constantCommissionProcessorFactory->create();
    }

    public function getCommissionProcessor(ProgramInterface $program): CommissionProcessorInterface
    {
        if (isset($this->processors[$program->getAffiliateMode()])) {
            return $this->processors[$program->getAffiliateMode()];
        } else {
            //in case when extension with additional processor is disabled
            return $this->defaultProcessor;
        }
    }

    private function validateCommissionProcessors(array $processors): void
    {
        foreach ($processors as $processor) {
            if (!($processor instanceof CommissionProcessorInterface)) {
                throw new \InvalidArgumentException(
                    'Processor "' . $processor . '" must be instance of ' . CommissionProcessorInterface::class
                );
            }
        }
    }
}
