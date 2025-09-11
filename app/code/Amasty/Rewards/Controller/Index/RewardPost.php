<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Controller\Index;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;

class RewardPost extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Amasty\Rewards\Model\Quote
     */
    protected $_rewardsQuote;

    /**
     * @var \Amasty\Rewards\Model\Rewards
     */
    protected $_rewardsModel;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\Rewards\Helper\Data
     */
    private $helper;

    /**
     * RewardPost constructor.
     *
     * @param \Magento\Framework\App\Action\Context              $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session                    $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator     $formKeyValidator
     * @param \Magento\Checkout\Model\Cart                       $cart
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Quote\Api\CartRepositoryInterface         $quoteRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\Registry $registry,
        \Amasty\Rewards\Model\Quote $quote,
        \Amasty\Rewards\Helper\Data $helper,
        \Amasty\Rewards\Model\Rewards $rewards,
        CustomerSession $customerSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Psr\Log\LoggerInterface\Proxy $logger
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->customerSession = $customerSession;
        $this->_rewardsQuote = $quote;
        $this->_registry = $registry;
        $this->_rewardsModel = $rewards;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->helper = $helper;
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
        $applyCode = $this->getRequest()->getParam('remove') == 1 ? 0 : 1;
        $cartQuote = $this->_checkoutSession->getQuote();
        $usedPoints = $this->helper->roundPoints($this->getRequest()->getParam('amreward_amount', 0));
        $pointsLeft = $this->_rewardsModel->getPoints($this->customerSession->getCustomerId());

        try {
            if ($applyCode) {

                if ($usedPoints > $pointsLeft) {
                    throw new LocalizedException(__('Too much point(s) used.'));
                }
                if ($usedPoints < 0) {
                    $usedPoints = $usedPoints * -1;
                    if ($usedPoints < 0) {
                        return $this->_goBack();
                    }
                }
                $itemsCount = $cartQuote->getItemsCount();
                if ($itemsCount) {
                    $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                    $cartQuote->setData('amrewards_point', $usedPoints);

                    $cartQuote->setDataChanges(true);
                    $cartQuote->collectTotals();
                    $this->_rewardsQuote->addReward(
                        $cartQuote->getId(),
                        $cartQuote->getData('amrewards_point')
                    );
                    $this->quoteRepository->save($cartQuote);

                    if ($this->_registry->registry('ampoints_used')) {
                        $this->messageManager->addNoticeMessage(
                            __('You used %1 point(s)', $this->_registry->registry('ampoints_used'))
                        );
                    }
                }
            } else {
                $itemsCount = $cartQuote->getItemsCount();
                if ($itemsCount) {
                    $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                    $cartQuote->setData('amrewards_point', 0);
                    $cartQuote->setDataChanges(true);
                    $cartQuote->collectTotals();
                }
                $this->quoteRepository->save($cartQuote);

                $this->_rewardsQuote->addReward(
                    $cartQuote->getId(),
                    0
                );

                $this->messageManager->addSuccessMessage(__('You Canceled Reward'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We cannot Reward.'));
            $this->logger->critical($e);
        }

        return $this->_goBack();
    }
}
