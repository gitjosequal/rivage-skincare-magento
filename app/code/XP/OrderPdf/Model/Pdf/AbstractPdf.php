<?php
namespace XP\OrderPdf\Model\Pdf;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Payment\Helper\Data;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Pdf;
use Magento\Sales\Model\Order\Pdf\Config;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use TCPDF;
use XP\OrderPdf\Model\Config\ConfigPool;

use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;


/**
 * Class AbstractPdf
 * @package XP\Pdf\Model\Order
 */
abstract class AbstractPdf extends Pdf\AbstractPdf
{
    protected $common_font = 'jannalt';
    protected $_price_fix_font = 'dejavusans';
    public $defaultX = 15;
    public $defaultWidth = 73;
    public $defaultFontSize = 10;

    protected $_rtl = false;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $_productRepository;

    /**
     * @var AttributeOptionInterfaceFactory
     */
    protected AttributeOptionInterfaceFactory $optionFactory;

    /**
     * @var Configurable
     */
    protected Configurable $_configurableOption;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var RequestInterface
     */
    protected RequestInterface $_request;

    /**
     * @var CustomerRepositoryInterface
     */
    private CustomerRepositoryInterface $customerRepository;

    /**
     * @var Image
     */
    protected Image $_imageHelper;

    /**
     * @var InvoiceRateFormatter
     */
    protected InvoiceRateFormatter $_invoiceRateFormatter;

    /**
     * @var ConfigPool
     */
    protected ConfigPool $_configPool;

    /**
     * @var string
     */
    protected string $_documentType = 'order';

    /**
     * @var int
     */
    protected $_h = 0;

    protected bool $_itemsHaveThumbnail = false;


    /**
     * @var \Zend_Pdf|TCPDF|null
     */
    protected $_pdf = null;

    protected $_orderData = null;

    /**
     * layout of the block
     *
     * @var LayoutInterface
     */
    protected $_layout;

    /**
     * @var array|string[]
     */
    protected array $_amountKeys = [
        "base_cost",
        "price",
        "base_price",
        "original_price",
        "base_original_price",
        "tax_amount",
        "base_tax_amount",
        "tax_invoiced",
        "base_tax_invoiced",
        "discount_amount",
        "base_discount_amount",
        "discount_invoiced",
        "base_discount_invoiced",
        "amount_refunded",
        "base_amount_refunded",
        "base_row_total",
        "row_total",
        "row_invoiced",
        "base_row_invoiced",
        "base_tax_before_discount",
        "tax_before_discount",
        "price_incl_tax",
        "base_price_incl_tax",
        "row_total_incl_tax",
        "base_row_total_incl_tax",
        "discount_tax_compensation_amount",
        "base_discount_tax_compensation_amount",
        "discount_tax_compensation_invoiced",
        "base_discount_tax_compensation_invoiced",
        "discount_tax_compensation_refunded",
        "base_discount_tax_compensation_refunded",
        "tax_cancelled",
        "discount_tax_compensation_cancelled",
        "tax_refunded",
        "base_tax_refunded",
        "discount_tax_refunded",
        "base_discount_tax_refunded",
        "paypal_price",
        "paypal_row_total",
    ];

    /**
     * @var array|string[]
     */
    protected array $_qtyRows = ["qty_ordered", "qty_canceled", "qty_refunded", "qty_shipped", "qty_backordered", "qty_invoiced"];

    private array $PDFConfigData = [];

    const PDF_PAGE_PORTRAIT = 'P';
    const PDF_PAGE_LANDSCAPE = 'L';
    //const PDF_PAGE_FORMATS = ['A4', 'A5', 'A6', 'A7'];
    const PDF_PAGE_FORMAT_A4 = 'A4';
    const PDF_PAGE_FORMAT_A5 = 'A5';
    const PDF_PAGE_FORMAT_A6 = 'A6';
    const PDF_PAGE_FORMAT_A7 = 'A7';

    const PDF_INLINE = 'I';
    const PDF_DOWNLOAD = 'D';
    const PDF_UNIT_MM = 'mm';
    const PDF_UNIT_CM = 'cm';

    const PDF_AUTHOR = 'Extended Patterns';
    const PDF_SUBJECT = 'Rivage';
    const PDF_KEYWORDS = 'Flowers Delivery Online, flowers online, flowers delivery, Buy flowers online, Order flowers '
    . 'online, flowers delivery Riyadh, wedding flowers, anniversary flowers.';
    const PDF_COMPANY_INFORMATION = 'Rivage';

    const XML_PATH_XP_PDF = 'xp_orderpdf';

