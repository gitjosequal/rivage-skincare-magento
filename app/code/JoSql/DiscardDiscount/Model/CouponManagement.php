<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace JoSql\DiscardDiscount\Model;

use Magento\Framework\Exception\LocalizedException;
use \Magento\Quote\Api\CouponManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Coupon management object.
 */
class CouponManagement implements CouponManagementInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheritDoc
     */
    public function get($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        return $quote->getCouponCode();
    }

    /**
     * @inheritDoc
     */
    public function set($cartId, $couponCode)
    {

        $quote = $this->quoteRepository->getActive($cartId);
        
        
        
        /**********************************************************/
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $couponFactory = $objectManager->create('\Magento\SalesRule\Model\CouponFactory');
        
        $oldCouponCode = $quote->getCouponCode();
        
        $productdiscountAmount = 0;
        $productOriginalAmount = 0;
        
        
        $codeLength = strlen($couponCode);

        $applyCouponMsg = '';
        
        $coupon = $couponFactory->create();
        $data = $coupon->load($couponCode, 'code');
        $ruleId =   $data->getRuleId();
        
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
        
        if($quote->getItems() && $codeLength && $rule->getIsActive() && $dateCouponValid){
            
            
            $actionType = $rule->getSimpleAction();
            
            $amount = $rule->getDiscountAmount();
            $couponDiscount = 0;
            foreach($quote->getItems() as $item){
               
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
                    throw new NoSuchEntityException(__("You can't apply this coupon code because you have discount on cart products"));
                
                }else if($couponDiscount && $couponDiscount >= $productdiscountAmount){
                    $applyCouponMsg = '  The amount of your coupon is more than the products discount, the products discount has been discarded.';
                    foreach($quote->getItems() as $item){
                        $item->setOriginalCustomPrice($item->getProduct()->getPrice());
                    }
                }
            }
            
            
        }
        
        /*********************************************************/
        
        
        
        
        
        /** @var  \Magento\Quote\Model\Quote $quote */
        
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        if (!$quote->getStoreId()) {
            throw new NoSuchEntityException(__('Cart isn\'t assigned to correct store'));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);

        try {
            $quote->setCouponCode($couponCode);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__('The coupon code couldn\'t be applied: ' .$e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __("The coupon code couldn't be applied. Verify the coupon code and try again."),
                $e
            );
        }
        if ($quote->getCouponCode() != $couponCode) {
            throw new NoSuchEntityException(__("The coupon code isn't valid. Verify the code and try again."));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function remove($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            if($quote->getItems()){
                foreach($quote->getItems() as $item){
                    $item->setOriginalCustomPrice($item->getProduct()->getFinalPrice());
                }
            }
        
            $quote->setCouponCode('');
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __("The coupon code couldn't be deleted. Verify the coupon code and try again.")
            );
        }
        if ($quote->getCouponCode() != '') {
            throw new CouldNotDeleteException(
                __("The coupon code couldn't be deleted. Verify the coupon code and try again.")
            );
        }
        
        
        return true;
    }
}
