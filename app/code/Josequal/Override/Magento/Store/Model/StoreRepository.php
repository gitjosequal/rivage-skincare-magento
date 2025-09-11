<?php
namespace Josequal\Override\Magento\Store\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Config;
use Magento\Framework\Exception\NoSuchEntityException;

class StoreRepository extends \Magento\Store\Model\StoreRepository
{

    /**
     * @var Config
     */
    private $appConfig;

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        
        $id = $id == 8 ? 13 : $id;
        
        if (isset($this->entitiesById[$id])) {
            return $this->entitiesById[$id];
        }
        
        
        $storeData = $this->getAppConfig()->get('scopes', "stores/$id", []);
        $store = $this->storeFactory->create([
            'data' => $storeData
        ]);

        if ($store->getId() === null) {
            throw new NoSuchEntityException(
                __("The store that was requested wasn't found. Verify the store and try again. " . $id)
            );
        }

        $this->entitiesById[$id] = $store;
        $this->entities[$store->getCode()] = $store;
        return $store;
    }

    /**
     * Retrieve application config.
     *
     * @deprecated 100.1.3
     * @return Config
     */
    private function getAppConfig()
    {
        if (!$this->appConfig) {
            $this->appConfig = ObjectManager::getInstance()->get(Config::class);
        }
        return $this->appConfig;
    }

}