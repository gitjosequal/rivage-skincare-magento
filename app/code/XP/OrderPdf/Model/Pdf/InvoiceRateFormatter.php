<?php
namespace XP\OrderPdf\Model\Pdf;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

class InvoiceRateFormatter
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var null|int|string|mixed
     */
    private $scope = null;

    /**
     * InvoiceRateFormatter constructor.
     * @param PriceCurrencyInterface $priceCurrency
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(PriceCurrencyInterface $priceCurrency, ScopeConfigInterface $scopeConfig)
    {
        $this->priceCurrency = $priceCurrency;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int $scopeId
     */
    public function setScope($scopeId)
    {
        if ($this->scope == null) {
            $this->scope = $scopeId;
        }

        return $this->scope;
    }

    /**
     * Returns the USD formatted price
     *
     * @param int|float $basePrice
     * @return float|string
     */
    public function getFormattedAndConvertedAmount($basePrice)
    {
        return $this->priceCurrency->format(
            $basePrice * $this->getUSDRate(),
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            'USD'
        );
    }

    /**
     * Returns USD covnerted and formatted prices
     *
     * @param Order $order
     * @param string $source_field
     * @return float|string
     */
    public function getFormattedUSDAmount(Order $order, $source_field)
    {
        return $this->getFormattedAndConvertedAmount($this->getBaseAmount($order, $source_field));
    }

    /**
     * Returns USD amount with converted rate.
     *
     * @param Order $order
     * @param string $source_field
     * @return float|int
     */
    public function getBaseAmount(Order $order, $source_field)
    {
        return $order->getData('base_' . $source_field);
    }

    /**
     * Get USD Rate from Configuration
     *
     * @return float
     */
    public function getUSDRate()
    {
        return (double) $this->scopeConfig->getValue(
            \XP\OrderPdf\Model\Pdf\AbstractPdf::XML_PATH_XP_PDF . '/general/usd_rate',
            ScopeInterface::SCOPE_WEBSITE,
            $this->scope
        );
    }
}
