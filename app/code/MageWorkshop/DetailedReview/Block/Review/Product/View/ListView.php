<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Block\Review\Product\View;

/**
 * Class ListView
 *
 * @method string getDateFormat()
 */
class ListView extends \Magento\Review\Block\Product\View\ListView
{
    const EVENT_NAME_PREPARE_REVIEW_COLLECTION = 'prepare_review_collection_for_review_list';

    const XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_SHOW_READ_MORE
        = 'mageworkshop_detailedreview/general/show_read_more';

    const XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_SYMBOLS_IN_PREVIEW
        = 'mageworkshop_detailedreview/general/symbols_in_preview';

    const XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_CONVERT_NEWLINE_TO_BR_TAG
        = 'mageworkshop_detailedreview/general/convert_newline_to_br_tag';

    /** @var \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper */
    protected $attributeHelper;

    /** @var \MageWorkshop\Core\Helper\View */
    protected $viewHelper;

    /**
     * ListView constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper
     * @param \MageWorkshop\Core\Helper\View $viewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \MageWorkshop\DetailedReview\Helper\Attribute $attributeHelper,
        \MageWorkshop\Core\Helper\View $viewHelper,
        array $data
    ) {
        $this->attributeHelper = $attributeHelper;
        $this->viewHelper = $viewHelper;
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $collectionFactory,
            $data
        );
    }

    /**
     * @return \Magento\Review\Model\ResourceModel\Review\Collection
     */
    public function getReviewsCollection()
    {
        if ($this->_reviewsCollection === null) {
            $reviewsCollection = parent::getReviewsCollection();
            $this->_eventManager->dispatch(
                self::EVENT_NAME_PREPARE_REVIEW_COLLECTION,
                [
                    'collection' => $reviewsCollection,
                    'product'    => $this->getProduct()
                ]
            );
        }
        return $this->_reviewsCollection;
    }

    /**
     * Note: method is used in the template and in the $this::getOptions() method
     *
     * @return \MageWorkshop\DetailedReview\Model\ResourceModel\Attribute\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getReviewFormAttributes()
    {
        return $this->attributeHelper->getReviewFormAttributes($this->getProduct());
    }

    public function getLabelClass(\MageWorkshop\DetailedReview\Model\Attribute $attribute)
    {
        return $this->attributeHelper->isVisualSwatch($attribute)
            ? 'swatch'
            : $attribute->getFrontendInput();
    }

    /**
     * @param \MageWorkshop\DetailedReview\Model\Attribute $attribute
     * @param \Magento\Review\Model\Review $review
     * @return array|string
     */
    public function getAttributeValue(
        \MageWorkshop\DetailedReview\Model\Attribute $attribute,
        \Magento\Review\Model\Review $review
    ) {
        $value = (string) $review->getData($attribute->getAttributeCode());
        switch ($attribute->getFrontendInput()) {
            case 'textarea':
                if (empty($value)) {
                    break;
                }
                $value = $this->addReadMore($value);
                break;
            case 'multiselect':
                $labels = [];
                foreach (explode(',', $value) as $optionId) {
                    $labels[] = $this->getLabelForValue($attribute, $optionId);
                }
                $value = implode(', ', $labels);
                break;
            case 'select':
            case 'boolean':
                if ($value === '0' || $value === '') {
                    break;
                }
                $value = $this->getLabelForValue($attribute, $value);
                break;
            default:
                break;
        }

        return $value;
    }

    protected function getLabelForValue(\MageWorkshop\DetailedReview\Model\Attribute $attribute, $value)
    {
        $attributeOptions = $this->attributeHelper->getAttributeOptionValues($attribute);
        foreach ($attributeOptions as $option) {
            if ($option['value'] == $value) {
                return isset($option['src']) && $option['src']
                    ? $option
                    : $option['label'];
            }
        }
        return '';
    }

    /**
     * @return \MageWorkshop\Core\Helper\View
     */
    public function getViewHelper()
    {
        return $this->viewHelper;
    }

    /**
     * @param $value
     * @return string
     */
    public function addReadMore($value)
    {
        $value = $this->escapeHtml($value);
        $allowReadMore = $this->_scopeConfig->getValue(self::XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_SHOW_READ_MORE);
        $symbolsInPreview = (int) $this->_scopeConfig->getValue(self::XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_SYMBOLS_IN_PREVIEW);
        $convertNewlineToBrTag = $this->_scopeConfig->getValue(self::XML_PATH_MAGEWORKSHOP_DETAILEDREVIEW_CONVERT_NEWLINE_TO_BR_TAG);

        if ($allowReadMore
            && $symbolsInPreview
            && strlen($value) > $symbolsInPreview
        ) {
            // truncate string
            $teaser = substr($value, 0, $symbolsInPreview);

            if ($convertNewlineToBrTag) {
                $teaser = nl2br($teaser);
                $value = nl2br($value);
            }

            $html = "<span class='teaser'>{$teaser}</span>";

            $html .= "<span class='completeDescription'>{$value}</span>";
            $html .= '<span class="moreLink" data-less="' . __('...less') . '" data-more="' . __('...read more') . '">' . __('...read more') . '</span>';

            $value = $html;
        } elseif ($convertNewlineToBrTag) {
            $value = nl2br($value);
        }

        return $value;
    }
}
