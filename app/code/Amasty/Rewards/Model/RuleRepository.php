<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

namespace Amasty\Rewards\Model;

use Amasty\Rewards\Api\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class RuleRepository implements \Amasty\Rewards\Api\RuleRepositoryInterface
{
    /**
     * @var ResourceModel\Rule
     */
    protected $ruleResource;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * RuleRepository constructor.
     *
     * @param ResourceModel\Rule                $ruleResource
     * @param RuleFactory                       $ruleFactory
     */
    public function __construct(
        \Amasty\Rewards\Model\ResourceModel\Rule $ruleResource,
        \Amasty\Rewards\Model\RuleFactory $ruleFactory
    ) {
        $this->ruleResource = $ruleResource;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Data\RuleInterface $rule)
    {
        try {
            $this->ruleResource->save($rule);
            unset($this->rules[$rule->getId()]);
        } catch (\Exception $e) {
            if ($rule->getRuleId()) {
                throw new CouldNotSaveException(
                    __('Unable to save rule with ID %1. Error: %2', [$rule->getRuleId(), $e->getMessage()])
                );
            }
            throw new CouldNotSaveException(__('Unable to save new rule. Error: %1', $e->getMessage()));
        }

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function get($ruleId)
    {
        if (!isset($this->rules[$ruleId])) {
            /** @var \Amasty\Rewards\Model\Rule $rule */
            $rule = $this->ruleFactory->create();
            $this->ruleResource->load($rule, $ruleId);
            if (!$rule->getRuleId()) {
                throw new NoSuchEntityException(__('Rule with specified ID "%1" not found.', $ruleId));
            }
            $this->rules[$ruleId] = $rule;
        }
        return $this->rules[$ruleId];
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Data\RuleInterface $rule)
    {
        try {
            $this->ruleResource->delete($rule);
            unset($this->rules[$rule->getId()]);
        } catch (\Exception $e) {
            if ($rule->getRuleId()) {
                throw new CouldNotDeleteException(
                    __('Unable to remove rule with ID %1. Error: %2', [$rule->getRuleId(), $e->getMessage()])
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove rule rule. Error: %1', $e->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ruleId)
    {
        $model = $this->get($ruleId);
        $this->delete($model);
        return true;
    }
}
