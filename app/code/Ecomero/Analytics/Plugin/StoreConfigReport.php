<?php
/**
 *           a88888P8
 *          d8'
 * .d8888b. 88        .d8888b. 88d8b.d8b. .d8888b. .dd888b. .d8888b.
 * 88ooood8 88        88'  `88 88'`88'`88 88ooood8 88'    ` 88'  `88
 * 88.  ... Y8.       88.  .88 88  88  88 88.  ... 88       88.  .88
 * `8888P'   Y88888P8 `88888P' dP  dP  dP `8888P'  dP       `88888P'
 *
 *           Copyright © eComero Management AB, All rights reserved.
 */
declare(strict_types=1);

namespace Ecomero\Analytics\Plugin;

use Ecomero\Analytics\Helper\Data;

class StoreConfigReport
{
    private $data;

    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    public function afterGetReport($subject, $result)
    {
        $configReport = [];
        $reportingCurrency = $this->data->getReportingCurency();

        foreach ($result as $value) {
            if ($value['config_path'] === "currency/options/base") {
                $value['value'] = $reportingCurrency;
            }
            $configReport [] = $value;
        }

        return new \IteratorIterator(new \ArrayIterator($configReport));
    }
}
