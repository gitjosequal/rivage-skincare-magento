<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Setup;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use MageWorkshop\DetailedReview\Api\Data\Entity\EntityTypeConfigInterface;
use MageWorkshop\DetailedReview\Model\Details;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Swatches\Model\Swatch;

class DetailsSetup extends AbstractEavSetup
{
    /**
     * @return string
     */
    protected function getEntityCode()
    {
        return Details::ENTITY;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAdditionalColumns()
    {
        $columns = [];
        $connection = $this->getSetup()->getConnection();

        /** @var ColumnConfigInterface $column */
        $column = $this->columnConfigFactory->create();
        $column->setName('review_id')
            ->setType(Table::TYPE_BIGINT)
            ->setOptions([
                'nullable' => false,
                'unsigned' => true
            ])
            ->setComment('Parent Review ID');

        /** @var ForeignKeyConfigInterface $foreignKeyConfig */
        $foreignKeyConfig = $this->foreignKeyConfigFactory->create();
        $fkName = $connection->getForeignKeyName($this->getEntityCode(), 'review_id', 'review', 'review_id');
        $foreignKeyConfig->setFkName($fkName)
            ->setColumn('review_id')
            ->setReferenceTable('review')
            ->setReferenceColumn('review_id')
            ->setOnDelete(Table::ACTION_CASCADE);
        $column->setForeignKeyConfig($foreignKeyConfig);

        $columns[] = $column;

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getIndexes()
    {
        $indexes = [];
        $connection = $this->getSetup()->getConnection();

        /** @var IndexConfigInterface $index */
        $index = $this->indexConfigFactory->create();
        $column = 'review_id';
        $indexName = $connection->getIndexName($this->getEntityCode(), $column, AdapterInterface::INDEX_TYPE_UNIQUE);
        $index->setName($indexName)
            ->setColumn($column)
            ->setOptions([
                'type' => AdapterInterface::INDEX_TYPE_UNIQUE
            ]);
        $indexes[] = $index;
        return $indexes;
    }

    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     */
    protected function createAdditionalAttributeTable()
    {
        /** @var EntityTypeConfigInterface $entityConfig */
        $entityConfig = $this->entityConfig->getEntityTypeConfig($this->getEntityCode());
        $tableName = $entityConfig->getAdditionalAttributeTable();
        $setup = $this->getSetup();

        /** Attribute table. See extended example for 'customer_eav_attribute' */
        $table = $setup->getConnection()->newTable(
            $this->getSetup()->getTable($tableName)
        );
        $table->addColumn(
            'attribute_id',
            Table::TYPE_SMALLINT,
            null,
            [
                'identity' => false,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Attribute Id'
        )->addColumn(
            'is_visible_on_front',
            Table::TYPE_BOOLEAN,
            null,
            [
                'default' => 1
            ],
            'Is System'
        )->addColumn(
            'validate_rules',
            Table::TYPE_TEXT,
            '4k',
            [],
            'Validate Rules'
        )->addColumn(
            'additional_data',
            Table::TYPE_TEXT,
            '64k',
            [],
            'Additional swatch attributes data'
        )->addColumn(
            'attribute_placement',
            Table::TYPE_BOOLEAN,
            null,
            [
                'nullable' => false,
                'default' => 0
            ],
            'Mapping for the review sections: review content section or customer details section'
        )->addColumn(
            'attribute_visual_settings',
            Table::TYPE_TEXT,
            '4k',
            [],
            'Attribute visual settings'
        )->addForeignKey(
            $setup->getConnection()->getForeignKeyName($tableName, 'attribute_id', 'eav_attribute', 'attribute_id'),
            'attribute_id',
            $setup->getTable('eav_attribute'),
            'attribute_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Review Eav Attribute'
        );
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function getEntityAttributes()
    {
        $attributes = [
            'review_id' => [
                'input'            => 'text',
                'label'            => 'Review ID',
                'type'             => AbstractAttribute::TYPE_STATIC,
                'user_defined'     => 0,
                'required'         => 1,
                'visible_on_front' => 0,
                'sort_order'       => 0
            ],
            'nickname' => [
                'input'        => 'text',
                'label'        => 'Nickname',
                'type'         => AbstractAttribute::TYPE_STATIC,
                'user_defined' => 0,
                'required'     => 1,
                'sort_order'   => 10
            ],
            'title' => [
                'input'        => 'text',
                'label'        => 'Summary',
                'type'         => AbstractAttribute::TYPE_STATIC,
                'user_defined' => 0,
                'required'     => 1,
                'sort_order'   => 20
            ],
            'detail' => [
                'input'        => 'textarea',
                'label'        => 'Review',
                'type'         => AbstractAttribute::TYPE_STATIC,
                'user_defined' => 0,
                'required'     => 1,
                'sort_order'   => 30
            ],
            'like' => [
                'input'        => 'boolean',
                'label'        => 'Do you like this product?',
                'group'        => 'General',
                'sort_order'   => 60
            ],
            'coolness' => [
                'input'        => 'select',
                'label'        => 'Is this product cool?',
                'group'        => 'General',
                'sort_order'   => 70,
                'option'       => [
                    'values' => [
                        'Super Cool!',
                        'Good!',
                        'Not bad',
                        'Nothing special'
                    ]
                ]
            ],
            'location' => [
                'input'        => 'text',
                'label'        => 'Location',
                'group'        => 'General',
                'attribute_placement' => 1,
                'sort_order'   => 80,
            ],
            'age' => [
                'input'        => 'text',
                'label'        => 'Age',
                'group'        => 'General',
                'validate_rules' => $this->serializer->serialize([
                    /* [ better to use input[type=number] instead
                        'type' => 'minimum-length',
                        'value' => '2'
                    ], */[
                        'type' => 'maximum-length',
                        'value' => '3'
                    ], [
                        'type' => 'validate-number',
                        'value' => true
                    ]
                ]),
                'attribute_placement' => 1,
                'sort_order'   => 90
            ],
            'height' => [
                'input'        => 'text',
                'label'        => 'Height',
                'group'        => 'General',
                'validate_rules' => $this->serializer->serialize([
                    /* [ better to use input[type=number] instead
                        'type' => 'minimum-length',
                        'value' => '2'
                    ], */[
                        'type' => 'maximum-length',
                        'value' => '3'
                    ], [
                        'type' => 'validate-number',
                        'value' => ''
                    ]
                ]),
                'attribute_placement' => 1,
                'sort_order'   => 100
            ],
            'pros' => [
                'input'        => 'multiselect',
                'label'        => 'Pros',
                'group'        => 'General',
                'sort_order'   => 110,
                'option'       => [
                    'values' => [
                        'Price',
                        'Quality',
                        'Reliableness',
                        'Usefulness',
                        'Comfort'
                    ]
                ]
            ],
            'cons' => [
                'input'        => 'multiselect',
                'label'        => 'Cons',
                'group'        => 'General',
                'sort_order'   => 120,
                'option'       => [
                    'values' => [
                        'Price',
                        'Quality',
                        'Reliableness',
                        'Usefulness',
                        'Comfort'
                    ]
                ]
            ],
            'body_type' => [
                'input'        => 'swatch_visual',
                'label'        => 'Body Type',
                'group'        => 'General',
                'sort_order'   => 130,
                'attribute_placement' => 1,
                'additional_data' => $this->serializer->serialize([
                    'swatch_input_type' => Swatch::SWATCH_INPUT_TYPE_VISUAL
                ])
            ]
        ];

        return $attributes;
    }

    /**
     * Install EAV entity data
     * Swatch attribute options are not installed properly because EAV installer inserts data rather than saving models
     * Anyway, there does not seem to be ability to install swatch attributes in some adequate way in the early
     * M2 versions
     *
     * @param array $entities
     * @return $this
     */
    public function installEntities($entities = null)
    {
        parent::installEntities($entities);
        // $this->attributeRepository does not "see" newely installed entity type, so we need to clean it's cache
        $this->eavConfig->clear();

        foreach ($this->getSwatchOptions() as $attributeCode => $options) {
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute = $this->attributeRepository->get($this->getEntityCode(), $attributeCode);
            $swatchData = $this->convertOptionToVisualSwatch($attributeCode, $options);
            $attribute->addData($swatchData);
            $attribute->save();
        }

        return $this;
    }

    /**
     * Swatch options definition. Only file names are required. Dispersion and proper names will be added after
     * images upload is emulated in the \MageWorkshop\DetailedReview\Setup\VisualSwatchInstaller
     *
     * @return array
     */
    protected function getSwatchOptions()
    {
        return [
            'body_type' => [
                [
                    'label'   => ['Triangle'],
                    'image'   => 'triangle.png',
//                    'default' => true
                ], [
                    'label'   => ['Inverted Triangle'],
                    'image'   => 'inverted-triangle.png'
                ], [
                    'label'   => ['Rectangle'],
                    'image'   => 'rectangle.png'
                ], [
                    'label'   => ['Hourglass'],
                    'image'   => 'hourglass.png'
                ], [
                    'label'   => ['Apple'],
                    'image'   => 'apple.png'
                ]
            ]
        ];
    }

    /**
     * @param string $attributeCode
     * @param array $options
     * @return array
     * internal param array $option
     * internal param $attributeCode
     * internal param array $optionsData
     */
    protected function convertOptionToVisualSwatch($attributeCode, array $options)
    {
        $result = [
            'defaultvisual' => [],
            'optionvisual' => [
                'order'  => [],
                'value'  => [],
                'delete' => []
            ],
            'swatchvisual' => []
        ];

        /* Result should look like this:
            $options = [
                'defaultvisual' => [
                    'option_1'
                ],
                'optionvisual' => [
                    'order' => [
                        'option_0' => 1,
                        'option_1' => 2
                    ],
                    'value' => [
                        'option_0' => [
                            'Option 1'
                        ],
                        'option_1' => [
                            'Option 2'
                        ],
                    ],
                    'delete' => [
                        'option_0' => '',
                        'option_1' => '',
                    ]
                ],
                'swatchvisual' => [
                    'value' => [
                        'option_0' => '/s/o/some_image.png',
                        'option_1' => '/e/x/example_swatch.png'
                    ]
                ]
            ];
        */
        foreach ($options as $index => $option) {
            if (isset($option['image']) && $option['image']) {
                $option['image'] = $this->visualSwatchInstaller->installSwatchImage($attributeCode, $option['image']);
            }

            $optionIndex = 'option_' . $index;

            if (isset($option['default']) && $option['default']) {
                $result['defaultvisual'][] = $optionIndex;
            }

            $result['optionvisual']['order'][$optionIndex] = $index + 1;
            $result['optionvisual']['value'][$optionIndex] = $option['label'];
            $result['optionvisual']['delete'][$optionIndex] = '';

            if (isset($option['image']) && $option['image']) {
                $result['swatchvisual']['value'][$optionIndex] = $option['image'];
            }
        }
        return $result;
    }
}
