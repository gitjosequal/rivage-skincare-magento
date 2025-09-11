<?php
/**
 * Copyright © Rivage(info@rivage.com) All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddtoCartEventObserver implements ObserverInterface
{
    /**
     * @var \Rivage\GtmExtension\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Rivage\GtmExtension\Block\Datalayer
     */
    protected $datablock;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @param \Rivage\GtmExtension\Helper\Data $helper
     * @param \Rivage\GtmExtension\Block\Datalayer $datablock
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Rivage\GtmExtension\Helper\Data $helper,
        \Rivage\GtmExtension\Block\Datalayer $datablock,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->helper = $helper;
        $this->datablock = $datablock;
        $this->checkoutSession = $checkoutSession;
        $this->localeResolver = $localeResolver;
    }

    /**
     * Observer for AddToCartEvent
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isModuleEnabled()) {
            return $this;
        }

        $product = $observer->getProduct();
        $request = $observer->getRequest();

        $params = $request->getParams();

        // @codingStandardsIgnoreStart
        if (isset($params['qty'])) {
            $filter = new \Magento\Framework\Filter\LocalizedToNormalized(
                ['locale' => $this->localeResolver->getLocale()] // Use the injected $localeResolver
            );
            $qty = $filter->filter($params['qty']);
        }
        else {
            $qty = 1;
        }
        // @codingStandardsIgnoreEnd

        $requestParams = [];
        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $params['qty'] = $qty;
            $requestParams = $params;
        }

        $addToCartJsonData = $this->datablock->getAddtocartJsonData($qty, $product, $requestParams);
        $this->checkoutSession->setData('GA4AddToCartData', $addToCartJsonData);

        return $this;
    }
}
