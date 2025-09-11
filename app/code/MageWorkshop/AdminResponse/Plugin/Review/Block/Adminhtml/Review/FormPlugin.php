<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\AdminResponse\Plugin\Review\Block\Adminhtml\Review;

use Magento\Backend\Block\Widget\Form\Generic;
use MageWorkshop\AdminResponse\Model\AdminResponse;

/**
 * Class FormPlugin
 * This class injects additional fields into the review form
 * Be patient while editing this class because there are 4 different cases with different logic:
 * 1) enter Edit Review pare - review data is available and is initialized inside Magento
 * 2) enter New Review - form is rendered prior to selecting the product. That is why there is no info about the product
 *    and target stores at that moment. So, we just need to insert container and wait till all data becomes available
 * 3) Edit Review, Visibility (stores) are changed - AJAX call to our own controller is processed. We know the review id
 *    and product id is taken from it, so need to get fields list for all stores and process them one by one
 * 4) New Review, Visibility (stores) are changed - the same as above, but we do not have the review data, so product id
 *    is passed as a request argument - it is present in the hidden input on the page, but only in this case
 */
class FormPlugin
{
    const SELECT_STORES = 'select_stores';

    const PRODUCT_ID = 'product_id';

    /**
     * @var \Magento\Framework\App\Request\Http $request
     */
    private $request;

    /**
     * @var \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     */
    private $reviewHelper;

    /**
     * @var \Magento\Framework\View\LayoutInterface $layout
     */
    private $layout;

    /**
     * @var \Magento\Framework\Registry $registry
     */
    private $registry;

    /**
     * FormPlugin constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \MageWorkshop\DetailedReview\Helper\Review $reviewHelper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \MageWorkshop\DetailedReview\Helper\Review $reviewHelper,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Registry $registry
    ) {
        $this->request = $request;
        $this->reviewHelper = $reviewHelper;
        $this->layout = $layout;
        $this->registry = $registry;
    }

    /**
     * See the same plugin inside the DetailedReview for more information
     *
     * @param Generic $subject
     * @throws \Exception
     */
    public function afterSetForm(Generic $subject/*, $result */)
    {
        if ($this->request->isAjax()) {
            return;
        }

        $form = $subject->getForm();

        // Depends on the mode - creating new review or creating an existing one. See form id in:
        // \Magento\Review\Block\Adminhtml\Add\Form at line #62
        // \Magento\Review\Block\Adminhtml\Edit\Form at line #65
        $targetFieldSetName = ($review = $this->getReview())
            ? 'review_details'
            : 'add_review_form';

        // Note! There is no review data if new review is being created and the product has not yet been selected
        if ($review !== null && !$this->reviewHelper->isProductReview($review)) {
            return;
        }

        $targetFieldSet = false;
        /** @var \Magento\Framework\Data\Form\Element\Fieldset $element */
        foreach ($form->getElements() as $element) {
            if ($element->getId() === $targetFieldSetName) {
                $targetFieldSet = $element;
                break;
            }
        }

        if ($targetFieldSet) {
            $targetFieldSet->addField(
                AdminResponse::FIELD_NAME,
                'textarea',
                [
                    'name'     => AdminResponse::FIELD_NAME,
                    'title'    => __('Admin Response'),
                    'label'    => __('Admin Response'),
                    // 'required' => $isRequired,
                    'style' => 'min-width:200px; height:16em;',
                    // 'class' => $validation,
                ]
            );
        }
    }

    /**
     * Note! There may be no review data if new review is being created and the product has not yet been selected
     *
     * @return \Magento\Review\Model\Review|null
     */
    private function getReview()
    {
        return $this->registry->registry('review_data');
    }
}
