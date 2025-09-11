<?php

namespace Rivaz\Expertadvice\Block;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Framework\View\Element\Template;

class Form extends Template
{
	 protected $_countryCollectionFactory;
    
	public function __construct(
        CountryCollectionFactory $countryCollectionFactory,
        Template\Context $context,
        array $data = []
    )
    {
        $this->_countryCollectionFactory = $countryCollectionFactory;
        parent::__construct($context, $data);
    }
    
    public function getCountryCollection()
    {
        $collection = $this->_countryCollectionFactory->create();
        $collection->addFieldToSelect('*');
        return $collection;
    }
		
	/* public function _prepareLayout()
	{
	    return parent::_prepareLayout();
	} */
	public function getFormAction()
    {
        return $this->getUrl('expertadvice/index/post', ['_secure' => true]);
    }
		
	
	}

