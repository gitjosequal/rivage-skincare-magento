<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{

    /** @var CustomerSetupFactory $customerSetupFactory */
    protected $customerSetupFactory;

    /** @var BookmarkInterfaceFactory $bookmarkFactory */
    protected $bookmarkFactory;

    /** @var BookmarkRepositoryInterface $bookmarkRepository */
    protected $bookmarkRepository;

    /** @var Data $jsonHelper */
    protected $jsonHelper;

    /** @var \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry */
    protected $indexerRegistry;

    /**
     * UpgradeData constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Ui\Api\Data\BookmarkInterfaceFactory $bookmarkFactory
     * @param \Magento\Ui\Api\BookmarkRepositoryInterface $bookmarkRepository
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Ui\Api\Data\BookmarkInterfaceFactory $bookmarkFactory,
        \Magento\Ui\Api\BookmarkRepositoryInterface $bookmarkRepository,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->bookmarkRepository   = $bookmarkRepository;
        $this->bookmarkFactory      = $bookmarkFactory;
        $this->indexerRegistry      = $indexerRegistry;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $version = $context->getVersion();
        if (version_compare($version, '1.0.0', '<=')) {
            /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup  = $this->customerSetupFactory->create(['setup' => $setup]);

            $customerSetup->addAttribute(\Magento\Customer\Model\Customer::ENTITY, 'banned_till', [
                'label'            => __('Is Banned to write review till'),
                'input'            => 'date',
                'type'             => 'varchar',
                'required'         => false,
                'visible'          => true,
                'sort_order'       => 1000,
                'position'         => 1000,
                'system'           => 0,
                'visible_on_front' => 0,
                'group'            => '',
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => false,
            ]);
            $attribute = $customerSetup->getEavConfig()
                ->clear()
                ->getAttribute(\Magento\Customer\Model\Customer::ENTITY, 'banned_till');
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute->setData('used_in_forms', ['adminhtml_customer']);
            $attribute->save();
            $this->updateBookmarks();
            $this->invalidateIndexer();
        }
        if (version_compare($version, '1.2.1', '<=')) {
            /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
            $customerSetup  = $this->customerSetupFactory->create(['setup' => $setup]);
            $oldTableName = $customerSetup->getAttributeTable(\Magento\Customer\Model\Customer::ENTITY, 'banned_till');

            if (strpos($oldTableName, 'datetime') === false) {
                $attributeId = $customerSetup->getAttributeId(\Magento\Customer\Model\Customer::ENTITY, 'banned_till');
                $connection = $setup->getConnection();
                $select = $connection->select()->from($oldTableName)->where("attribute_id = {$attributeId}");
                $oldData = $setup->getConnection()->fetchAll($select);
                $newData = [];

                foreach ($oldData as $item) {
                    unset($item['value_id']);
                    $item['value'] = preg_match('/([0-9]{1,2})\/([0-9]{2})\/([0-9]{4})/', $item['value']) ? date('Y-m-d H:i:s', strtotime($item['value'])) : '';
                    $newData[] = $item;
                }

                $customerSetup->updateAttribute(
                    \Magento\Customer\Model\Customer::ENTITY,
                    'banned_till',
                    'backend_type',
                    'datetime'
                );
                $customerSetup->updateAttribute(
                    \Magento\Customer\Model\Customer::ENTITY,
                    'banned_till',
                    'backend_model',
                    'Magento\Eav\Model\Entity\Attribute\Backend\Datetime'
                );
                $customerSetup->updateAttribute(
                    \Magento\Customer\Model\Customer::ENTITY,
                    'banned_till',
                    'frontend_model',
                    'Magento\Eav\Model\Entity\Attribute\Frontend\Datetime'
                );

                if (!empty($newData)) {
                    $newTableName = $customerSetup->getAttributeTable(\Magento\Customer\Model\Customer::ENTITY, 'banned_till');
                    $connection->insertMultiple($newTableName, $newData);
                    $connection->delete($oldTableName, "attribute_id = {$attributeId}");
                }
                $this->invalidateIndexer();
            }
        }
    }

    /**
     *
     */
    protected function updateBookmarks()
    {
        /** @var \Magento\Ui\Model\Bookmark $bookmark */
        $bookmark = $this->bookmarkFactory->create();
        $listingConfig = $bookmark->getCollection()
            ->addFieldToFilter('namespace', ['eq' => 'customer_listing']);

        /** @var \Magento\Ui\Model\Bookmark $config */
        foreach ($listingConfig as $config) {
            $params = $config->getConfig();
            if (isset($params['views']) && isset($params['views']['default']) && isset($params['views']['default']['data'])) {
                $settings = $params['views']['default']['data'];
                $updatedSettings = $this->setBannedTillVisible($settings);
                $params['views']['default']['data'] = $updatedSettings;
            } elseif (isset($params['current'])) {
                $settings = $params['current'];
                $updatedSettings = $this->setBannedTillVisible($settings);
                $params['current'] = $updatedSettings;
            } else {
                continue;
            }
            $config->setConfig($this->jsonHelper->jsonEncode($params));
            $config->save();
        }
    }

    /**
     * @param $settings
     * @return array
     */
    public function setBannedTillVisible($settings)
    {
        $settings['columns']['banned_till'] = [
            'visible' => true,
            'sorting' => false
        ];
        $positions = $settings['positions'];
        if (isset($positions['banned_till'])) {
            unset($positions['banned_till']);
        }
        $positions = array_keys($positions);
        array_splice($positions, count($positions) - 1, 0, 'banned_till');
        $settings['positions'] = array_flip($positions);
        return $settings;
    }

    public function invalidateIndexer()
    {
        $indexer = $this->indexerRegistry->get(\Magento\Customer\Model\Customer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->invalidate();
    }
}
