<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Controller\Adminhtml\Set;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;

class BanToCustomer extends AbstractMassAction
{
    /**
     * @var \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper
     */
    private $banHelper;

    /**
     * BanToCustomer constructor.
     * @param \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper,
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->banHelper = $banHelper;
    }

    /**
     * @param AbstractCollection $customerCollection
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function massAction(AbstractCollection $customerCollection)
    {
        $successfullyBanned = [];
        $customerCollection->addAttributeToSelect('banned_till');
        $banPeriod = $this->getRequest()->getParam('ban_period');
        try {
            foreach ($customerCollection as $customer) {
                /** @var \Magento\Customer\Model\Backend\Customer $customer */
                $this->banHelper->banCustomer($customer, $banPeriod);
                $successfullyBanned[] = $customer->getName();
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $this->messageManager->addSuccessMessage(
            __('Following customer(s) was/were banned: %1', implode(', ', $successfullyBanned))
        );
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/index/index/');
        return $resultRedirect;
    }
}
