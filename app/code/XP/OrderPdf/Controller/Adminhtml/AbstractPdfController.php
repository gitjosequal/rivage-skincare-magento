<?php
namespace XP\OrderPdf\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Store\Model\ScopeInterface;
use XP\OrderPdf\Model\Pdf\Order;
use XP\OrderPdf\Model\PdfExporter;

abstract class AbstractPdfController extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'XP_OrderPdf::print_order';

    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var ResultFactory
     */
    protected ResultFactory $resultRedirect;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @var PdfExporter
     */
    protected PdfExporter $pdfExporter;

    /**
     * @var bool
     */
    protected bool $rtl = false;

    /**
     * @var string
     */
    protected $redirectUrl = 'sales/order/index';

    /**
     * PrintAction constructor.
     * @param Action\Context $context
     * @param FileFactory $fileFactory
     * @param ForwardFactory $resultForwardFactory
     * @param ResultFactory $result
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Action\Context $context,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        ResultFactory $result,
        ScopeConfigInterface $scopeConfig,
        PdfExporter $pdfExporter
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultRedirect = $result;
        $this->scopeConfig = $scopeConfig;
        $this->pdfExporter = $pdfExporter;
    }

    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $locale_param = $this->getRequest()->getParam('locale_param');
        if ($locale_param && ($locale_param == 'ar' || $locale_param == 'ar_SA')) {
            $this->rtl = true;
        }
        if ($request->getModuleName() == 'xporderpdf' && $request->getActionName() =='printaction') {
            if ($locale_param && $locale_param == 'ar' &&  $this->_localeResolver->getLocale() != 'ar_SA') {
                $this->rtl = true;
                $locale = 'ar_SA';
                // Forcefully set locale.
                $this->getRequest()->setParam('locale', $locale);
                $this->_processLocaleSettings();

                $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('xporderpdf/order/printaction/locale_param/ar', $this->getRequest()->getParams());

                return $resultRedirect;
            }

            // Forcefully set back to en.
            $this->getRequest()->setParam('locale', 'en_US');
            $this->_processLocaleSettings();
        }

        return parent::dispatch($request);
    }

    protected function _redirectToLocaleSettings($orderModel, $path = 'xporderpdf/order/printaction/locale_param/ar')
    {
        $this->_localeResolver->emulate($orderModel->getStoreId());
        if ($this->_localeResolver->getLocale() == "ar_SA"
            && !$this->getRequest()->getParam('locale_param', false)
        ) {
            $this->getRequest()->setParam('locale', $this->_localeResolver->getLocale());
            $this->getRequest()->setParam('locale_param', $this->_localeResolver->getLocale());
            $this->_processLocaleSettings();
            $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($path, $this->getRequest()->getParams());

            return $resultRedirect;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    /**
     * @param string|null $path
     * @return string|null
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }
}
