<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace JoSql\DiscardDiscount\Controller\Rewrite\Cart;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponPost extends \Magento\Checkout\Controller\Cart implements HttpPostActionInterface
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Coupon factory
     *
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->couponFactory = $couponFactory;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        
        $couponCode = $this->getRequest()->getParam('remove') == 1
            ? ''
            : trim($this->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldCouponCode = $cartQuote->getCouponCode();
        
        $productdiscountAmount = 0;
        $productOriginalAmount = 0;
        
        
       
        
        $codeLength = strlen($couponCode);
        if (!$codeLength && !strlen($oldCouponCode)) {
            return $this->_goBack();
        }
        
        $applyCouponMsg = '';
        
         $coupon = $this->couponFactory->create();
        $data = $coupon->load($couponCode, 'code');
        $ruleId =   $data->getRuleId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $rule = $objectManager->create('\Magento\SalesRule\Model\Rule')->load($ruleId);
       
       
        $dateCouponValid = false;
        
        if($rule->getFromDate() && $rule->getToDate() && strtotime($rule->getFromDate()) >= time() && strtotime($rule->getToDate()) <= time()){
            $dateCouponValid = true;
        }else if($rule->getFromDate() && strtotime($rule->getFromDate()) >= time()){
            $dateCouponValid = true;
        }else if($rule->getToDate() && strtotime($rule->getToDate()) <= time()){
            $dateCouponValid = true;
        }else{
            $dateCouponValid = true;
        }
      
        
        if($cartQuote->getItems() && $codeLength && $rule->getIsActive() && $dateCouponValid){
            
           
            $actionType = $rule->getSimpleAction();
            
            $amount = $rule->getDiscountAmount();
            $couponDiscount = 0;
            foreach($cartQuote->getItems() as $item){
               
               $productdiscountAmount += $item->getProduct()->getPrice() - $item->getProduct()->getFinalPrice();
               $productOriginalAmount += $item->getProduct()->getPrice();
                if($actionType == 'by_fixed'){
                   $couponDiscount += $amount;
                }
            }
            
            if($actionType == 'by_percent'){
                $couponDiscount = $productOriginalAmount * ($amount / 100);
            }else if($actionType == 'cart_fixed'){
               $couponDiscount = $amount;
            }
            
            // echo "*******couponDiscount************";
            // var_dump($couponDiscount);
            // echo "*********productdiscountAmount**********";
            // echo $productdiscountAmount;
            // echo "********productOriginalAmount***********";
            // echo $productOriginalAmount;
            // echo "*******************";
            // echo $amount;
            // echo "*******actionType************";
            // echo $actionType;die;
            $discardCoupons = ['CAB15212023R','CAB20212023R'];

            if(!in_array($couponCode,$discardCoupons)){
            
                if($couponDiscount && $couponDiscount < $productdiscountAmount){
                    $this->messageManager->addErrorMessage(__('You can\'t apply this coupon code because you have discount on cart products'));
                    return $this->_goBack();
                }else if($couponDiscount && $couponDiscount >= $productdiscountAmount){
                    $applyCouponMsg = '  The amount of your coupon is more than the products discount, the products discount has been discarded.';
                    foreach($cartQuote->getItems() as $item){
                        $item->setOriginalCustomPrice($item->getProduct()->getPrice());
                    }
                }
            }
            
        }else if(!$codeLength){
            foreach($cartQuote->getItems() as $item){
                $item->setOriginalCustomPrice($item->getProduct()->getFinalPrice());
            }
        }
        

        try {
            $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

            $itemsCount = $cartQuote->getItemsCount();
            if ($itemsCount) {
                $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                $this->quoteRepository->save($cartQuote);
            }

            if ($codeLength) {
                $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
                $coupon = $this->couponFactory->create();
                $coupon->load($couponCode, 'code');
                if (!$itemsCount) {
                    if ($isCodeLengthValid && $coupon->getId()) {
                        $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                        $this->messageManager->addSuccessMessage(
                            __(
                                'You used coupon code "%1".' . $applyCouponMsg,
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    } else {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    }
                } else {
                    if ($isCodeLengthValid && $coupon->getId() && $couponCode == $cartQuote->getCouponCode()) {
                        $this->messageManager->addSuccessMessage(
                            __(
                                'You used coupon code "%1".' . $applyCouponMsg,
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    } else {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The coupon code "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    }
                }
            } else {
                $this->messageManager->addSuccessMessage(__('You canceled the coupon code.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We cannot apply the coupon code.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }

        return $this->_goBack();
    }
}
