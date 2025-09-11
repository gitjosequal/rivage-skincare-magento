<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use MageWorkshop\DetailedReview\Model\Details;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

class GenerateDetailFixturesCommand extends \MageWorkshop\DetailedReview\Console\AbstractFixturesCommand
{
    const FORCE_REGENERATE = 'force';

    /** @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory */
    protected $reviewCollectionFactory;

    /** @var \MageWorkshop\DetailedReview\Helper\AbstractAttribute $attributeHelper */
    protected $attributeHelper;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory
     * @param \MageWorkshop\DetailedReview\Helper\AbstractAttribute $attributeHelper
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\State $appState,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        \MageWorkshop\DetailedReview\Helper\AbstractAttribute $attributeHelper,
        $name = null
    ) {
        parent::__construct($eavConfig, $appState, $name);
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->attributeHelper = $attributeHelper;
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this->setName('mageworkshop:detailedreview:generate-detail-fixtures')
            ->setDescription('MageWorkshop DetailedReview Review Details fixtures generator (max 100k per launch)')
            ->setDefinition([
                new InputOption(
                    self::FORCE_REGENERATE,
                    '-f',
                    InputOption::VALUE_NONE,
                    'Force regenerate data'
                )
            ]);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->attributeHelper->setEntityCode(Details::ENTITY);
        parent::execute($input, $output);
        $this->generateData($this->reviewCollectionFactory->create(), $input, $output);
    }

    /**
     * @param ReviewCollection $reviewCollection
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function generateData(
        ReviewCollection $reviewCollection,
        InputInterface $input,
        OutputInterface $output
    ) {
        $select = $reviewCollection->getSelect();
        if (!$input->getOption(self::FORCE_REGENERATE)) {
            $select->joinLeft(
                ['eav_entity' => $reviewCollection->getTable(Details::ENTITY)],
                'eav_entity.review_id = main_table.review_id',
                []
            );
            $reviewCollection->addFieldToFilter('eav_entity.eav_entity_id', ['null' => true]);
        }

        // Refresh data for particular product
        // $reviewCollection->addFieldToFilter('main_table.entity_pk_value', 1561);

        $reviewCollection->getSelect()->limit(100000);

        $totalReviewsProcessed = 0;
        $count = $reviewCollection->count();
        $this->logTotal($totalReviewsProcessed, $count, $output);

        /** @var \Magento\Review\Model\Review $review */
        foreach ($reviewCollection as $review) {
            $this->populateData($review, $this->getAttributes());
            $review->setData('admin_response', $this->generateText());
            $review->save();
            $totalReviewsProcessed++;

            if ($totalReviewsProcessed % 100 === 0) {
                $this->logTotal($totalReviewsProcessed, $count, $output);
            }
        }
        if ($totalReviewsProcessed % 100 !== 0) {
            $this->logTotal($totalReviewsProcessed, $count, $output);
        }
    }

    protected function getAttributes()
    {
        $attributes = [];
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        foreach ($this->attributeHelper->getIndexableDynamicAttributeCollection() as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $attributes[$attributeCode] = [
                'input_type'  => $attribute->getFrontendInput(),
                'has_options' => $attribute->usesSource(),
                'options'     => []
            ];

            if ($options = $attribute->getOptions()) {
                /** @var \Magento\Eav\Model\Entity\Attribute\Option $option */
                foreach ($options as $option) {
                    if (!$value = $option->getValue()) {
                        continue;
                    }

                    $attributes[$attributeCode]['options'][] = $value;
                }
            }
        }
        return $attributes;
    }
}
