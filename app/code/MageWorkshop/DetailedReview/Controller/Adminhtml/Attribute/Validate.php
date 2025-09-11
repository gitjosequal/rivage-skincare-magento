<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Controller\Adminhtml\Attribute;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\Http as HttpRequest;
use MageWorkshop\DetailedReview\Model\Attribute as AttributeModel;

class Validate extends \MageWorkshop\DetailedReview\Controller\Adminhtml\AbstractAttribute
{
    // message is taken from the Magento core
    const ATTRIBUTE_EXISTS_EXCEPTION = 'An attribute with the same code (%1) already exists.';

    /**
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $response = [
            'error' => false
        ];

        $frontendLabel = $request->getParam('frontend_label');
        if (!$attributeCode = $request->getParam('attribute_code')) {
            $attributeCode = $this->generateCode($frontendLabel[0]);
        }
        $attributeId = $request->getParam('attribute_id');

        /** @var AttributeModel $attribute */
        $attribute = $this->attributeFactory->create();
        $attribute->loadByCode($this->getEntityTypeId(), $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            $response = [
                'error'   => true,
                'message' => __(self::ATTRIBUTE_EXISTS_EXCEPTION)
            ];
        }

        $responseObject = new \Magento\Framework\DataObject();
        return $this->resultJsonFactory->create()
            ->setJsonData($responseObject->setData($response)->toJson());
    }
}
