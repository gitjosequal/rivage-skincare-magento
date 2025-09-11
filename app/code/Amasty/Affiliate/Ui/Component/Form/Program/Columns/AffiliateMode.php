<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Ui\Component\Form\Program\Columns;

use Magento\Framework\Module\Manager;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

class AffiliateMode extends Field
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Manager $manager,
        $components,
        array $data = []
    ) {
        $this->manager = $manager;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepare()
    {
        $config = $this->getData('config');

        if (!$this->manager->isEnabled('Amasty_AffiliateTierCommissions')) {
            $config['additionalInfo'] = 'The Constant Commission mode is available by default. '
                . 'Additionally, the Tier Commission mode is provided as part of an active product subscription'
                . ' or support subscription. To upgrade and access this functionality, please follow the '
                . "<a href=https://amasty.com/amcustomer/account/products/?utm_source=extension"
                . "&utm_medium=backend&utm_campaign=upgrade_affiliate target='_blank'>link.</a>";

            $this->setData('config', $config);
        }

        parent::prepare();
    }
}
