<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckReviewBackendObserver implements ObserverInterface
{
    /**
     * @var \Magento\Captcha\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Captcha\Observer\CaptchaStringResolver
     */
    protected $captchaStringResolver;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\HTTP\Header
     */
    protected $responseHeader;

    /**
     * @param \Magento\Captcha\Helper\Data $helper
     * @param \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\HTTP\Header $responseHeader
     */
    public function __construct(
        \Magento\Captcha\Helper\Data $helper,
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\HTTP\Header $responseHeader
    ) {
        $this->helper                = $helper;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->actionFlag            = $actionFlag;
        $this->messageManager        = $messageManager;
        $this->responseHeader        = $responseHeader;
        $this->redirect              = $redirect;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formId = 'product_review';
        $captcha = $this->helper->getCaptcha($formId);
        if ($captcha->isRequired()) {
            /** @var \Magento\Framework\App\Action\Action $controller */
            $controller = $observer->getControllerAction();
            if (!$captcha->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
                $this->messageManager->addErrorMessage(__('Incorrect CAPTCHA.'));
                $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                $this->redirect->redirect($controller->getResponse(), $this->responseHeader->getHttpReferer());
            }
        }
    }
}
