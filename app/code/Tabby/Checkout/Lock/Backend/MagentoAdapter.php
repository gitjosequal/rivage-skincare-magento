<?php
declare(strict_types=1);

namespace Tabby\Checkout\Lock\Backend;

use Magento\Framework\Lock\Backend\Database as MagentoLockManager;
use Tabby\Checkout\Lock\LockManagerInterface;

/**
 * Adapter class to use Magento's core lock manager with Tabby's interface
 */
class MagentoAdapter implements LockManagerInterface
{
    /**
     * @var MagentoLockManager
     */
    private $magentoLockManager;

    /**
     * @param MagentoLockManager $magentoLockManager
     */
    public function __construct(MagentoLockManager $magentoLockManager)
    {
        $this->magentoLockManager = $magentoLockManager;
    }

    /**
     * Sets a lock for name
     *
     * @param string $name lock name
     * @param int $timeout How long to wait lock acquisition in seconds, negative value means infinite timeout
     * @return bool
     */
    public function lock(string $name, int $timeout = -1): bool
    {
        return $this->magentoLockManager->lock($name, $timeout);
    }

    /**
     * Releases a lock for name
     *
     * @param string $name lock name
     * @return bool
     */
    public function unlock(string $name): bool
    {
        return $this->magentoLockManager->unlock($name);
    }

    /**
     * Tests if the lock is set
     *
     * @param string $name lock name
     * @return bool
     */
    public function isLocked(string $name): bool
    {
        return $this->magentoLockManager->isLocked($name);
    }
}
