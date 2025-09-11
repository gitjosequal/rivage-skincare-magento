<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Controller\Exception;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

/**
 * Class AccessDeniedException
 * @package MageWorkshop\Voting\Controller\Exception
 */
class AccessDeniedException extends LocalizedException
{
    /**
     * AccessDeniedException constructor.
     * @param Phrase|string $phrase
     * @param \Exception|null $cause
     */
    public function __construct($phrase, \Exception $cause = null)
    {
        if (is_string($phrase)) {
            $phrase = new Phrase($phrase);
        }
        parent::__construct($phrase, $cause);
    }
}