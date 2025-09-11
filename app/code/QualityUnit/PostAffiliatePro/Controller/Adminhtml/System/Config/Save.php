<?php
namespace QualityUnit\PostAffiliatePro\Controller\Adminhtml\System\Config;

class Save extends \Magento\Config\Controller\Adminhtml\System\Config\Save {
    /**
     * @var \QualityUnit\PostAffiliatePro\Helper\Data
     */
    protected $_papConfig;
    protected $hash;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker
     * @param \Magento\Config\Model\Config\Factory $configFactory
     * @param \Magento\Framework\Cache\FrontendInterface $cache
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \QualityUnit\PostAffiliatePro\Helper\Data $papConfig
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker,
        \Magento\Config\Model\Config\Factory $configFactory,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Magento\Framework\Stdlib\StringUtils $string,
        \QualityUnit\PostAffiliatePro\Helper\Data $papConfig
    ) {
        parent::__construct($context, $configStructure, $sectionChecker, $configFactory, $cache, $string);
        $this->_papConfig = $papConfig;
    }

    /**
     * Save configuration
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute() {
        try {
            // check API connection (validate entered data)
            $groups = $this->getRequest()->getParam('groups');
            if (isset($groups['api']['fields']['url']['value'])) {
                $groups = $groups['api']['fields'];
                $this->validateAPIConnection($groups['url']['value'],$groups['username']['value'],$groups['password']['value']);
                $this->messageManager->addSuccess('API connection with Post Affiliate Pro tested successfully! Configuration saved.');
            }
            else { // original message
                $this->messageManager->addSuccess(__('You saved the configuration.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $messages = explode("\n", $e->getMessage());
            foreach ($messages as $message) {
                $this->messageManager->addError($message);
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong:') . ' ' . $e->getMessage()
            );
        }

        try {
            // custom save logic
            $this->_saveSection();
            $section = $this->getRequest()->getParam('section');
            $website = $this->getRequest()->getParam('website');
            $store = $this->getRequest()->getParam('store');

            // check hash and replace request data
            $groups = $this->_getGroupsForSave();
            if (!empty($this->hash) && !empty($groups)) {
                $groups['tracking']['fields']['hash']['value'] = $this->hash;
            }

            $configData = [
                'section' => $section,
                'website' => $website,
                'store' => $store,
                'groups' => $groups,
            ];
            /** @var \Magento\Config\Model\Config $configModel  */
            $configModel = $this->_configFactory->create(['data' => $configData]);
            $configModel->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $messages = explode("\n", $e->getMessage());
            foreach ($messages as $message) {
                $this->messageManager->addError($message);
            }
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong:') . ' ' . $e->getMessage()
            );
        }

        $this->_saveState($this->getRequest()->getPost('config_state'));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath(
            'adminhtml/system_config/edit',
            [
                '_current' => ['section', 'website', 'store'],
                '_nosid' => true
            ]
        );
    }

    public function validateAPIConnection($server, $username, $password) {
        // validation here... try to connect to PAP and throw an error if problem occurrs
        $url = 'http://'.\QualityUnit\PostAffiliatePro\Helper\Data::getDomainOnly($server).'/scripts/server.php';

        $query = 'D='.urlencode('{"C":"Gpf_Api_AuthService","M":"authenticate","fields":[["name","value","values","error"],["username","'.$username.'",null,""],["password","'.$password.'",null,""],["roleType","M",null,""],["isFromApi","Y",null,""],["apiVersion","",null,""]]}');
        try {
            $response = $this->_papConfig->connectExternal($url, $query);
            $response = json_decode($response);
        } catch (\Exception $e) {
            throw new \Exception('Error occurred: '.$e->getMessage());
            return false;
        }

        if (empty($response)) {
            throw new \Exception('Authentication problem, make sure the URL is correct: '.$url);
        }
        if (!isset($response->success) || $response->success != 'Y') {
            if (isset($response->message)) {
                throw new \Exception('Post Affiliate Pro authentication problem: '.$response->message);
            }

            return false;
        }

        $session = '';
        foreach ($response->fields as $field) {
            if ($field[0] == 'S') {
                $session = $field[1];
                break;
            }
        }
        if (empty($session)) {
            throw new \Exception('Session not set/found');
        }

        // hashing
        $query = 'D='.urlencode('{"C":"Gpf_Rpc_Server","M":"run","requests":[{"C":"Pap_Merchants_Tools_IntegrationMethods","M":"getHashScriptNameParams"}],"S":"'.$session.'"}');
        try {
            $response = $this->_papConfig->connectExternal($url, $query);
            $response = json_decode($response);
            $hash = '';
            foreach ($response[0] as $field) {
                if ($field[0] == 'hashTrackingScriptsEnabled' && $field[1] == 'N') {
                    $hash = '';
                    break;
                }
                if ($field[0] == 'hashTrackingScriptsValue') {
                    $hash = $field[1];
                }
            }
            $this->hash = $hash;
        } catch (\Exception $e) {
        }

        return true;
    }
}
