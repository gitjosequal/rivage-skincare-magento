<?php
namespace Rivage\SecurityLogger\Block\Adminhtml\Log;

use Magento\Backend\Block\Template;

class View extends Template
{
    public function getLogContents($file)
    {
        $logPath = BP . '/var/log/' . $file;
        if (file_exists($logPath)) {
            // Read last 200 lines for performance
            $lines = array_slice(file($logPath), -200);
            return implode('', $lines);
        }
        return "Log file not found: " . $file;
    }
}
