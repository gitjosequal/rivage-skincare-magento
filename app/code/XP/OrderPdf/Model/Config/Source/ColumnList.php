<?php
namespace XP\OrderPdf\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Column list options
 */
class ColumnList implements OptionSourceInterface
{
    const COLUMNS = ["thumbnail", "name", "sku", "price", "qty", "tax", "subtotal"];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $columnsArray = [];
        foreach (static::COLUMNS as $key) {
            $columnsArray [] = [
                "value" => $key,
                "label" => $key != "thumbnail" ? ucfirst($key) : "Image"
            ];
        }
        return $columnsArray;
    }
}
