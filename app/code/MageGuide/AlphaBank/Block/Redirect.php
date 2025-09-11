<?php
namespace MageGuide\AlphaBank\Block;
class Redirect extends \Magento\Framework\View\Element\Template
{
	protected $_model;
	protected $formFactory;
	protected $_helper;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Data\FormFactory $formFactory,
		\MageGuide\AlphaBank\Model\Alphabank $model,
		\MageGuide\AlphaBank\Helper\Data $helper
    )
    {
		$this->_model	   = $model;
		$this->_helper 	   = $helper;
		$this->formFactory = $formFactory;
        parent::__construct($context);
    }
	
	protected function _toHtml()
    {
		$form = $this->formFactory->create();
        $form->setAction(
            $this->_helper->getAlphabankGatewayUrl()
        )->setId(
            $this->getFormId()
        )->setName(
            $this->getFormName()
        )->setAttr(
            'data-auto-submit',
            'true'
        )->setMethod(
            $this->getFormMethod()
        )->setUseContainer(
            true
        )->getRedirectOutput(
			$this->_helper->getAlphabankGatewayUrl()
		);
		$form = $this->_model->addRedirectFormPostFields($form);
		
		$html = '<html><body>';
        $html.= __('You will be redirected to Alphabank in a few seconds.');
        $html.= $form->toHtml();
		$html.= '<script type="text/javascript">document.getElementById("alphabank_checkout").submit();</script>';
        $html.= '</body></html>';
		return $html;
    }
	
	protected function getFormId()
	{
		return 'alphabank_checkout';
	}
	
	protected function getFormName()
	{
		return 'alphabank_checkout';
	}
	
	protected function getFormMethod()
    {
        return 'POST';
    }
}