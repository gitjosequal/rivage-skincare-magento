<?php
/**
 * Profile Avatar
 * 
 * @author Slava Yurthev
 */
namespace SY\Avatar\Controller\Manager;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \SY\Avatar\Block\Customer\Account\Avatar;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Filesystem;
class Upload extends \Magento\Framework\App\Action\Action {
	protected $_resultPageFactory;
	protected $allowedExtensions = ['png','jpeg','jpg','gif','svg'];
	protected $fileId = 'avatar';
	public function __construct(Context $context, PageFactory $resultPageFactory){
		$this->_resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}
	public function execute(){
		$files = $this->getRequest()->getFiles();
		$resultPage = $this->_resultPageFactory->create();
		$object_manager = $this->_objectManager;
		$block = $resultPage->getLayout()->createBlock('SY\Avatar\Block\Customer\Account\Avatar');
		$customer = $block->getCustomer();
		if($customerId = $customer->getId()){
			$fileSystem = $object_manager->create('\Magento\Framework\Filesystem');
			$mediaDir = $this->_objectManager->get('\Magento\Framework\Filesystem')
						->getDirectoryRead(DirectoryList::MEDIA);
			$save_image_path = $mediaDir->getAbsolutePath('avatar'); 
			if($customer->getData('avatar')){
				@unlink($save_image_path.'/'.$customer->getData('avatar'));
				@rmdir($save_image_path.'/'.$customer->getId());
			}
			$resource = $object_manager->create('Magento\Framework\App\ResourceConnection');
			$table = $resource->getTableName('customer_entity');
			$write = $resource->getConnection($resource::DEFAULT_CONNECTION);
			try {
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'avatar']
				);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				$uploader->setAllowCreateFolders(true);
				if ($uploader->save($save_image_path.'/'.$customerId)) {
					$uploadedFileNameAndPath = $customerId.'/'.$uploader->getUploadedFileName();
					$write->query("UPDATE `{$table}` SET `avatar`='{$uploadedFileNameAndPath}' WHERE `entity_id`='{$customerId}'");
				}
			} catch (\Exception $e) {}
		}
		$this->_redirect('customer/account');
	}
}