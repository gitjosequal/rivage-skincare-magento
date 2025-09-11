<?php
namespace Rivage\SecurityLogger\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Backend\Model\Auth\Session as AdminSession;

class SecurityLoggerObserver implements ObserverInterface
{
    protected $adminSession;
    // Only log 'q' (search query), everything else whitelisted is ignored for patterns
    protected $paramWhitelist = [
        'cat', 'myskins', // Whitelist filter params, not scanned
        // add any others that are always safe
    ];

    protected $patterns = [
        // SQL Injection
        '/union.*select/i',
        '/select.*from/i',
        '/sleep\([0-9]+\)/i',
        '/benchmark\([0-9]+,.*\)/i',
        '/or\s+1=1/i',
        '/or\s+1=0/i',
        '/or\s+true/i',
        '/or\s+false/i',
        '/and\s+1=1/i',
        '/and\s+1=0/i',
        '/--\s/i', // less aggressive than /--/i
        '/information_schema/i',
        '/into\s+outfile/i',
        '/into\s+dumpfile/i',
        '/load_file\s*\(/i',
        '/xp_cmdshell/i',
        '/sp_executesql/i',
        '/exec\s+xp_/i',
        '/drop\s+table/i',
        '/truncate\s+table/i',
        '/insert\s+into/i',
        '/update\s+.*set/i',
        '/delete\s+from/i',
        '/char\([0-9]+\)/i',
        '/concat\(/i',
        '/group_concat\(/i',
        '/having\s+[0-9]+=+[0-9]+/i',
        '/waitfor\s+delay/i',

        // XSS
        '/<script\b/i',
        '/javascript:/i',
        '/on\w+\s*=/i',
        '/<iframe\b/i',
        '/<img\b.*src=/i',
        '/document\.cookie/i',
        '/document\.location/i',
        '/window\.location/i',
        '/<svg\b/i',
        '/<body\b/i',
        '/<embed\b/i',
        '/<object\b/i',
        '/<link\b/i',
        '/style\s*=\s*["\'].*expression/i',
        '/alert\s*\(/i',
        '/prompt\s*\(/i',
        '/confirm\s*\(/i',
        '/src\s*=\s*["\']?data:/i',

        // RFI/LFI
        '/\.\.\/\.\.\//i',
        '/\.\.\//i',
        '/etc\/passwd/i',
        '/\/proc\/self\/environ/i',
        '/input_file/i',
        '/php:\/\/input/i',
        '/php:\/\/filter/i',
        '/file:\/\/\/?/i',
        '/data:\/\/text\/plain/i',

        // Path Traversal
        '/\.\.\\\\/i',
        '/\.\.\\\/i',

        // PHP Injection
        '/eval\s*\(/i',
        '/system\s*\(/i',
        '/passthru\s*\(/i',
        '/shell_exec\s*\(/i',
        '/popen\s*\(/i',
        '/proc_open\s*\(/i',

        // Miscellaneous
        '/base64_decode\s*\(/i',
        '/base64_encode\s*\(/i',
        '/\$_(GET|POST|REQUEST|COOKIE|SERVER)/i',
        '/wget\s+/i',
        '/curl\s+/i',
        '/nmap\s+/i',
        '/user\s+agent.*sqlmap/i',
        '/\bselect\b.*\buser\b/i',
        '/\bselect\b.*\bversion\b/i',
        '/mysql_/i',
        '/mysqli_/i',
        '/pg_connect/i',
        '/sqlite_/i',
        '/\bcast\b\s*\(/i',
    ];

    public function __construct(
        AdminSession $adminSession = null // Null for frontend where backend session isn't available
    ) {
        $this->adminSession = $adminSession;
    }

    public function execute(Observer $observer)
    {
        static $alreadyLogged = false;
        if ($alreadyLogged) {
            return;
        }
    
        $event = $observer->getEvent();
        $request = $event->getRequest();
        $areaCode = $request->getRouteName();
        $uri = $request->getRequestUri();
        $method = $request->getMethod();
        $paramsArr = $request->getParams(); // Don't merge with $_GET, $_POST
        $params = json_encode($paramsArr);
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'n/a';
        $ts = date('Y-m-d H:i:s');
    
        // --- 1. Admin Activity Logging ---
        if ($this->adminSession && $this->adminSession->isLoggedIn() && strpos($uri, '/admin') !== false) {
            $user = $this->adminSession->getUser();
            $username = $user ? $user->getUsername() : 'unknown';
            $action = $request->getFullActionName();
            $line = "[$ts] ADMIN $username $ip $action $uri $params\n";
            file_put_contents(BP . '/var/log/admin_actions.log', $line, FILE_APPEND);
        }
    
        // --- 2. Suspicious Request Logging ---
        $log = false;
        foreach ($paramsArr as $key => $val) {
            if (in_array($key, $this->paramWhitelist)) {
                continue;
            }
            if (is_string($val)) {
                foreach ($this->patterns as $pattern) {
                    if (preg_match($pattern, $val)) {
                        $log = true;
                        break 2;
                    }
                }
            }
        }
        if ($log) {
            $line = "[$ts] SUSPICIOUS $ip $method $uri $params\n";
            file_put_contents(BP . '/var/log/suspicious_requests.log', $line, FILE_APPEND);
        }
        $alreadyLogged = true;
    }

}
