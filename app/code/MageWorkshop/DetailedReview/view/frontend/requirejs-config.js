/*
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */

var config = {
    paths: {
        mageWorkshop_detailedReview_reviewFilters: 'MageWorkshop_DetailedReview/js/review-filters',
        mageWorkshop_detailedReview_reviewRating: 'MageWorkshop_DetailedReview/js/review-rating',
        mageWorkshop_detailedReview_listReviews: 'MageWorkshop_DetailedReview/js/list-reviews',
        mageWorkshop_detailedReview_reviewForm: 'MageWorkshop_DetailedReview/js/review-form',
        mageWorkshop_detailedReview_reviewCustomValidateLength: 'MageWorkshop_DetailedReview/js/review-custom-validate-length'
    },
    config: {
        mixins: {
            'Magento_Review/js/process-reviews': {
                'MageWorkshop_DetailedReview/js/process-reviews-mixin': true
            }
        }
    }
};
