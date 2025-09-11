<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Ui\Component\MassAction\Ban;

use JsonSerializable;

class Options implements JsonSerializable
{
    /** @var array $options */
    protected $options;

    /** @var array $data */
    protected $data;

    /** @var \Magento\Framework\UrlInterface $urlBuilder */
    protected $urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string $urlPath
     */
    protected $urlPath;

    /**
     * Additional params for subactions
     *
     * @var array $additionalData
     */
    protected $additionalData = [];

    /**
     * Param name for subactions
     *
     * @var string $paramName
     */
    protected $paramName;

    /** @var \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper */
    protected $banHelper;

    /**
     * Options constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper,
        array $data = []
    ) {
        $this->data = $data;
        $this->banHelper = $banHelper;
        $this->urlBuilder = $urlBuilder;
        $this->urlPath = array_key_exists('urlPath', $data) ? $data['urlPath'] : null;
        $this->paramName = array_key_exists('paramName', $data) ? $data['paramName'] : null;
    }

    /**
     * Get action options
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        if ($this->options === null) {
            foreach ($this->banHelper->getBanPeriods() as $value => $label) {
                $this->options[$value] = [
                    'type' => 'ban_customer_' . $value,
                    'label' => $label,
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$value]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $value]
                    );
                }

                $this->options[$value] = array_merge_recursive(
                    $this->options[$value],
                    $this->additionalData
                );
            }
            $this->options = array_values($this->options);
        }
        return $this->options;
    }
}