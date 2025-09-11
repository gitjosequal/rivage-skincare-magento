<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\CustomerPermissions\Controller\Adminhtml\Remove;

use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction;

class CustomerFromBan extends AbstractMassAction
{
    /** @var \MageWorkshop\CustomerPermissions\Helper\BanHelper $banHelper */
    protected $banHelper;

    /**
     * CustomerFromBan constructor.
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
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function massAction(AbstractCollection $collection)
    {
        $collection->addAttributeToSelect('banned_till');
        $successfullyRemovedFromBanList = [];
        try {
            $successfullyRemovedFromBanList = $this->banHelper->removeFromBanList($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        if (empty($successfullyRemovedFromBanList)) {
            $this->messageManager->addNoticeMessage(__('No banned users found.'));
        } else {
            $this->messageManager->addSuccessMessage(
                __(
                    'Following author(s) was/were removed from ban list: %1',
                    implode(', ', $successfullyRemovedFromBanList)
                )
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('customer/index/index/');
        return $resultRedirect;
    }
}
