<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Josequal\Override\Magento\MediaStorage\Model\File;

use Magento\Framework\Validation\ValidationException;
use Magento\Framework\App\ObjectManager;
use Magento\MediaStorage\Model\File\Validator\Image;

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
class Uploader extends \Magento\MediaStorage\Model\File\Uploader
{
    /**
     * @var Image
     */
    private $imageValidator;
    
    /**
     * Save file to storage
     *
     * @param  array $result
     * @return $this
     */
    protected function _afterSave($result)
    {
        if (empty($result['path']) || empty($result['file'])) {
            return $this;
        }

        if ($this->_coreFileStorage->isInternalStorage() || $this->skipDbProcessing()) {
            return $this;
        }

        $this->_result['file'] = $this->_coreFileStorageDb->saveUploadedFile($result);

        return $this;
    }

    /**
     * Getter/Setter for _skipDbProcessing flag
     *
     * @param null|bool $flag
     * @return bool|\Magento\MediaStorage\Model\File\Uploader
     */
    public function skipDbProcessing($flag = null)
    {
        if ($flag === null) {
            return $this->_skipDbProcessing;
        }
        $this->_skipDbProcessing = (bool)$flag;
        return $this;
    }

    /**
     * Check protected/allowed extension
     *
     * @param string $extension
     * @return boolean
     */
    public function checkAllowedExtension($extension)
    {
        //validate with protected file types
        if (!$this->_validator->isValid($extension)) {
            return false;
        }

        return parent::checkAllowedExtension($extension);
    }

    /**
     * Get file size
     *
     * @return int
     */
    public function getFileSize()
    {
        return $this->_file['size'];
    }

    /**
     * Validate file
     *
     * @return array
     */
    public function validateFile()
    {
        $this->_validateFile();
        return $this->_file;
    }
    
    /**
     * @inheritDoc
     * @since 100.4.0
     */
    protected function _validateFile()
    {
        //This is custom to add wepb
        if($this->getFileExtension() == 'webp'){
            return true;
        }
        
        parent::_validateFile();
       
        $ext = pathinfo($this->_file['tmp_name'], PATHINFO_EXTENSION);
        if($ext == 'svg' || $ext == 'webp'){
            return true;
        }

        if (!$this->getImageValidator()->isValid($this->_file['tmp_name'])) {
            throw new ValidationException(__('File validation failed xxx.'));
        }
    }

    /**
     * Return image validator class.
     *
     * Child classes __construct() don't call parent, so we have to retrieve class instance with private function.
     *
     * @return Image
     */
    private function getImageValidator(): Image
    {
        if (!$this->imageValidator) {
            $this->imageValidator = ObjectManager::getInstance()->get(Image::class);
        }

        return $this->imageValidator;
    }
   
}