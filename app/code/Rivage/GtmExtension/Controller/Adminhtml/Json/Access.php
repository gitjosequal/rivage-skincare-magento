<?php
/**
 * Copyright © Rivage(info@rivage.com)
 * See COPYING.txt for license details.
 */

namespace Rivage\GtmExtension\Controller\Adminhtml\Json;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\ResponseInterface;
use Rivage\GtmExtension\Model\JsonInitiate;

class Access extends Action
{
    /**
     * @var Http
     */
    protected $httpClient;

    /**
     * @var JsonInitiate
     */
    protected $jsonInitiate;

    /**
     * Download constructor.
     * @param Context $context
     * @param Http $httpClient
     * @param JsonInitiate $jsonInitiate
     */
    public function __construct(
        Context $context,
        Http $httpClient,
        JsonInitiate $jsonInitiate
    ) {
        parent::__construct($context);
        $this->httpClient = $httpClient;
        $this->jsonInitiate = $jsonInitiate;
    }

    /**
     * Executes the action to generate and send JSON content
     *
     * @return ResponseInterface|ResultInterface|void
     * @throws FileSystemException
     */
    public function execute()
    {
        $jsonInitiate = $this->jsonInitiate->getGeneratedJsonContent();
        $this->httpClient->getHeaders()->clearHeaders();
        $this->httpClient->setHeader('Content-Type', 'application/json')
            ->setHeader("Content-Disposition", "attachment; filename=ga4_gtm_ga4.json")
            ->setBody($jsonInitiate);
    }
}
