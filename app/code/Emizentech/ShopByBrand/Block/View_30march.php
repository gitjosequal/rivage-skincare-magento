<?php
namespace Emizentech\ShopByBrand\Block;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class View extends \Magento\Framework\View\Element\Template
{
	
	/**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;
    
	/**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    
    /**
     * Image helper
     *
     * @var Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
     /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    protected $_brandFactory;


	public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Emizentech\ShopByBrand\Model\BrandFactory $brandFactory,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
		$this->_categoryFactory = $categoryFactory;
        $this->_imageHelper = $context->getImageHelper();
        $this->_brandFactory = $brandFactory;
        $this->_cartHelper = $context->getCartHelper();
        parent::__construct(
            $context,
            $data
        );
	$this->setCollection($this->getProductCollection());
    }
	 public function getAddToCartUrl($product, $additional = [])
    {
			return $this->_cartHelper->getAddUrl($product, $additional);
    }
    
    
    public function _prepareLayout()
    {
        parent::_prepareLayout();
	/** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
           'Magento\Theme\Block\Html\Pager',
           'brand.view.pager'
        );
        $pager
			#->setLimit(12)
            #->setShowAmounts(false)
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
 
        return $this;
    }
	/**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    public function getBrand(){
	    $id = $this->getRequest()->getParam('id');
	    if ($id) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		    $model = $objectManager->create('Emizentech\ShopByBrand\Model\Items');
		    $model->load($id);
			return $model;
		}
		return false;
    }
    
    public function getCat_ids()
    {
		$catIds = [];
		$products = $this->getProductCollection();
		foreach($products as $_product):
			$proCats = $_product->getCategoryIds();
			$catIds= array_merge($catIds, $proCats);
		endforeach;
		$finalCat_ids = array_unique($catIds);
		$final_array = [];
		$final_par_array = [];
		foreach($finalCat_ids as $key=>$value){
			$category = $this->getParentCategory($value);
			if($category->getId() != 2){
				$final_par_array[$category->getId()] = $category->getName();
			}
		}
		return $final_par_array;
	}
	
	public function getParentCategory($categoryId = false)
    {
        return $category = $this->_categoryFactory->create()->load($categoryId)->getParentCategory();
    }
	
    public function getProductCollection()
    {
		$brand = $this->getBrand();
		$collection = $this->_productCollectionFactory->create();
    	$collection->addAttributeToSelect('*');
     	/* var_dump(get_class_methods($collection));
     	die;*/
		$collection->addAttributeToSelect('name');
    	$collection->addAttributeToFilter('ingredients' , array('finset' => $brand->getAttributeId()));
    	$collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
    	$collection->addAttributeToFilter('visibility', array('neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE));
        return $collection;
    }
    
    public function imageHelperObj(){
        return $this->_imageHelper;
    }
    
    public function getProductPricetoHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null
	) {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product
            );
        }
        return $price;
    }
}