    /**
     * OrderPdf constructor.
     * @param Data $paymentData
     * @param StringUtils $string
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @param Config $pdfConfig
     * @param Pdf\Total\Factory $pdfTotalFactory
     * @param Pdf\ItemsFactory $pdfItemsFactory
     * @param TimezoneInterface $localeDate
     * @param StateInterface $inlineTranslation
     * @param Renderer $addressRenderer
     * @param StoreManagerInterface $storeManager
     * @param ResolverInterface $localeResolver
     * @param ProductRepositoryInterface $_productRepository
     * @param AttributeOptionInterfaceFactory $optionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param CustomerRepositoryInterface $customerRepository
     * @param Configurable $configurable
     * @param RequestInterface $request
     * @param Image $_imageHelper
     * @param InvoiceRateFormatter $_invoiceRateFormatter
     * @param ConfigPool $configPool
     * @param LayoutInterface $layout
     * @param array $data
     * @param Database|null $fileStorageDatabase
     */
    public function __construct(
        Data                                                         $paymentData,
        StringUtils                                                  $string,
        ScopeConfigInterface                                         $scopeConfig,
        Filesystem                                                   $filesystem,
        Config                                                       $pdfConfig,
        Pdf\Total\Factory                                            $pdfTotalFactory,
        Pdf\ItemsFactory                                             $pdfItemsFactory,
        TimezoneInterface                                            $localeDate,
        StateInterface                                               $inlineTranslation,
        Renderer                                                     $addressRenderer,
        StoreManagerInterface                                        $storeManager,
        ResolverInterface                                            $localeResolver,
        ProductRepositoryInterface                                   $_productRepository,
        AttributeOptionInterfaceFactory                              $optionFactory,
        PriceCurrencyInterface                                       $priceCurrency,
        CustomerRepositoryInterface                                  $customerRepository,
        Configurable                                                 $configurable,
        RequestInterface                                             $request,
        Image                                                        $_imageHelper,
        InvoiceRateFormatter                                         $_invoiceRateFormatter,
        ConfigPool                                                   $configPool,
        LayoutInterface                                              $layout,
        array                                                        $data = [],
        Database                                                     $fileStorageDatabase = null
    )
    {
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
        $this->_productRepository = $_productRepository;
        $this->optionFactory = $optionFactory;
        $this->_priceCurrency = $priceCurrency;
        $this->customerRepository = $customerRepository;
        $this->_configurableOption = $configurable;
        $this->_request = $request;
        $this->_imageHelper = $_imageHelper;
        $this->_invoiceRateFormatter = $_invoiceRateFormatter;
        $this->_configPool = $configPool;
        $this->_layout = $layout;
        /**
         * Init TCPDF constants to init directory
         */
        if (!defined('K_PATH_MAIN')) {
            define('K_PATH_MAIN', dirname(__FILE__) . '/');
        }
        if (!defined('K_PATH_FONTS')) {
            define('K_PATH_FONTS', K_PATH_MAIN . 'tcpdf/fonts/');
        }
        $this->_pdf = $this->getPdfInstance();
        parent::__construct($paymentData, $string, $scopeConfig, $filesystem, $pdfConfig, $pdfTotalFactory, $pdfItemsFactory, $localeDate, $inlineTranslation, $addressRenderer, $data, $fileStorageDatabase);
    }

    /**
     * Retrieve layout object
     *
     * @return LayoutInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLayout()
    {
        if (!$this->_layout) {
            throw new \Magento\Framework\Exception\LocalizedException(
                new \Magento\Framework\Phrase('Layout must be initialized')
            );
        }
        return $this->_layout;
    }

    /**
     * @param \TCPDF $pdf
     */
    protected function _setLang(&$pdf)
    {
        if ($this->_isLocaleRTL()) {
            $l = [];
            $l['a_meta_charset'] = 'UTF-8';
            $l['a_meta_dir'] = 'rtl';
            $l['a_meta_language'] = 'ar';
            $l['w_page'] = 'صفحة';
            $pdf->setLanguageArray($l);
        }
    }

    protected function _isLocaleRTL()
    {
        return $this->_rtl;
    }

    public function setRTL($rtl = false)
    {
        $this->_rtl = $rtl;
        return $this;
    }
	
	 function getQrCode($orderModel){
        return '';
        $grandTotal = $orderModel->getGrandTotal();
        $vatTotal = $orderModel->getTaxAmount();
        if ($this->_isUSDCurrency()) {
            $rate = $this->_invoiceRateFormatter->getUSDRate();
            $grandTotal *= $rate;
            $vatTotal *= $rate;
        }

        $date = $this->_localeDate->date(new \DateTime($orderModel->getCreatedAt()))->format(\DateTime::ATOM);

        $img_base64_encoded = GenerateQrCode::fromArray([
            new Seller(self::PDF_COMPANY_INFORMATION), // seller name        
            new TaxNumber($this->getConfig('general/store_information/merchant_vat_number')), // seller tax number
            new InvoiceDate($date), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($this->_formatAmountInDecimal($grandTotal)), // invoice total amount
            new InvoiceTaxAmount($this->_formatAmountInDecimal($vatTotal)) // invoice tax amount
        ])->render();
        
        return '<td style="width: 12%;text-align:center"><img src="@' . preg_replace('#^data:image/[^;]+;base64,#', '', $img_base64_encoded) . '" width="100" height="100"></td>';
    }

    /**
     * Before getPdf processing
     *
     * @return void
     */
    protected function _beforeGetPdf()
    {
        $this->common_font = 'jannalt';
        if ($this->_isLocaleRTL()) {
            $this->defaultX = 6.5;
            $this->defaultWidth = 70;
        }
    }

    /**
     * @param array $custom_dimensions
     * @return array
     * @throws NoSuchEntityException
     * @throws \Zend_Pdf_Exception
     */
    protected function _getPdfLogo(array $custom_dimensions = []): array
    {
        $image = $this->_scopeConfig->getValue(
            'sales/identity/logo',
            ScopeInterface::SCOPE_STORE,
            null
        );
        $imagePath = '/sales/store/logo/' . $image;
        if (!$image || !$this->_mediaDirectory->isFile($imagePath)) {
            return [
                "html" => "<img src=\"https://rivage.ae/media/logo/stores/13/logo2_1_.png\" alt=\"Rivage\" width=\"100%\" height=\"60px\" border=\"0\" style=\"display: inline-block;float: left;\"/>",
                "width" => 200,
                "height" => 100
            ];
        }
        $imagePath = '\\sales\\store\\logo\\' . preg_replace('/[\\s\/]+/', '\\', $image);
        $image_file = $this->_mediaDirectory->getAbsolutePath($imagePath);
        $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
        //top border of the page
        $widthLimit = 71;
        //half of the page width
        $heightLimit = 69;
        //assuming the image is not a "skyscraper"
        $width = $image->getPixelWidth();
        $height = $image->getPixelHeight();

        //preserving aspect ratio (proportions)
        $ratio = $width / $height;
        if ($ratio > 1 && $width > $widthLimit) {
            $width = $widthLimit;
            $height = $width / $ratio;
        } elseif ($ratio < 1 && $height > $heightLimit) {
            $height = $heightLimit;
            $width = $height * $ratio;
        } elseif ($ratio == 1 && $height > $heightLimit) {
            $height = $heightLimit;
            $width = $widthLimit;
        }
        if (isset($custom_dimensions['width'])) {
            $width = $custom_dimensions['width'];
        }
        if (isset($custom_dimensions['height'])) {
            $height = $custom_dimensions['height'];
        }

        return [
            "html" => "<img src=\"https://rivage.ae/media/logo/stores/13/logo2_1_.png\" alt=\"Rivage\" width=\"$width\" height=\"$height\" border=\"0\" style=\"display: block;\"/>",
            "width" => $width,
            "height" => $height
        ];
    }
    
