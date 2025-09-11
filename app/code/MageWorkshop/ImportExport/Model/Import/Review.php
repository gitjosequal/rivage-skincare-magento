<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\ImportExport\Model\Import;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\Review as ReviewModel;
use Magento\ImportExport\Model\Import;
use MageWorkshop\DetailedReview\Model\Attribute;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Review extends \Magento\ImportExport\Model\Import\AbstractEntity
{
    const ENTITY_TYPE_CODE = 'review_entity';

    const ERROR_IMAGE_DOES_NOT_EXIST = 'imageDoesNotExist';

    const EVEN_IMPORT_START = 'mageworkshop_import_start';

    const EVEN_IMPORT_COMPLETED = 'mageworkshop_import_completed';

    /**
     * Code of a primary attribute which identifies the entity group if import contains of multiple rows
     *
     * @var string $masterAttributeCode
     */
    protected $masterAttributeCode = 'entity_id';

    /**
     * @var array $_permanentAttributes
     */
    protected $_permanentAttributes = [
        'entity_id',
        'sku',
        'status_id',
        'nickname',
        'title',
        'detail'
        // 'store_id' - just leave it NULL for all stores
    ];

    /**
     * Columns mapping. Need to move this to some Mapper class once we implement export functionality
     *
     * @var array $mapping
     */
    private $mapping = [
        'entity_id' => 'review_id',
        'response'  => 'admin_response'
    ];

    /**
     * Cache customer emails to load less data while processing subsequent batches
     *
     * @var array $customerEmailsCache
     */
    private $customerEmailsCache = [];

    /**
     * @var array $productIdsCache
     */
    private $productIdsCache = [];

    /**
     * @var string $absoluteImagePath
     */
    private $absoluteImagePath;

    /**
     * @var array $imageAttributeCodes
     */
    private $imageAttributeCodes;

    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory $itemCollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     */
    private $reviewHelper;

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     */
    private $attributeHelper;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * Review constructor.
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\ImportExport\Model\ImportFactory $importFactory
     * @param \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $itemCollectionFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\ImportExport\Model\ImportFactory $importFactory,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $itemCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $data = []
    ) {
        parent::__construct($string, $scopeConfig, $importFactory, $resourceHelper, $resource, $errorAggregator, $data);
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->reviewHelper = $reviewHelper;
        $this->attributeHelper = $attributeHelper;
        $this->filesystem = $filesystem;
        $this->eventManager = $eventManager;
    }

    /**
     * Imported entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_TYPE_CODE;
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return bool
     * @throws LocalizedException
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        $this->setExistingImages($rowData, $rowNumber, true);
        return true;
    }

    /**
     * Import data rows
     *
     * @abstract
     * @return boolean
     * @throws \RuntimeException
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function _importData()
    {
        $this->eventManager->dispatch(self::EVEN_IMPORT_START);

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $reviewsToCreate = [];
            $reviewsToUpdate = [];
            $reviewsToDelete = [];
            $processedData = [];
            $behavior = '';

            foreach ($bunch as $rowNumber => $rowData) {
                if (!$this->validateRow($rowData, $rowNumber)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNumber);
                    continue;
                }

                $behavior = $this->getBehavior($rowData);

                if ($behavior === Import::BEHAVIOR_DELETE) {
                    $reviewsToDelete[] = $rowData['entity_id'];
                } elseif ($behavior === Import::BEHAVIOR_ADD_UPDATE
                    || $behavior === Import::BEHAVIOR_REPLACE
                ) {
                    $processedData[(int) $rowData['entity_id']] = $this->_prepareDataForUpdate($rowData, $rowNumber);
                } else {
                    throw new LocalizedException(__('Selected Import Behaviour is not supported'));
                }
            }

            if (!empty($processedData) &&
                ($behavior === Import::BEHAVIOR_ADD_UPDATE || $behavior === Import::BEHAVIOR_REPLACE)
            ) {
                $existingReviews = $this->getExistingReviews(array_keys($processedData));

                foreach ($processedData as $rowData) {
                    // entity_id has already been changed to review_id in the _prepareDataForUpdate() method
                    if ($existingReviews->getItemById($rowData['review_id'])) {
                        $reviewsToUpdate[] = $rowData;
                    } else {
                        $reviewsToCreate[] = $rowData;
                    }
                }

                $this->saveReviews($processedData, $existingReviews, $behavior);
            }

            if (!empty($reviewsToDelete)) {
                $this->deleteReviews($reviewsToDelete);
            }

            $this->updateItemsCounterStats($reviewsToCreate, $reviewsToUpdate, $reviewsToDelete);
        }

        $this->eventManager->dispatch(self::EVEN_IMPORT_COMPLETED);
        return true;
    }

    /**
     * @param array $entityIds
     * @return ReviewCollection
     */
    private function getExistingReviews(array $entityIds)
    {
        /** @var ReviewCollection $collection */
        $collection = $this->getNewReviewsCollection();
        return $collection->addFieldToFilter('main_table.review_id', ['in' => $entityIds]);
    }

    /**
     * Update and insert data
     *
     * @param array $processedData
     * @param ReviewCollection $existingReviews
     * @param string $behaviour
     * @return $this
     * @throws \Exception
     */
    private function saveReviews(array $processedData, ReviewCollection $existingReviews, $behaviour)
    {
        try {
            $customerEmails = [];
            $productSku = [];
            /** @var Mysql $connection */
            $connection = $existingReviews->getConnection();
            $connection->beginTransaction();

            foreach ($processedData as $reviewId => $rowData) {
                if (isset($rowData['customer_email'])
                    && !empty($rowData['customer_email'])
                    && !isset($this->customerEmailsCache[strtolower($rowData['customer_email'])])
                ) {
                    $customerEmails[] = strtolower($rowData['customer_email']);
                }

                if (isset($rowData['sku'])
                    && !empty($rowData['sku'])
                    && !isset($this->productIdsCache[strtolower($rowData['sku'])])
                ) {
                    $productSku[] = strtolower($rowData['sku']);
                }
            }

            if (!empty($customerEmails)) {
                $customerCollection = $this->customerCollectionFactory->create();
                $customerCollection->addFieldToFilter('email', ['in' => array_unique($customerEmails)]);

                /** @var Customer $customer */
                foreach ($customerCollection as $customer) {
                    $this->customerEmailsCache[strtolower($customer->getEmail())] = (int) $customer->getId();
                }
            }

            if (!empty($productSku)) {
                $productCollection = $this->productCollectionFactory->create();
                $productCollection->addFieldToFilter('sku', ['in' => array_unique($productSku)]);

                /** @var Product $product */
                foreach ($productCollection as $product) {
                    $this->productIdsCache[strtolower($product->getSku())] = (int) $product->getId();
                }
            }

            foreach ($processedData as $reviewId => $rowData) {
                /** @var ReviewModel $review */
                $review = $existingReviews->getItemById($reviewId) ?: $existingReviews->getNewEmptyItem();
                $isObjectNew = $review->isObjectNew();

                if ($isObjectNew) {
                    if (!isset($this->productIdsCache[strtolower($rowData['sku'])])) {
                        continue;
                    }

                    $rowData['entity_pk_value'] = $this->productIdsCache[strtolower($rowData['sku'])];
                }

                $customerEmail = strtolower($rowData['customer_email']);

                if (isset($this->customerEmailsCache[$customerEmail])) {
                    $rowData['customer_id'] = $this->customerEmailsCache[$customerEmail];
                }

                // We can not save review with pre-defined ID because the insert into review_detail fails
                // So need to save a new object and update the ID after that
                if ($isObjectNew) {
                    $review->unsetData('review_id');
                    $connection->insertOnDuplicate(
                        $review->getResource()->getMainTable(),
                        [
                            'review_id'       => (int) $reviewId,
                            'created_at'      => $rowData['created_at'],
                            'entity_id'       => $rowData['entity_id'],
                            'entity_pk_value' => $rowData['entity_pk_value'],
                            'status_id'       => (int) $rowData['status_id'],
                        ]
                    );
                    $review = $existingReviews->getNewEmptyItem()->load($reviewId);
                }

                if ($behaviour === Import::BEHAVIOR_ADD_UPDATE) {
                    $review->addData($rowData);
                } else {
                    $review->setData($rowData);
                }

                $review->save();
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Delete list of customers
     *
     * @param array $reviewsToDelete customers id list
     * @return $this
     */
    private function deleteReviews(array $reviewsToDelete)
    {
        throw new \Exception('Deleting reviews is not implemented yet!');
        $condition = $this->_connection->quoteInto('entity_id IN (?)', $reviewsToDelete);
        $this->_connection->delete($this->_entityTable, $condition);

        return $this;
    }

    /**
     * @return ReviewCollection
     */
    private function getNewReviewsCollection()
    {
        return $this->itemCollectionFactory->create();
    }

    /**
     * Prepare review data for update
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return array
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareDataForUpdate(array $rowData, $rowNumber)
    {
        foreach ($this->mapping as $importColumn => $detailedReviewDataColumn) {
            if (isset($rowData[$importColumn])) {
                $rowData[$detailedReviewDataColumn] = $rowData[$importColumn];
                unset($rowData[$importColumn]);
            }
        }

        $rowData['entity_id'] = $this->reviewHelper->getReviewEntityIdByCode();
        $rowData = $this->setExistingImages($rowData, $rowNumber);

        return $rowData;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    private function getImageAttributeCodes()
    {
        if (null === $this->imageAttributeCodes) {
            $this->imageAttributeCodes = [];
            $imageAttributes = $this->attributeHelper->getAttributesCollection();
            $imageAttributes->addFieldToFilter('frontend_input', 'image');

            /** @var Attribute $imageAttribute */
            foreach ($imageAttributes as $imageAttribute) {
                $this->imageAttributeCodes[] = $imageAttribute->getAttributeCode();
            }
        }

        return $this->imageAttributeCodes;
    }

    /**
     * @param array $rowData
     * @param int $rowNumber
     * @param bool $showError
     * @return array
     * @throws LocalizedException
     */
    private function setExistingImages(array $rowData, $rowNumber, $showError = false)
    {
        $mediaDirectoryPath = $this->getAbsoluteImagePath();

        foreach ($this->getImageAttributeCodes() as $attributeCode) {
            if (!isset($rowData[$attributeCode]) || empty($rowData[$attributeCode])) {
                continue;
            }

            $existingImages = [];
            $filePaths = array_filter(explode(',', $rowData[$attributeCode]));

            foreach ($filePaths as $filePath) {
                if (strpos($filePath, 'detailedreview/') === 0) {
                    $filePath = str_replace('detailedreview/', '', $filePath);
                }

                $filePath = '/' . trim($filePath, '/');
                $absolutePath = $mediaDirectoryPath . $filePath;

                if (file_exists($absolutePath)) {
                    $existingImages[] = $filePath;
                } elseif ($showError) {
                    $this->addRowError(
                        self::ERROR_IMAGE_DOES_NOT_EXIST,
                        $rowNumber,
                        $attributeCode,
                        "Can not find image $filePath in path $absolutePath",
                        ProcessingError::ERROR_LEVEL_WARNING
                    );
                }
            }

            $rowData[$attributeCode] = implode(',', $existingImages);
        }

        return $rowData;
    }

    private function getAbsoluteImagePath()
    {
        if (null === $this->absoluteImagePath) {
            $this->absoluteImagePath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                ->getAbsolutePath('mageworkshop/imageloader');
        }

        return $this->absoluteImagePath;
    }
}
