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
use Magento\Analytics\ReportXml\ConnectionFactory;
use Magento\Analytics\ReportXml\IteratorFactory;
use Magento\Analytics\ReportXml\Query;
use Magento\Analytics\ReportXml\QueryFactory;

class OrderReport
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * @var IteratorFactory
     */
    private $iteratorFactory;

    private $data;

    /**
     * ReportProvider constructor.
     *
     * @param QueryFactory $queryFactory
     * @param ConnectionFactory $connectionFactory
     * @param IteratorFactory $iteratorFactory
     */
    public function __construct(
        Data $data,
        QueryFactory $queryFactory,
        ConnectionFactory $connectionFactory,
        IteratorFactory $iteratorFactory
    ) {
        $this->data = $data;
        $this->queryFactory = $queryFactory;
        $this->connectionFactory = $connectionFactory;
        $this->iteratorFactory = $iteratorFactory;
    }

    /**
     * Returns custom iterator name for report
     * Null for default
     *
     * @param Query $query
     * @return string|null
     */
    private function getIteratorName(Query $query)
    {
        $config = $query->getConfig();
        return $config['iterator'] ?? null;
    }

    /**
     * Returns report data by name and criteria
     *
     * @param string $name
     * @return \IteratorIterator
     */
    public function aroundGetReport(
        \Magento\Analytics\ReportXml\ReportProvider $subject,
        callable $proceed,
        $name
    ) {
        $query = $this->queryFactory->create($name);
        $connection = $this->connectionFactory->getConnection($query->getConnectionName());

        if ($name === 'orders') {
            $statement = $connection->query($this->generateOrdersSql());
        } else {
            $statement = $connection->query($query->getSelect());
        }
        return $this->iteratorFactory->create($statement, $this->getIteratorName($query));
    }

    private function safeSql(array $params)
    {
        foreach ($params as $outerKey => $outerVal) {
            foreach ($outerVal as $key => $val) {
                $saveVal = preg_replace('/[^a-zA-Z0-9_ .-]/s', '', $val);
                $params[$outerKey][$key] = $saveVal;
            }
        }

        return $params;
    }

    private function generateOrdersSql()
    {
        $reportingCurrency = $this->data->getReportingCurency();
        $exchangeRates = $this->safeSql($this->data->getExchangeRates());

        $sql = "SELECT  entity_id,
                        created_at,
                        customer_id,
                        status,
                ";

        $sql .= $this->generateCaseBlock($exchangeRates, "base_grand_total");
        $sql .= $this->generateCaseBlock($exchangeRates, "base_tax_amount");
        $sql .= $this->generateCaseBlock($exchangeRates, "base_shipping_amount");

        $sql .= "SHA1(coupon_code) AS coupon_code,
                store_id,
                store_name,";

        $sql .= $this->generateCaseBlock($exchangeRates, "base_discount_amount");
        $sql .= $this->generateCaseBlock($exchangeRates, "base_subtotal");
        $sql .= $this->generateCaseBlock($exchangeRates, "base_total_refunded");

        $sql .= "shipping_method,
                shipping_address_id,
                SHA1(customer_email) AS customer_email,";

        $sql .= $this->generateCaseBlock($exchangeRates, "base_total_online_refunded");

        $sql .= $this->generateCaseBlock($exchangeRates, "base_total_offline_refunded");

        $sql .= "'" . $reportingCurrency . "' as base_currency_code,
                billing_address_id
                FROM sales_order";
        return $sql;
    }

    private function generateCaseBlock(array $exchangeRates, string $field)
    {
        $sql = "CASE
        ";

        foreach ($exchangeRates as $exchangeRate) {
            $sql .= "WHEN base_currency_code = '" . $exchangeRate['currency'] . "' THEN " . $field . " * " . $exchangeRate['rate'] . "
            ";
        }

        $sql .= "ELSE " . $field . "
        ";

        $sql .= "END as " . $field . ",
        ";
        return $sql;
    }
}