    protected function getTotalsHtml($order, array $config = [], bool $isTableData = true)
    {
        $itemsTotal = [];
    
        $trStyles = $config["tr"] ?? "";
        $tdStyles = $config["td"] ?? "";
        $grandTotalStyle = $config["grand_total_style"] ?? "";
        $textAlign = $this->_isLocaleRTL() ? 'text-align: right;' : 'text-align: left;';
    
        // Extract amounts
        $subtotal = $order->getSubtotal();
        
        $shippingInclTax = $order->getShippingAmount() / 1.05;
        $shippingTax = $order->getShippingTaxAmount();
        $shippingExclTax = $shippingInclTax - $shippingTax;
    
        $taxAmount = $order->getTaxAmount() + ($order->getShippingAmount() * 0.05);
        $grandTotal = $order->getGrandTotal();
        $totalBeforeTax = $subtotal + $shippingExclTax;
        
        $originalSubtotal = 0;

        foreach ($order->getAllVisibleItems() as $item) {
            $originalSubtotal += $item->getOriginalPrice() * $item->getQtyOrdered();
        }
        
        // Format helper
        $format = function ($value) {
            return $this->_priceCurrency->format($value, true, 2);
        };
    
        $rows = [
            ['label' => __('Original Subtotal'), 'value' => $format($originalSubtotal)],
       //     ['label' => __('Invoice Subtotal After Discount'), 'value' => $subtotal],
            ['label' => __('Shipping (Without Tax)'), 'value' => $format($shippingExclTax)],
            ['label' => __('Total Before Tax'), 'value' => $format($totalBeforeTax)],
            ['label' => __('Tax Amount'), 'value' => $format($taxAmount)],
            ['label' => '<span style="font-weight:bold; font-size:16px;">' . __('Total Amount') . '</span>', 'value' => '<span style="font-weight:normal; font-size:16px;">' . $format($grandTotal) . '</span>', 'is_bold' => true],
        ];
    
        foreach ($rows as $row) {
            $value = is_string($row['value']) ? $row['value'] : $format($row['value']);
            $style = isset($row['is_bold']) ? $grandTotalStyle : $tdStyles;
    
            $itemsTotal[] = [
                'heading' => [
                    'styles' => $style,
                    'html' => $row['label']
                ],
                'content' => [
                    'styles' => $textAlign . ' ' . $style,
                    'html' => $value
                ]
            ];
        }
    
        return $itemsTotal;
    }


    /**
     * @param Order|OrderInterface $order
     * @param array $config
     * @param bool $isTableData
     * @return array
     */
    protected function getTotalsHtmlOLD($order, array $config = [], bool $isTableData = true)
    {
        $itemsTotal = [];
        $totals = $this->_getTotalsList($order);
        print_r($totals);die;
        if (!$totals) {
            return $itemsTotal;
        }
        $trStyles = $config["tr"] ?? "";
        $tdStyles = $config["td"] ?? "";
        foreach ($totals as $total) {
           
            $total->setOrder($order)
                ->setSource($order);
            // Order Source fix.
            $total->getSource()->setOrder($order);
            if ($total->canDisplay()) {
                $total->setFontSize(10);
                $is_bold = false;
                if ($total->getSourceField() == 'grand_total') {
                    $is_bold = true;
                    $tdStyles = $config["grand_total_style"] ?? "";
                }
                $USDAmount = $this->_checkAndConvertPriceInUSD($order, $total->getData('source_field'));
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    // Fix Discount label when coupon code is empty.
                    if ($total->getTitleSourceField() == "discount_description" && !$order->getCouponCode()) {
                        $totalData['label'] = __($total->getTitle());
                    }
                    
                    $orderSourceAmount = $order->getData($total->getSourceField());
                    /**
                     * Order Model Totals
                     */
                    
                  //  echo $total->getSourceField() . '------' ;
                    if (isset($this->_orderData[$total->getSourceField()])) {
                        if('shipping_amount' == $total->getSourceField()){
                            
                            $totalValue = $this->_orderData[$total->getSourceField()] / 1.05;
                        }else{
                            $totalValue = $this->_orderData[$total->getSourceField()];
                            
                        }
                        $totalData['amount'] = $this->_priceCurrency->format(
                            $totalValue,
                            true,
                            2
                        );
                        $orderSourceAmount = $totalValue;
                    }
                    /**
                     * Split Amount for Arabic store and append the currency separately.
                     */
                    if ($this->_isLocaleRTL() && $orderSourceAmount) {
                        $totalData['amount'] = $this->_formatArabicPrice($orderSourceAmount);
                    }

                    if ($USDAmount) {
                        $totalData['amount'] = $USDAmount;
                    }
                    if ($is_bold) {
                        $totalData['label'] = '<span style="font-weight: bold; font-size: 16px; ">' . $totalData['label'] . '</span>';
                        $totalData['amount'] = '<span style="font-weight: normal; font-size: 16px; ">' . $totalData['amount'] . '</span>';
                    }
                    if ($isTableData) {
                        $text_align = 'text-align: left;';
                        if ($this->_isLocaleRTL()) {
                            $text_align = 'text-align: right;';
                        }
                        $itemsTotal[] = [
                            "heading" => [
                                "styles" => $tdStyles,
                                "html" => $totalData['label']
                            ],
                            "content" => [
                                "styles" => $text_align . ' ' . $tdStyles,
                                "html" => $totalData['amount']
                            ]
                        ];
                    } else {
                        $itemsTotal[] = [
                            "heading" => [
                                "styles" => '',
                                "html" => $totalData['label']
                            ],
                            "content" => [
                                "styles" => '',
                                "html" => $totalData['amount']
                            ]
                        ];
                    }
                }
            }
        }
        
