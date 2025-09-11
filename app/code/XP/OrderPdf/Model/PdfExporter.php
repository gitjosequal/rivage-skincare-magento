<?php
namespace XP\OrderPdf\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\ScopeInterface;

/**
 * Exports PDF output
 */
class PdfExporter
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var DateTime
     */
    private DateTime $dateTime;

    /**
     * @var ResolverInterface
     */
    private ResolverInterface $localeResolver;

    private $outputName = null;

    private const OUTPUT_FORMAT = '.pdf';

    /**
     * @var bool
     */
    private bool $rtl = false;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DateTime $dateTime,
        ResolverInterface $localeResolver
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->dateTime = $dateTime;
        $this->localeResolver = $localeResolver;
    }

    /**
     * @param mixed $pdf
     * @param mixed $orderModel
     * @return bool
     */
    public function exportPdf($pdf, $orderModel): bool
    {
        $outputType = $this->getConfig('xp_orderpdf/general/display_inline') ? 'I' : 'D';
        $pdfInstance = $pdf->setRTL($this->rtl)->getPdf($orderModel);
        // No pages were printed
        if (!$pdfInstance->getNumPages()) {
            return false;
        }
        $pdfInstance->Output($this->getPdfOutputName($orderModel), $outputType);
        return true;
    }

    /**
     * @param mixed $orderModel
     * @return string
     */
    public function getPdfOutputName($orderModel): ?string
    {
        if ($this->outputName == null) {
            $this->setOutputName($orderModel);
        }
        return $this->outputName;
    }

    /**
     * @param mixed $orderModel
     * @param string $outputName
     * @param bool $append
     * @return $this
     */
    public function setOutputName($orderModel, string $outputName = '', bool $append = true): PdfExporter
    {
        if (!$append || !$orderModel) {
            $this->outputName = $outputName;
            return $this;
        }
        $orderId = (!$orderModel instanceof AbstractDb) ?
            'Order#' . $orderModel->getIncrementId() : "Orders";
        $this->outputName = $outputName
            . $orderId. '_' . $this->dateTime->date('Y-m-d_H-i-s') . static::OUTPUT_FORMAT;
        return $this;
    }

    /**
     * @param string|null $path
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getRtl()
    {
        return $this->rtl;
    }

    /**
     * @param string|null $locale
     * @return $this
     */
    public function setRtl(string $locale = null): PdfExporter
    {
        if (is_numeric($locale)) {
            $this->rtl = (int)$locale;
            return $this;
        }
        if ($locale == null) {
            $locale = $this->localeResolver->getLocale();
        }
        $this->rtl = ($locale == 'ar' || $locale == 'ar_SA');
        return $this;
    }
}
