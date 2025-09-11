<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Seo\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class HideBlockBy implements ArrayInterface
{
    const HIDE_BY_CSS = 'css';

    const HIDE_BY_JS  = 'js';

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::HIDE_BY_CSS,
                'label' => __('CSS')
            ], [
                'value' => self::HIDE_BY_JS,
                'label' => __('JavaScript (hide SEO block after the page load)')
            ]
        ];
    }
}
