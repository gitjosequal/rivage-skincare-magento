<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Config\Data;

use MageWorkshop\DetailedReview\Config\Data;
use Magento\Framework\ObjectManagerInterface;

/**
 * Proxy class for \MageWorkshop\DetailedReview\Config\Data
 */
class Proxy extends Data
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $instanceName;

    /**
     * Proxied instance
     *
     * @var \Magento\Framework\Mview\Config\Data
     */
    protected $subject;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $isShared = null;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = \MageWorkshop\DetailedReview\Config\Data::class,
        $shared = true
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
        $this->isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['_subject', '_isShared'];
    }

    /**
     * Retrieve ObjectManager from global scope
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     *
     * @return void
     */
    public function __clone()
    {
        $this->subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\Framework\Mview\Config\Data
     */
    protected function _getSubject()
    {
        if (!$this->subject) {
            $this->subject = true === $this->isShared ? $this->objectManager->get(
                $this->instanceName
            ) : $this->objectManager->create(
                $this->instanceName
            );
        }
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $config)
    {
        $this->_getSubject()->merge($config);
    }

    /**
     * {@inheritdoc}
     */
    public function get($path = null, $default = null)
    {
        return $this->_getSubject()->get($path, $default);
    }
}
