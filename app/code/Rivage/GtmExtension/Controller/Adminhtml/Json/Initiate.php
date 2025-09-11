<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Controller\Adminhtml\Json;

use Magento\Backend\App\Action;
use Rivage\GtmExtension\Model\JsonInitiate;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Initiate extends Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Rivage\GtmExtension\Model\JsonInitiate
     */
    protected $jsonInitiate;

    /**
     * Constructor for initializing the controller.
     *
     * @param JsonInitiate $jsonInitiate
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        JsonInitiate $jsonInitiate,
        Context $context,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonInitiate = $jsonInitiate;
        parent::__construct($context);
    }

    /**
     * Executes the JSON generation process.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $ga4Url = null;
        $errors = $this->validateInputs($params);
        $jsonUrl = '';

        // @codingStandardsIgnoreStart
        if (empty($errors)) {
            $formData = [];
            parse_str($params['form_data'], $formData);

            try {
                $accountId = trim($params['account_id'] ?? '');
                $containerId = trim($params['container_id'] ?? '');
                $measurementId = trim($params['measurement_id'] ?? '');
                $publicId = trim($params['public_id'] ?? '');

                $jsonUrl = $this->jsonInitiate->generateGA4Json($accountId, $containerId, $measurementId, $publicId);
                $errors[] = __('Your JSON file is ready! Just click the Download JSON button to get it.');
            } catch (\Exception $ex) {
                $errors[] = $ex->getMessage();
            }
        }
        // @codingStandardsIgnoreEnd

        return $this->resultJsonFactory->create()->setData([
            'msg' => $errors,
            'jsonUrl' => $jsonUrl,
        ]);
    }

    /**
     * Validates the provided parameters.
     *
     * @param array $params The parameters to validate.
     * @return array An array of error messages for missing or empty parameters.
     */
    protected function validateInputs($params)
    {
        $requiredFields = ['account_id', 'container_id', 'measurement_id', 'public_id'];
        $errors = [];

        // @codingStandardsIgnoreStart
        foreach ($requiredFields as $field) {
            if (empty(trim($params[$field] ?? ''))) {
                $errors[] = __('Please ensure that the ' . str_replace('_', ' ', ucwords($field, '_')) . ' is specified.');
            }
        }
        // @codingStandardsIgnoreEnd

        return $errors;
    }
}
