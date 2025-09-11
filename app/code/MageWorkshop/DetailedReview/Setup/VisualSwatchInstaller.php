<?php
/**
 * Copyright (c) MageWorkshop. All rights reserved.
 * This source file is subject to the MageWorkshop License https://mageworkshop.com/terms-of-service
 * Do not change this file if you want to upgrade the module to the newer versions in the future
 * Please, contact us at https://mageworkshop.com/contact if you wish to customize this module according to you business needs
 */
namespace MageWorkshop\DetailedReview\Setup;

use Magento\Framework\Module\Dir;
use Magento\Framework\App\Filesystem\DirectoryList;

class VisualSwatchInstaller
{
    // Files should be placed in: <Your_Module>/etc/visual_swatches/<attribute_code>/
    const SOURCE_DIR = 'visual_swatches';

    /** @var \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory */
    protected $uploaderFactory;

    /** @var \Magento\Framework\Module\Dir\Reader $directoryReader */
    protected $directoryReader;

    /** @var \MageWorkshop\Core\Model\Module\AbstractDetailsData $moduleDetailsData */
    protected $moduleDetailsData;

    /** @var \Magento\Framework\Image\AdapterFactory $adapterFactory */
    protected $adapterFactory;

    /** @var \Magento\Framework\Filesystem $filesystem */
    protected $filesystem;

    /** @var \Magento\Framework\Event\ManagerInterface $eventManager */
    protected $eventManager;

    /** @var \Magento\Catalog\Model\Product\Media\Config $mediaConfig */
    protected $mediaConfig;

    /** @var \Magento\Swatches\Helper\Media $swatchHelper */
    protected $swatchHelper;

    /** @var array $allowedExtensions */
    protected $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];

    /**
     * VisualSwatchInstaller is responsible for emulating the swatch file upload during the module installation process
     * $moduleDetailsData and $swatchHelper (optionally) should be passed via the di.xml because they are
     * module-dependent $swatchHelper should be passed if you need to install visual swatch attributes for other EAV
     * entities then "catalog_product"
     *
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Framework\Image\AdapterFactory $adapterFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Framework\Module\Dir\Reader $directoryReader
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \MageWorkshop\Core\Model\Module\AbstractDetailsData $moduleDetailsData
     * @param \Magento\Swatches\Helper\Media $swatchHelper
     */
    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Module\Dir\Reader $directoryReader,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \MageWorkshop\Core\Model\Module\AbstractDetailsData $moduleDetailsData,
        \Magento\Swatches\Helper\Media $swatchHelper
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->mediaConfig = $mediaConfig;
        $this->directoryReader = $directoryReader;
        $this->eventManager = $eventManager;
        $this->moduleDetailsData = $moduleDetailsData;
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * @param string $attributeCode
     * @param string $fileName
     * @return string
     * @throws \Exception
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function installSwatchImage($attributeCode = 'body_type', $fileName = 'av.neoverve.net_error.png')
    {
        $sourceDir = $this->directoryReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            $this->moduleDetailsData->getModuleCode()
        );
        $originalFile = implode(DIRECTORY_SEPARATOR, [$sourceDir, self::SOURCE_DIR, $attributeCode, $fileName]);

        if (!is_file($originalFile) || !is_readable($originalFile)) {
            throw new \Exception("Swatch file '$originalFile' does not exist!");
        }

        // Emulating file upload. Create temp file as we do not want the original file to be deleted
        $tempFile = tmpfile();
        fwrite($tempFile, file_get_contents($originalFile));
        $metaData = stream_get_meta_data($tempFile);
        $tmpFileName = $metaData['uri'];

        // This is a dirty-dirty lifehack for the following error:
        // Direct use of $_FILES Superglobal detected
        // Don't be so strict to us - we just want to utilize the default functionality to upload swatches,
        // but can not do this smoothly without settings data in the $_FILES superglobal
        $_files = &${'_FILES'};
        $_files = [
            'datafile' => [
                'name'     =>  $fileName,
                'tmp_name' =>  $tmpFileName,
                'size'     =>  filesize($originalFile),
                'error'    =>  0
            ]
        ];

        // "Upload" the file in the Magento-like way so all actions are processed in the native way
        // See Magento\Swatches\Controller\Adminhtml\Iframe\Show::execute() for the similar functionality
        $uploader = $this->uploaderFactory->create(['fileId' => 'datafile']);
        $uploader->setAllowedExtensions($this->allowedExtensions);
        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
        $imageAdapter = $this->adapterFactory->create();
        $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $result = $uploader->save($mediaDirectory->getAbsolutePath($this->mediaConfig->getBaseTmpMediaPath()));

        $this->eventManager->dispatch(
            'swatch_gallery_upload_image_after',
            ['result' => $result, 'action' => $this]
        );

        unset($result['tmp_name']);
        unset($result['path']);

        $result['url'] = $this->mediaConfig->getTmpMediaUrl($result['file']);
        $result['file'] = $result['file'] . '.tmp';

        $newFile = $this->swatchHelper->moveImageFromTmp($result['file']);
        unset($_files['datafile']);
        // $this->swatchHelper->generateSwatchVariations($newFile);
        return $newFile;
    }
}
