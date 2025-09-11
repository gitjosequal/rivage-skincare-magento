<?php
namespace MageGuide\AlphaBank\Model;
use Magento\Framework\App\RequestInterface;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Payment\Model\MethodInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
class AlphabankConfigProvider implements ConfigProviderInterface
{
    protected $assetRepository;
    protected $paymentHelper;
    protected $paymentMethod;
	protected $_helper;
	protected $request;
    public function __construct(
        PaymentHelper $paymentHelper,
        Repository $assetRepository,
		\MageGuide\AlphaBank\Helper\Data $helper,
		RequestInterface $request
    ) {
        $this->assetRepository = $assetRepository;
        $this->paymentHelper   = $paymentHelper;
		$this->_helper 		   = $helper;
		$this->request 		   = $request;
    }
	
	public function getConfig()
    {
		 $config = array(
            'alphabank' => array(
                'payment' => array(
					'getStartUrl'						=> $this->getStartUrl(),
					'getAlphabankInstallmentOptions'    => $this->getAlphabankInstallmentOptions(),
					'getAlphabankNumberofInstallments'	=> $this->getAlphabankNumberofInstallments(),
                ),
            ),
        );
		return $config;
    }
	
	public function getStartUrl()
	{
		return $this->_helper->getStartUrl();
	}
	
	public function getAlphabankInstallmentOptions()
	{ 
		$installment = $this->_helper->getInstallmentOptions();	
		if(count($installment)>0)
			return true;
		return false;	
	}
	
	public function getAlphabankNumberofInstallments()
	{
		return $this->_helper->getNumberofInstallments();	
	}
}