<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Affiliate for Magento 2
 */

namespace Amasty\Affiliate\Controller\Adminhtml\Account;

use Amasty\Affiliate\Api\AccountRepositoryInterface;
use Amasty\Affiliate\Controller\Adminhtml\Account;
use Amasty\Affiliate\Model\ResourceModel\Account\CollectionFactory;
use Amasty\Affiliate\Model\Source\Status;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassChangeStatus extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = Account::ADMIN_RESOURCE;

    /**
     * @var Status
     */
    private $statusOptions;

    /**
     * @var AccountRepositoryInterface
     */
    private $accountRepository;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Action\Context $context,
        AccountRepositoryInterface $accountRepository,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Status $statusOptions
    ) {
        $this->statusOptions = $statusOptions;
        $this->accountRepository = $accountRepository;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Change Status action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/');

        $status = (int)$this->getRequest()->getParam('status');
        $optionArray = $this->statusOptions->toArray();
        if (!isset($optionArray[$status])) {
            $this->messageManager->addErrorMessage(__('Wrong status value.'));

            return $resultRedirect;
        }
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        /** @var \Amasty\Affiliate\Model\Account $item */
        foreach ($collection as $item) {
            $item->setStatus($status);
            $this->accountRepository->save($item);
        }

        $message = 'A total of %1 record(s) have been changed to status: ' . $optionArray[$status];

        $this->messageManager->addSuccessMessage(__($message, $collection->getSize()));

        return $resultRedirect;
    }
}
