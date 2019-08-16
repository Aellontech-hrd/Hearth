<?php
/**
 * Mageinn_Hint extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Mageinn
 * @package     Mageinn_Hint
 * @copyright   Copyright (c) 2017 Mageinn. (http://mageinn.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mageinn\Hint\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Data
 * @package Mageinn\Hint\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ENABLED = 'mageinn_hint_configuration/general/enable';
    const TEMPLATE_VIEW = 'mageinn_hint_configuration/general/view_template';
    const TEMPLATE_FILTER = 'mageinn_hint_configuration/general/filter_template';
    const TEMPLATE_ATTRIBUTES = 'mageinn_hint_configuration/general/product_attributes_template';
    const TEMPLATE_COMPARE = 'mageinn_hint_configuration/general/compare_template';
    const IS_USE_DEFAULT_TEMPLATE = 'mageinn_hint_configuration/general/use_default_template';

    /** Path to template file with Swatch renderer. */
    const MAGEINN_SWATCH_RENDERER_TEMPLATE = "Mageinn_Hint::product/view/renderer.phtml";

    /** Path to default template file with standard Configurable renderer. */
    const MAGEINN_CONFIGURABLE_RENDERER_TEMPLATE = "Mageinn_Hint::product/view/type/options/configurable.phtml";

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Magento\Framework\View\Element\BlockFactory */
    private $blockFactory;

    /** @var \Mageinn\Hint\Model\Attribute */
    private $titleHint;

    /** @var \Mageinn\Hint\Model\Option */
    private $optionHint;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Mageinn\Hint\Model\Attribute $titleHint
     * @param \Mageinn\Hint\Model\Option $optionHint
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Mageinn\Hint\Model\Attribute $titleHint,
        \Mageinn\Hint\Model\Option $optionHint
    ) {
        $this->storeManager = $storeManager;
        $this->blockFactory = $blockFactory;
        $this->titleHint = $titleHint;
        $this->optionHint = $optionHint;
        parent::__construct($context);
    }

    /**
     * Get is module enabled
     *
     * @return bool
     */
    public function getActiveFlag()
    {
        return $this->isSetFlag(self::ENABLED);
    }

    /**
     * @return string
     */
    public function getTemplateView()
    {
        return $this->getValue(self::TEMPLATE_VIEW);
    }

    /**
     * @return string
     */
    public function getTemplateFilter()
    {
        return $this->getValue(self::TEMPLATE_FILTER);
    }

    /**
     * @return string
     */
    public function getTemplateAttributes()
    {
        return $this->getValue(self::TEMPLATE_ATTRIBUTES);
    }

    /**
     * @return string
     */
    public function getTemplateCompare()
    {
        return $this->getValue(self::TEMPLATE_COMPARE);
    }

    /**
     * @return bool
     */
    public function isUseDefaultTemplate()
    {
        return $this->isSetFlag(self::IS_USE_DEFAULT_TEMPLATE);
    }

    /**
     * @return bool
     */
    public function isNotUseDefaultTemplate()
    {
        return ! $this->isUseDefaultTemplate();
    }

    /**
     * Get Swatch renderer Template
     *
     * @return string
     */
    public function getSwatchRendererTemplate()
    {
        return self::MAGEINN_SWATCH_RENDERER_TEMPLATE;
    }

    /**
     * Get Configurable renderer Template
     *
     * @return string
     */
    public function getConfigurableRendererTemplate()
    {
        return self::MAGEINN_CONFIGURABLE_RENDERER_TEMPLATE;
    }

    /**
     * Get actual template. Used in plugins
     *
     * @param string $actual
     * @param string $default
     * @return string
     */
    public function getTemplateName($actual, $default)
    {
        return $this->getActiveFlag() && $this->isNotUseDefaultTemplate() ? $actual : $default;
    }

    /**
     * Retrieve title hint for current attribute
     *
     * @param int|false $attributeId
     * @param int|false $store
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTitleHint($attributeId = false, $store = false)
    {
        if (! $store) {
            $store = $this->storeManager->getStore()->getId();
        }

        if (! $attributeId) {
            return '';
        }

        return $this->titleHint
            ->getCollection()
            ->addFieldToFilter('attribute_id', $attributeId)
            ->addFieldToFilter('store_id', $store)
            ->setPageSize(1)
            ->getFirstItem()
            ->getValue();
    }

    /**
     * Retrieve title hint block html
     *
     * @param int $attributeId
     * @param int|false $store
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTitleHintHtml($attributeId, $store = false)
    {
        return $this->renderTooltip($this->getTitleHint($attributeId, $store));
    }

    /**
     * Retrieve hint for current option
     *
     * @param int $optionId
     * @param int|false $attributeId
     * @param int|false $store
     * @return string
     * @throws NoSuchEntityException
     */
    public function getOptionHint($optionId, $attributeId = false, $store = false)
    {
        if (! $store) {
            $store = $this->storeManager->getStore()->getId();
        }

        if (! $attributeId) {
            return '';
        }

        return $this->optionHint
            ->getCollection()
            ->addFieldToFilter('attr_id', $attributeId)
            ->addFieldToFilter('store_id', $store)
            ->addFieldToFilter('option_id', $optionId)
            ->setPageSize(1)
            ->getFirstItem()
            ->getValue();
    }

    /**
     * Retrieve current option hint block html
     *
     * @param int $optionId
     * @param int $attributeId
     * @param int|false $store
     * @return string
     * @throws NoSuchEntityException
     */
    public function getOptionHintHtml($optionId, $attributeId, $store = false)
    {
        return $this->renderTooltip($this->getOptionHint($optionId, $attributeId, $store));
    }

    /**
     * @param string $html
     * @return string
     */
    public function getCustomOptionHtml($html)
    {
        return $this->renderTooltip($html);
    }

    /**
     * @param string $tooltip
     * @return string
     */
    public function getTooltip($tooltip)
    {
        /** @var \Mageinn\Hint\Block\Element $block */
        $block = $this->blockFactory->createBlock('Mageinn\Hint\Block\Element');
        return $block
            ->setTooltip($tooltip)
            ->toHtml();
    }

    /**
     * Get config value by key. Deprecated
     *
     * @return mixed
     * @throws LocalizedException
     * @deprecated
     */
    public function getConfig($key)
    {
        throw new LocalizedException(__('Deprecated. Use public methods instead'));
    }

    /**
     * Helper method to return tooltip html only if module enabled and html not empty
     *
     * @param string $html
     * @return string
     */
    private function renderTooltip($html)
    {
        return (! empty($html)) && $this->getActiveFlag() ? $this->getTooltip($html) : '';
    }

    /**
     * Helper method for retrieve config value by path and scope
     *
     * @param string $path The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|string $scopeCode
     * @return mixed
     */
    private function getValue($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Helper method for retrieve config flag by path and scope
     *
     * @param string $path The path through the tree of configuration values, e.g., 'general/store_information/name'
     * @param string $scopeType The scope to use to determine config value, e.g., 'store' or 'default'
     * @param null|string $scopeCode
     * @return bool
     */
    private function isSetFlag($path, $scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag($path, $scopeType, $scopeCode);
    }
}
