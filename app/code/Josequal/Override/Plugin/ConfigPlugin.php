<?php

namespace Josequal\Override\Plugin;

class ConfigPlugin
{
    /**
     * Override the getValue method for design/head/includes
     *
     * @param \Magento\Framework\App\Config $subject
     * @param string|null $result
     * @param string $path
     * @param null|string $scopeCode
     * @param null|string $scopeType
     * @return string|null
     */
    public function afterGetValue(
        \Magento\Framework\App\Config $subject,
        $result,
        $path,
        $scopeCode = null,
        $scopeType = null
    ) {
        if ($path === 'design/head/includes' || $path === 'design/footer/absolute_footer') {
            return '';
        }

        return $result;
    }
}