<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\Voting\Helper;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\SearchCriteria;
use MageWorkshop\Voting\Model\Vote;

/**
* Helper for criteria creating for the usage in the repositories
*/
class Criteria extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroupBuilder
     */
    protected $filterGroupBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Array of field keys, allowed for filtering
     * @var array
     */
    protected static $allowedFilters = [
        Vote::KEY_VOTE_ID,
        Vote::KEY_REVIEW_ID,
        Vote::KEY_CUSTOMER_ID,
        Vote::KEY_GUEST_TOKEN,
        Vote::KEY_PRODUCT_ID,
    ];

    /**
     * Criteria constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\Search\FilterGroupBuilder $filterGroupBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }

    /**
     * @param array $parameters
     * @return SearchCriteria
     */
    public function createCriteriaByParamsArray(array $parameters)
    {
        /** @var Filter [] $filters */
        $filters = [];

        foreach ($parameters as $key => $value) {
            if (!in_array($key, self::$allowedFilters, true)) {
                continue;
            }
            /** @var Filter $filter */
            $filter = $this->filterBuilder->create()
                ->setField($key)
                ->setValue($value)
                ->setConditionType(is_array($value) ? 'in' : 'eq');

            $filters[] = $filter;
        }

        /** @var FilterGroup $filterGroup */
        $filterGroup = $this->filterGroupBuilder->create();
        $filterGroup->setFilters($filters);

        /** @var SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->create()
            ->setFilterGroups([$filterGroup]);

        return $searchCriteria;
    }
}