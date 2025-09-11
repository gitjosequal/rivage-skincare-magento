<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api\Data;

interface RuleInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const RULE_ID = 'rule_id';
    const IS_ACTIVE = 'is_active';
    const NAME = 'name';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const ACTION = 'action';
    const AMOUNT = 'amount';
    const SPENT_AMOUNT = 'spent_amount';
    /**#@-*/

    /**
     * @return int
     */
    public function getRuleId();

    /**
     * @param int $ruleId
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setRuleId($ruleId);

    /**
     * @return int
     */
    public function getIsActive();

    /**
     * @param int $isActive
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setIsActive($isActive);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * @param string|null $conditionsSerialized
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * @return string|null
     */
    public function getAction();

    /**
     * @param string|null $action
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setAction($action);

    /**
     * @return int
     */
    public function getAmount();

    /**
     * @param int $amount
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setAmount($amount);

    /**
     * @return int
     */
    public function getSpentAmount();

    /**
     * @param int $spentAmount
     *
     * @return \Amasty\Rewards\Api\Data\RuleInterface
     */
    public function setSpentAmount($spentAmount);
}
