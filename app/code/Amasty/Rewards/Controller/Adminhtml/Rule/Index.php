<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */

/**
 * Copyright © 2015 Amasty. All rights reserved.
 */
namespace Amasty\Rewards\Controller\Adminhtml\Rule;

class Index extends \Amasty\Rewards\Controller\Adminhtml\Rule
{

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_Rewards::rule');
        $resultPage->getConfig()->getTitle()->prepend(__('Rewards'));
        $resultPage->addBreadcrumb(__('Rewards'), __('Rewards'));

        return $resultPage;
    }
}
