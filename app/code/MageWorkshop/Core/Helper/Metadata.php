<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Core\Helper;

class Metadata extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CACHE_KEY_MAGENTO_VERSION = 'mageworkshop_magento_version';

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    private $cache;

    /**
     * @var string $magentoVersion
     */
    private $magentoVersion;

    /**
     * Serializer constructor.
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\CacheInterface $cache,
        // Need context to be able to access this helper in templates
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->productMetadata = $productMetadata;
        $this->cache = $cache;
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        if (null === $this->magentoVersion) {
            if (!$magentoVersion = $this->cache->load(self::CACHE_KEY_MAGENTO_VERSION)) {
                $magentoVersion = $this->productMetadata->getVersion();
                $this->cache->save($magentoVersion, self::CACHE_KEY_MAGENTO_VERSION);
            }

            $this->magentoVersion = $magentoVersion;
        }

        return $this->magentoVersion;
    }

    /**
     * @param string $magentoVersion
     * @return bool
     */
    public function isMagentoVersionEqualOrGreater($magentoVersion)
    {
        return version_compare($this->getMagentoVersion(), $magentoVersion) >= 0;
    }
}
