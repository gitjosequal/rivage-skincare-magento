<?php
/**
 * MasterCard Internet Gateway Service (MIGS) - Virtual Payment Client (VPC)
 * @author      Trinh Doan
 * @copyright   Copyright (c) 2017 Trinh Doan
 * @package     TD_MasterCard
 */
namespace TD\MasterCard\Gateway\Config;

/**
 * Class Config.
 * Values returned from Magento\Payment\Gateway\Config\Config.getValue()
 * are taken by default from ScopeInterface::SCOPE_STORE
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const CODE = 'mastercard_gateway';

    const KEY_ACTIVE = 'active';
    const KEY_TITLE = 'title';
    const KEY_DESCRIPTION = 'description';
    const KEY_GATEWAY_LOGO = 'gateway_logo';
    const KEY_MERCHANT_ID = 'merchant_id';
    const KEY_ACCESS_CODE = 'access_code';
    const KEY_SECURE_SECRET = 'secure_secret';
    const KEY_DEBUG = 'debug';
    const KEY_MASTERCARD_APPROVED_ORDER_STATUS = 'mastercard_approved_order_status';
    const KEY_EMAIL_CUSTOMER = 'email_customer';
    const KEY_AUTOMATIC_INVOICE = 'automatic_invoice';


    /**
     * Get Merchant Id
     *
     * @return string
     */
    public function getMerchantId() {
        return $this->getValue(self::KEY_MERCHANT_ID);
    }

    /**
     * Get Merchant Access Code
     *
     * @return string
     */
    public function getAccessCode() {
        return $this->getValue(self::KEY_ACCESS_CODE);
    }

    /**
     * Get Merchant Secure Secret Key
     *
     * @return string
     */
    public function getSecureSecret() {
        return $this->getValue(self::KEY_SECURE_SECRET);
    }

    /**
     * Get Merchant number
     *
     * @return string
     */
    public function getTitle() {
        return $this->getValue(self::KEY_TITLE);
    }

    /**
     * Get Logo
     *
     * @return string
     */
    public function getLogo() {
        return $this->getValue(self::KEY_GATEWAY_LOGO);
    }

    /**
     * Get Description
     *
     * @return string
     */
    public function getDescription() {
        return $this->getValue(self::KEY_DESCRIPTION);
    }

    /**
     * Get MasterCard Approved Order Status
     *
     * @return string
     */
    public function getMasterCardApprovedOrderStatus()
    {
        return $this->getValue(self::KEY_MASTERCARD_APPROVED_ORDER_STATUS);
    }

    /**
     * Check if customer is to be notified
     * @return boolean
     */
    public function isEmailCustomer()
    {
        return (bool) $this->getValue(self::KEY_EMAIL_CUSTOMER);
    }

    /**
     * Check if customer is to be notified
     * @return boolean
     */
    public function isAutomaticInvoice()
    {
        return (bool) $this->getValue(self::KEY_AUTOMATIC_INVOICE);
    }

    /**
     * Get Payment configuration status
     * @return bool
     */
    public function isActive()
    {
        return (bool) $this->getValue(self::KEY_ACTIVE);
    }

}
