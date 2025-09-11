<?php declare(strict_types=1);
/**
 *
 *           a88888P8
 *          d8'
 * .d8888b. 88        .d8888b. 88d8b.d8b. .d8888b. .dd888b. .d8888b.
 * 88ooood8 88        88'  `88 88'`88'`88 88ooood8 88'    ` 88'  `88
 * 88.  ... Y8.       88.  .88 88  88  88 88.  ... 88       88.  .88
 * `8888P'   Y88888P8 `88888P' dP  dP  dP `8888P'  dP       `88888P'
 *
 *           Copyright © eComero Management AB, All rights reserved.
 *
 */
namespace Ecomero\Analytics\Helper;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;

class Data extends AbstractHelper
{
    protected $configWriter;
    protected $configCollectionFactory;
    protected $unserializer;

    public function __construct(
        Context $context,
        WriterInterface $configWriter,
        CollectionFactory $configCollectionFactory,
        Json $unserializer
    ) {
        parent::__construct($context);
        $this->configWriter = $configWriter;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->unserializer = $unserializer;
    }

    public function getReportingCurency() : ?string
    {
        return $this->scopeConfig->getValue('analytics/currency/report_currency');
    }

    public function getExchangeRates() : ?array
    {
        $tableConfig =  $this->scopeConfig->getValue('analytics/currency/exchange_rates');
        if ($tableConfig) {
            $tableConfigResults = $this->unserializer->unserialize($tableConfig);
            if (is_array($tableConfigResults)) {
                return $tableConfigResults;
            }
        }
        return null;
    }
}
