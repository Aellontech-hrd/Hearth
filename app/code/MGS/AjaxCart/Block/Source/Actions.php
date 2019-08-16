<?php
namespace MGS\AjaxCart\Block\Source;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Actions
 * @package MGS\AjaxCart\Block\Source
 */
class Actions extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->formKey = $formKey;
        $this->request = $request;
    }

    /**
     * Check if can redirect to cart
     *
     * @return bool
     */
    public function isRedirectToCartEnabled()
    {
        return $this->_scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return \Zend_Json::encode($this->formKey->getFormKey());
    }

    /**
    * @var check Module
    *
    * @return bool true|false
    */
    public function canShowCatalog(){
        $moduleName = $this->request->getModuleName();
        if($moduleName == 'catalogsearch' || $moduleName == 'catalog'){
            return false;
        }
        return true;
    }
}