      //  print_R($itemsTotal);die;
        return $itemsTotal;
    }

    protected function _checkAndConvertPriceInUSD($order, $sourceField)
    {
        if (!$this->_isUSDCurrency()) {
            return false;
        }
        return $this->_invoiceRateFormatter->getFormattedUSDAmount($order, $sourceField);
    }

    protected function _isUSDCurrency()
    {
        return $this->getRequest()->getParam('usd', false);
    }

    /**
     * Initialize renderer process
     *
     * @param string $type
     * @return void
     */
    protected function _initRenderer($type)
    {
        $this->_documentType = $type;
        parent::_initRenderer($type);
    }

    public function getSelectedOptions($options)
    {
        $result = [];
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }

    public function getLocaleStoreId()
    {
        $storeId = null;
        $stores = $this->_storeManager->getStores(true, true);
        if (empty($stores)) {
            return $storeId;
        }
        $storeCode = 'en';
        if ($this->_isLocaleRTL()) {
            $storeCode = 'ar';
        }

        if (isset($stores[$storeCode])) {
            $storeId = $stores[$storeCode]->getId();
        }
        return $storeId;
    }

    public function getCustomer($customerId)
    {
        if (!$customerId) {
            return false;
        }

        try {
            return $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return false;
        } catch (LocalizedException $e) {
            return false;
        }
    }

    /**
     * @param $order
     * @return string
     */
    protected function _getPaymentMethodInfo($order): string
    {
        $payment_method = '';
        $paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);
        foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $payment_method .= strip_tags(trim($_value));
                }
            }
        }
        return $payment_method;
    }

    /**
     * @param $order
     * @return string
     */
    protected function _getSenderDescription($order): string
    {
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$escaper = $objectManager->create('Magento\Framework\Escaper');
        $text = "";
        $sender_name = $order->getData('checkout_message_from') ? $escaper->escapeHtml($order->getData('checkout_message_from')) : '';
        $sender_name = $sender_name ? $sender_name : '';
        $text .= "<p>".__("Sender Name: ")." " . $sender_name . "</p>";

        $sender_phone = $escaper->escapeHtml($order->getData('checkout_sender_phone'));
        $sender_phone = $sender_phone ? $sender_phone : '';
        $text .= "<p>".__("Sender Phone: ")." " . $sender_phone . "</p>";

        $message = $escaper->escapeHtml($this->remove_emoji(json_decode($order->getData('checkout_message_data'))));
        $message = str_replace('🫂','',$message);
		$message = $message ? $message : '';


        $text .= "<p>".__("Message: ")." <span style='direction: rtl'>" . $message . "</span></p>";

        $hide = $escaper->escapeHtml($order->getData('checkout_hide_from'));
        $hide = $hide != null ? $hide : 0;
        $hide = $hide == 0 ? __("Yes") : __('No');
        $text .= "<p>".__("Hide Sender Name: ")." " . $hide . "</p>";
        return $text;
    }
	
	private function remove_emoji($string)
	{
		// Match Enclosed Alphanumeric Supplement
		$regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
		$clear_string = preg_replace($regex_alphanumeric, '', $string);

		// Match Miscellaneous Symbols and Pictographs
		$regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
		$clear_string = preg_replace($regex_symbols, '', $clear_string);

		// Match Emoticons
		$regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
		$clear_string = preg_replace($regex_emoticons, '', $clear_string);

		// Match Transport And Map Symbols
		$regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
		$clear_string = preg_replace($regex_transport, '', $clear_string);

		// Match Supplemental Symbols and Pictographs
		$regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
		$clear_string = preg_replace($regex_supplemental, '', $clear_string);

		// Match Miscellaneous Symbols
		$regex_misc = '/[\x{2600}-\x{26FF}]/u';
		$clear_string = preg_replace($regex_misc, '', $clear_string);

		// Match Dingbats
		$regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
		$clear_string = preg_replace($regex_dingbats, '', $clear_string);

		return $clear_string;
	}

    /**
     * @param $order
     * @return string
     */
    protected function _getReceiverDescription($order): string
    {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$escaper = $objectManager->create('Magento\Framework\Escaper');
        $text = "";
        $receiver_name = $escaper->escapeHtml($order->getData('checkout_receiver_name'));
        $receiver_name = $receiver_name ? $receiver_name : '';
        $text .= "<p>".__("Receiver Name: ")." " . $receiver_name . "</p>";

        $receiver_phone = $escaper->escapeHtml($order->getData('checkout_receiver_phone'));
        $receiver_phone = $receiver_phone ? $receiver_phone : '';
        $text .= "<p>".__("Receiver Phone: ")." " . $receiver_phone . "</p>";

        $know = $escaper->escapeHtml($order->getData('checkout_receiver_unknown_address'));
        $know = $know != null ? $know : 0;
        $know = $know == 0 ? __("Yes") : __('No, Please Call The Receiver to get the address');
        $text .= "<p>".__("Sender Know the receiver address: ")." " . $know . "</p>";

        $shippingaddress = $order->getShippingAddress(); 
        $address = $shippingaddress ? $shippingaddress->getStreet() : [];
        $address = isset($address[0]) ? $address[0] : '';
        $text .= "<p>".__("Receiver Address: ")." <span style='direction: rtl'>" . $address . "</span></p>";

        $note = $escaper->escapeHtml($order->getData('checkout_delivery_note'));
        $note = $note ? $note : '-';
        $text .= "<p>".__("Delivery Note: ")." " . $note . "</p>";
        return $text;
        
    }

    protected function _getProductItemsColHtml($order, $columns = ["name" => ["w" => "80%"], 'subtotal' => ["w" => "10%"]], array $tr_config = [])
    {
        $itemsHtml = '';
        if (!$order->getAllItems()) {
            return $itemsHtml;
        }
        $i = 0;
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()
                || ($this->_documentType == "invoice" && !(int)$item->getData('qty_invoiced'))
                || ($this->_documentType == "creditmemo" && !((int)$item->getData('qty_refunded') || (int)$item->getData('qty_cancelled')))
            ) {
                continue;
            }
            $options = $item->getProductOptions();
            $itemsHtml .= '<tr style="' . ($tr_config["tr_style"] ?? '') . '">';
            try {
                $product = $this->_productRepository->getById($item->getProductId(), false, $this->getLocaleStoreId());
                $options_html = '';
                $selectedOptions = [];
                if ($options) {
                    $selectedOptions = $this->getSelectedOptions($item->getProductOptions());
                    foreach ($product->getOptions() as $productOptions) {
                        foreach ($selectedOptions as $selectedOption) {
                            if ($selectedOption['option_id'] != $productOptions->getOptionId()) {
                                continue;
                            }
                            // Selected Option values
                            $selectedValue = explode(',', $selectedOption['option_value']);
                            $values = $productOptions->getValues();
                            $optionHtmlValues = [];
                            foreach ($values as $value) {
                                if (in_array($value->getOptionTypeId(), $selectedValue)) {
                                    $selectedOption['value'] = $value->getTitle();
                                    $selectedOption['print_value'] = $value->getTitle();
                                    $optionHtmlValues[] = $value->getTitle();
                                }
                            }
                            $options_html .= '<br /><strong style="font-weight: bolder;">' . $productOptions->getTitle() . '</strong> - '
                                . implode(',', $optionHtmlValues);
                        }
                    }
                }
                $configurableOptionsHtml = '';
                $configurable = $this->_configurableOption->getConfigurableAttributesAsArray($product);
                foreach ($selectedOptions as $selectedOption) {
                    if (!isset($configurable[$selectedOption['option_id']])) {
                        continue;
                    }

                    $optionHtmlValues = [];
                    foreach ($configurable[$selectedOption['option_id']]['options'] as $option) {
                        if ($selectedOption['option_value'] != $option['value']) {
                            continue;
                        }

                        $optionHtmlValues [] = $option['label'];
                    }
                    $configurableOptionsHtml .= '<br /><strong>' . $configurable[$selectedOption['option_id']]['frontend_label'] . '</strong> - '
                        . implode(',', $optionHtmlValues);
                }
                if (in_array("thumbnail", array_keys($columns))) {
                    $this->_itemsHaveThumbnail = true;
                }
                
                foreach ($columns as $key => $column) {
                    $col = $item->getData($key);
                    if (!$item->getIsQtyDecimal() && in_array($key, $this->_qtyRows)) {
                        $col = (int) $col;
                    }
                    if (in_array($key, ["name", "sku"]) || $key == "thumbnail") {
                        $col = $product->getData($key);
                    }
                    if (in_array($key, $this->_amountKeys)) {
                        
                        $col = $this->_formatArabicPrice($col, $this->_itemsHaveThumbnail);
                        
                        if (!$this->_isLocaleRTL() || $this->_isUSDCurrency()) {
                            /**
                             * Format currency either in USD or the configured currency
                             */
                            $col = $this->_isUSDCurrency() ?
                                $this->_invoiceRateFormatter->getFormattedAndConvertedAmount(
                                    count(explode('base_', $col)) > 1 ? 'base_' . $col : $col
                                ) : $this->_priceCurrency->format($col, true, 2);
                        }
                    }
                   
                    if ($key == "thumbnail") {
                        $imageWidth = $this->getConfig(self::XML_PATH_XP_PDF . '/items/thumbnail_width') ?? 80;
                        $imageHeight = $this->getConfig(self::XML_PATH_XP_PDF . '/items/thumbnail_height') ?? 90;

                        $productImagePath = '\\catalog\\product\\'
                            . preg_replace('/[\\s\/]+/', '\\', $product->getImage());
                        $image_path = $this->_mediaDirectory->getAbsolutePath($productImagePath);
                        $col = "<img src=\"" . $image_path . "\" width=\"" . $imageHeight . "px\" height=\""
                            . $imageHeight . "px\" alt=\"" . $item->getName() . "\"/>";
                        // Align content at center
                        $tr_config["td_style"] .= "  direction: rtl; text-align: left; ";
                    }
                    
                    if($key == 'tax_amount'){
                        $col = $this->_priceCurrency->format($item->getOriginalPrice());
                        
                    }
                    if ($col) {
                        if ($key == "name") {
                            $col .= $options_html;
                            $col .= $configurableOptionsHtml;
                        }
                        $itemsHtml .= '<td style="width: ' .  $column["w"] . ';' . ($tr_config["td_style"] ?? '') .'">' . $col . '</td>';
                        
                    }
                    


                }
            } catch (\Exception $e) {
                foreach ($columns as $key => $column) {
                    $col = $item->getData($key);
                    $itemsHtml .= '<td style="width: ' .  $column["w"] . ';' . ($tr_config["td_style"] ?? '') .'">' . $col . '</td>';
                }
            }
            $itemsHtml .= "</tr>";
            $i++;
        }
        return $itemsHtml;
    }

    /**
     * @param Order|Order\Invoice $order
     * @param string $pdfType
     * @param array $exclude
     * @return array
     * @throws NoSuchEntityException
     * @throws \Zend_Pdf_Exception
     */
    protected function _getPdfConfigData($order, string $pdfType = 'order', array $exclude = [])
    {
        $pdf_logo = $this->_getPdfLogo([
            "width" => $this->getConfig('sales/identity/logo_width'),
            "height" => $this->getConfig('sales/identity/logo_height')
        ]);
        $text_align = ' text-align: ' . ($this->_isLocaleRTL() ? 'right' : 'left') . '; ';
        $alt_text_align = ' text-align: ' . ($this->_isLocaleRTL() ? 'left' : 'right') . '; '; // alternate

        // Load configurations from Config Pool
        $configPool = $this->_configPool->getConfig();

        $topHeaderContentWidth = 100;
        if (isset($pdf_logo["html"])) {
            $topHeaderContentWidth -= 56;
            $pdf_logo = '<td style="width: 56%; margin-top: 5px; ' . ($this->_isLocaleRTL() ? $alt_text_align : $text_align) . '">' . $pdf_logo["html"] .'</td>';
        } else {
            $pdf_logo = '';
        }
        $configPool['top_header']['td'] .= "width: {$topHeaderContentWidth}%;";

        $rtl_style = $this->_isLocaleRTL() ? ' direction: rtl; ' : '';

        foreach ($configPool as $key => $configItem) {
            $configPool[$key]['tbl'] .= "{$rtl_style}";
            if ($key == "totals") {
                // Adjust text to alternate side
                $configPool[$key]['tbl'] .= "{$alt_text_align}";
                continue;
            }
            $configPool[$key]['tbl'] .= "{$text_align}";
        }

        $columns = explode(",", $this->getConfig(self::XML_PATH_XP_PDF . '/items/columns'));

        $items = [];
        foreach ($columns as $column) {
            $w = 10;
            switch ($column) {
                case "name":
                    $w = 17;
                    $this->_calculateCommonWidthColumns($w, $columns);
                    break;
                case "sku":
                    $w = 13;
                    break;
                 case "qty":
                    $w = 10;
                    break;
                case "price": case "subtotal":  case "tax":  case "thumbnail":
                    $w = 15;
                    break;
            }
            // Assign column to ColIndex for default value
            $colIndex = $column;
            switch ($column) {
                case "qty":
                    $ordered = ($this->_documentType == "invoice" ? 'invoiced'
                        : (($this->_documentType == "creditmemo" ? 'refunded' : 'ordered'))
                    );
                    $colIndex = "{$column}_{$ordered}";
                    break;
                case "subtotal": $colIndex = "row_total_incl_tax"; break;
                case "tax": $colIndex = "tax_amount"; break;
                case "price": $colIndex = "price_incl_tax"; break;
            }
            $items [$colIndex] = [
                "column" => __(ucfirst($column)),
                "w" => "{$w}%"
            ];
        }
        $originalOrderModel = $this->_getOrderData($order);
        
        $logoHtml = $this->getLayout()
                    ->createBlock(\XP\OrderPdf\Block\Config::class)
                    ->setData('top_header_data', array_merge($configPool['top_header'], [
                        'logo' => $pdf_logo,
                        'address' => $this->getConfig('sales/identity/address')
                    ]))
                    ->setTemplate('XP_OrderPdf::config/header.phtml')->toHtml();
                    

        $orderInfo = $this->getLayout()
                    ->createBlock(\XP\OrderPdf\Block\Config::class)
                    ->setData('order_info', array_merge($configPool['order_info'], [
                        "text_align" => $text_align,
                        "rtl_style" => $rtl_style,
                        "pdf_type" => $pdfType,
                        "created_at" => ($this->_localeDate->formatDate(
                            $order->getCreatedAt(),
                            \IntlDateFormatter::MEDIUM,
                            true
                        ))
                    ]))
                    ->setOrder($originalOrderModel) // Order data array
                    ->setTemplate('XP_OrderPdf::config/order_info.phtml')->toHtml();
                    

        
     
        $this->PDFConfigData = [
            /*"top_header" => [
                "html" => $this->getLayout()
                    ->createBlock(\XP\OrderPdf\Block\Config::class)
                    ->setData('top_header_data', array_merge($configPool['top_header'], [
                        'logo' => $pdf_logo,
                        'address' => $this->getConfig('sales/identity/address')
                    ]))
                    ->setTemplate('XP_OrderPdf::config/header.phtml')->toHtml(),
                "new_line" => $configPool['top_header']['new_line']
            ],*/
            "order_info" => [
                "html" =>  $orderInfo,
                "new_line" => $configPool['order_info']['new_line']
            ],
            "addresses" => [
                "html" => $this->getLayout()
                    ->createBlock(\XP\OrderPdf\Block\Config::class)
                    ->setData('addresses', $configPool['addresses'])
                    ->setOrder($order)
                    ->setTemplate('XP_OrderPdf::config/addresses.phtml')->toHtml(),
                "new_line" => !$order->getData('checkout_sender_phone') ? $configPool['addresses']['new_line'] : false
            ],
            "shipping and payment" => [
                "heading_text" => $this->getLayout()
                    ->createBlock(\XP\OrderPdf\Block\Config::class)
                    ->setData('shipping_data', array_merge($configPool['shipping and payment'], [
                        "shipping_description" => true,
                        "payment_method_info" => true
                    ]))
                    ->setIsHeading(true)
                    ->setTemplate('XP_OrderPdf::config/shipping_and_payment.phtml')->toHtml(),
                "html" => $this->getLayout()
                    ->createBlock(\XP\OrderPdf\Block\Config::class)
                    ->setData('shipping_data', array_merge($configPool['shipping and payment'], [
                        "shipping_description" => $this->_getShippingDescription(
                            $order,
                            $configPool['shipping and payment']['additional_config']
                        ),
                        "payment_method_info" => $this->_getPaymentMethodInfo($order)
                    ]))
                    ->setTemplate('XP_OrderPdf::config/shipping_and_payment.phtml')->toHtml(),
                "new_line" => !$order->getData('checkout_sender_phone') ? $configPool['shipping and payment']['new_line'] : false
            ],
            "sender and receiver" => [
                "heading_text" => $this->getLayout()
                    ->createBlock(\XP\OrderPdf\Block\Config::class)
                    ->setData('receiver_data', array_merge($configPool['sender and receiver'], [
                        "sender" => true,
                        "receiver" => true
                    ]))
                    ->setIsHeading(true)
                    ->setTemplate('XP_OrderPdf::config/sender_and_receiver.phtml')->toHtml(),
                "html" => $this->getLayout()
                    ->createBlock(\XP\OrderPdf\Block\Config::class)
                    ->setData('receiver_data', array_merge($configPool['sender and receiver'], [
                        "sender" => $this->_getSenderDescription($order),
                        "receiver" => $this->_getReceiverDescription($order)
                    ]))
                    ->setTemplate('XP_OrderPdf::config/sender_and_receiver.phtml')->toHtml(),
                "new_line" => $configPool['sender and receiver']['new_line']
            ],
            "items" => [
                "heading_text" => '',
                "html" => '',
                "new_line" => $configPool['items']['new_line']
            ],
            "totals" => [
                "html" =>$this->getTotalsHtml($order, $configPool['totals']),
                "new_line" => $configPool['totals']['new_line']
            ]
        ];

        foreach ($exclude as $itemToExclude) {
            if (isset($this->PDFConfigData[$itemToExclude])) {
                unset($this->PDFConfigData[$itemToExclude]);
            }
        }

        if(!$order->getData('checkout_sender_phone')){
            unset($this->PDFConfigData["sender and receiver"]);
        }

        if ($items && isset($this->PDFConfigData["items"])) {
           
            $this->PDFConfigData['items']['heading_text'] = $this->getLayout()
                ->createBlock(\XP\OrderPdf\Block\Config::class)
                ->setData('items_style', $configPool['items'])
                ->setItemColumns($items)
                ->setIsHeading(true)
                ->setTemplate('XP_OrderPdf::config/items.phtml')->toHtml();
            // Get Items HTML data.
            $itemsHtmlData = $this->_getProductItemsColHtml($order, $items, [
                'tr_style' => $configPool['items']['tr_content'],
                'td_style' => $configPool['items']['td_content']
            ]);
            $this->PDFConfigData["items"]["html"] = $this->getLayout()
                ->createBlock(\XP\OrderPdf\Block\Config::class)
                ->setData('items_style', $configPool['items'])
                ->setItemColumns($items)
                ->setItemsHtml($itemsHtmlData)
                ->setTemplate('XP_OrderPdf::config/items.phtml')->toHtml();
        }

        return $this->PDFConfigData;
    }

    protected function _getShippingDescription(Order $order, array $config = [])
    {
        $description =  $order->getShippingDescription();
        $description .= (isset($config['delivery_date'] )&& $config['delivery_date'] ?
            '<div class="shipping-description-title" style="margin-top: 4px;">' . $order->getShippingArrivalSlot() . '</div><br />'
            : '');
        if (!$order->getShippingDescription()) {
            return false;
        }
        $charges = $this->_formatArabicPrice($order->getShippingAmount());
        if (!$this->_isLocaleRTL() || $this->_isUSDCurrency()) {
            $charges = $this->_isUSDCurrency() ?
                $this->_invoiceRateFormatter->getFormattedAndConvertedAmount($order->getBaseShippingAmount()) :
                $this->_priceCurrency->format($order->getShippingAmount(), true, 2);
        }
        $description .= '<div class="shipping-charges">' . __('Total Shipping Charges') .' ' . $charges . '</div>';
        /*
         * @todo: ignore for now, causes rendering issues for emojis
         * $description .= (isset($config['delivery_comment']) ?
         *   '<div class="gift-comment">' . $order->getShippingArrivalComments() . '</div>' : '');
        */
        return $description;
    }

    /**
     * $amount: The price or amount.
     * $hasLTRStyle: Set true when text is being displayed in Left to right style for
     * Arabic store view in order to avoid the price rendering issue.
     *
     * @param mixed $amount
     * @param bool $hasLTRStyle
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _formatArabicPrice($amount, $hasLTRStyle = false)
    {
        if (!$this->_isLocaleRTL() || $this->_isUSDCurrency()) {
            return $amount;
        }

        $sign = $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
        if ($hasLTRStyle) {
            return "<span>{$sign}</span>" . $this->_formatAmountInDecimal($amount);
        }
        return $this->_formatAmountInDecimal($amount) . "<span>{$sign}</span>";
    }

    /**
     * @param mixed $amount
     * @return string
     */
    protected function _formatAmountInDecimal($amount)
    {
        return number_format((float)$amount, 2, '.', '');
    }

    /**
     * Calculates the width of skipped columns for items list.
     *
     * @param int|string $width
     * @param array|mixed $columns
     */
    protected function _calculateCommonWidthColumns(&$width, $columns)
    {
        $commonWithColumns = [
            "15" => ["thumbnail", "price", "tax", "subtotal"],
            "10" => ["qty"],
            "13" => ["sku"],
        ];
        foreach ($commonWithColumns as $widthKey => $colNames) {
            foreach ($colNames as $colName) {
                if (!in_array($colName, $columns)) {
                    $width += (int)$widthKey;
                }
            }
        }
    }

    /**
     * Original order array
     * @param $order
     * @return mixed
     */
    protected function _getOrderData($order)
    {
        if ($this->_orderData == null) {
            $this->_orderData = $order->toArray();
        }
        return $this->_orderData;
    }

    /**
     * Configure the PDF settings.
     */
    protected function _configurePdf()
    {
        /**
         * 1. Config PDF Header/Footer
         */
        $this->_pdf->setPrintHeader(false);
        $this->_pdf->setPrintFooter(false);

        /**
         * 2. Set Default Font to PDF
         */
        $this->_pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->_pdf->SetFont($this->common_font, '', $this->defaultFontSize);

        /**
         * 3. Adjust the conversion of pixels to user units
         * and image configurations.
         */
        $this->_pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->_pdf->setJPEGQuality(90);

        /**
         * 4. Configure cell paddings & spacing
         */
        //$this->_pdf->setCellPaddings(15, 3, 15, 3);
        $this->_pdf->setCellMargins(0, 0, 0, 0);
        $this->_pdf->setCellPaddings(10, 0, 10, 0);
        $this->_pdf->setCellHeightRatio(1);

        /**
         * 5. Set Pdf Information
         */
        $this->_pdf->SetAuthor(self::PDF_AUTHOR);
        $this->_pdf->SetCreator(self::PDF_AUTHOR);
        $this->_pdf->SetSubject(self::PDF_SUBJECT);
        $this->_pdf->SetKeywords(self::PDF_KEYWORDS);

        /**
         * Locale settings
         */
        $this->_setLang($this->_pdf);
    }

    protected function writeHtml($itemKey, $data, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
    {
        if (!in_array($itemKey, ['shipping and payment', 'items', 'totals']) && !in_array($itemKey, ['sender and receiver', 'items', 'totals'])) {
            $this->_pdf->writeHTML($data["html"], $ln, $fill, $reseth, $cell, $align);
            return;
        }
        
        // Fix RTL image
        if ($itemKey == "items" && $this->_isLocaleRTL() &&  $this->_itemsHaveThumbnail) {
            $this->_pdf->setRTL(!$this->_isLocaleRTL());
        }
        if (isset($data['heading_text'])) {
            $this->_pdf->writeHTML($data["heading_text"], 0, $fill, $reseth, $cell, $align);
        }
        /**
         * Render Cells for totals only.
         */
        if ($itemKey == "totals") {
            $positionX = ($this->_pdf->getPageWidth()/2);
            $positionX -= ($this->_isLocaleRTL() ? 5 : 0);
            $headingW = ($this->_isLocaleRTL() ? 55 : 50);
            $align = ($this->_isLocaleRTL() ? 'R' : 'L');
            foreach ($data["html"] as $d) {
                $this->_pdf->writeHTMLCell($headingW, 0.7, $positionX, $this->_pdf->GetY(),
                    '<div style="'
                    .  $d['heading']['styles']. '">' . $d['heading']['html'] .'</div>', 0,
                    0, false, true, $align);
                $this->_pdf->SetFont($this->_price_fix_font, '', $this->defaultFontSize);
                $this->_pdf->writeHTMLCell(50, $this->_pdf->getLastH(), $positionX + $headingW, $this->_pdf->GetY(),
                    '<div style="'
                    .  $d['heading']['styles']. '">' . $d['content']['html'] .'</div>', 0,
                    1 , false, true, $align);
                $this->_pdf->Ln(($this->_isLocaleRTL() ? 6 : 4));
                $this->_pdf->SetFont($this->common_font, '', $this->defaultFontSize);
            }
            return;
        }

        // Print separate heading
        $this->_pdf->SetFont($this->_price_fix_font, '', $this->defaultFontSize);
        $this->_pdf->writeHTML($data["html"], $ln, $fill, $reseth, $cell, $align);
        // Reset
        $this->_pdf->SetFont($this->common_font, '', $this->defaultFontSize);
        // Fix and RTL image
        if ($itemKey == "items" && $this->_isLocaleRTL() &&  $this->_itemsHaveThumbnail) {
            $this->_pdf->setRTL($this->_isLocaleRTL());
        }
    }

    /**
     * @param mixed $orderModel
     * @return string
     */
    protected function _getQRCodeConfigHtml($orderModel)
    {
        $grandTotal = $orderModel->getGrandTotal();
        $vatTotal = $orderModel->getTaxAmount();
        if ($this->_isUSDCurrency()) {
            $rate = $this->_invoiceRateFormatter->getUSDRate();
            $grandTotal *= $rate;
            $vatTotal *= $rate;
        }
        $date = $this->_localeDate->date(new \DateTime($orderModel->getCreatedAt()))->format('Y-m-d H:i:s');
        $qrHtml = self::PDF_COMPANY_INFORMATION . PHP_EOL;
        if ($vatNumber = $this->getConfig('general/store_information/merchant_vat_number')) {
            $qrHtml .= "VAT Number: " . $vatNumber . PHP_EOL;
        }
        if ($orderModel instanceof \Magento\Sales\Model\Order\Invoice) {
            $qrHtml .= "Invoice Number: " . $orderModel->getIncrementId() . PHP_EOL;
        }
        $qrHtml .= "Date: " . $date . PHP_EOL;
        $qrHtml .= "Total VAT: " . $this->_formatAmountInDecimal($vatTotal) . PHP_EOL;
        $qrHtml .= "Total Amount: " . $this->_formatAmountInDecimal($grandTotal);
        return $qrHtml;
    }

    protected function _addQRCode($text)
    {
        $style = [
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1
        ];
        $this->_pdf->write2DBarcode($text,
            'QRCODE,L',
            ($this->_isLocaleRTL() ? $this->_pdf->getPageWidth()/1.75 : $this->_pdf->getPageWidth()/2.5),
            $this->_pdf->GetY(),
            24,
            24,
            $style,
            '',
            true
        );
    }

    public static function getPageContentHeight(\TCPDF $pdf, $page)
    {
        // get total height of the page in user units
        $totalHeight = $pdf->getPageHeight($page) / $pdf->getScaleFactor();
        $margin = $pdf->getMargins();
        return $totalHeight - $margin['bottom'] - $margin['top'];
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->_request;
    }

    /**
     * @return TCPDF
     */
    public function getPdfInstance()
    {
        if ($this->_pdf == null) {
            $this->setRTL($this->_localeResolver->getLocale() == "ar_SA");
            $this->_pdf = new TCPDF(
                self::PDF_PAGE_PORTRAIT,
                self::PDF_UNIT_MM,
                self::PDF_PAGE_FORMAT_A4,
                true,
                'UTF-8',
                false
            );
        }
        return $this->_pdf;
    }

    public function getPdfTitle($orderModel)
    {
        return " #" . $orderModel->getIncrementId();
    }

    public function getConfig($path, $store = null)
    {
        return $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
