<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Observer\Review;

abstract class AbstractPrepareCollection implements \Magento\Framework\Event\ObserverInterface
{

    /** @var \Magento\Framework\App\RequestInterface $request */
    protected $request;

    /**
     * AbstractPrepareCollection constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param $requestParam
     * @return string
     */
    protected function getRequestParam($requestParam)
    {
        return $this->request->getParam($requestParam);
    }
}
