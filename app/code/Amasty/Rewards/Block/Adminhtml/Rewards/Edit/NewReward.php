<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Block\Adminhtml\Rewards\Edit;
use Magento\Customer\Controller\RegistryConstants;

class NewReward extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Amasty\Rewards\Controller\Adminhtml\Rewards\Index
     */
    protected $_controller;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $formFactory, $data);
        $this->setUseContainer(true);
    }

    /**
     * Form preparation
     *
     * @return void
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(['data' => ['id' => 'new_rewards_form', 'class' => 'admin__scope-old']]);
        $form->setUseContainer($this->getUseContainer());

        $customerId = (int)$this->getRequest()->getParam('id');

        $form->addField('new_rewards_messages', 'note', []);

        $fieldset = $form->addFieldset('new_rewards_form_fieldset', []);

        $fieldset->addField(
            'new_rewards_amount',
            'text',
            [
                'label'     => __('Amount'),
                'title'     => __('Amount'),
                'class'     => 'validate-number',
                'required'  => true,
                'note'      => __('Use negative amount to deduct points e.g. "-100"'),
                'name'      => 'new_rewards_amount'
            ]
        );

        $fieldset->addField(
            'new_rewards_comment',
            'textarea',
            [
                'label' => __('Comment'),
                'title' => __('Comment'),
                'required' => true,
                'name' => 'new_rewards_comment',
            ]
        );

        $fieldset->addField(
            'new_rewards_customer',
            'hidden',
            [
                'name' => 'new_rewards_comment',
                'value' => $customerId
            ]
        );


        $this->setForm($form);
    }

    /**
     * Attach new category dialog widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {

        $widgetOptions = $this->_jsonEncoder->encode(
            [
                'saveCategoryUrl' => $this->getUrl('amasty_rewards/rewards/new'),
                'customerId'      => $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
            ]
        );
        //TODO: JavaScript logic should be moved to separate file or reviewed
        return <<<HTML
<script>
require(["jquery","mage/mage", "Amasty_Rewards/js/add-points-dialog"],function($) {  // waiting for dependencies at first
    $(function(){ // waiting for page to load to have '#category_ids-template' available
        $('#add-points').newRewardsDialog($widgetOptions);
    });
});
</script>
HTML;
    }
}
