<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Josequal\Override\Magento\Framework\File;

use Magento\Framework\Validation\ValidationException;

/**
 * File upload class
 *
 * ATTENTION! This class must be used like abstract class and must added
 * validation by protected file extension list to extended class
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 *
 * @api
 * @since 100.0.2
 */
class Uploader extends \Magento\Framework\File\Uploader
{
    /**
     * Validate file before save
     *
     * @return void
     * @throws ValidationException
     */
    protected function _validateFile()
    {
        //This is custom to add wepb
        if($this->getFileExtension() == 'webp'){
            return true;
        }
        
        if ($this->_fileExists === false) {
            return;
        }

        //is file extension allowed
        if (!$this->checkAllowedExtension($this->getFileExtension())) {
            throw new ValidationException(__('Disallowed file type.'));
        }
        //run validate callbacks
        foreach ($this->_validateCallbacks as $params) {
            if (is_object($params['object'])
                && method_exists($params['object'], $params['method'])
                && is_callable([$params['object'], $params['method']])
            ) {
                $params['object']->{$params['method']}($this->_file['tmp_name']);
            }
        }
    }

   
}
