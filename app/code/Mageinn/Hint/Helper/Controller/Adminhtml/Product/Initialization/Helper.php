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
 * @copyright   Copyright (c) 2017 Mageinn. (http://mageinn.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class Helper
 *
 * @category   Mageinn
 * @package    Mageinn_Hint
 * @author     Mageinn
 */
namespace Mageinn\Hint\Helper\Controller\Adminhtml\Product\Initialization;

/**
 * Class Helper
 * @package Mageinn\Hint\Helper\Controller\Adminhtml\Product\Initialization
 */
class Helper
{
    /** @var \Magento\Framework\App\RequestInterface */
    private $request;

    /** @var \Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory */
    private $customOptionFactory;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory $customOptionFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory $customOptionFactory
    ) {
        $this->request = $request;
        $this->customOptionFactory = $customOptionFactory;
    }

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @param array $productData
     * @return \Magento\Catalog\Model\Product
     * @see \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper::initializeFromData()
     */
    public function aroundInitializeFromData(
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product,
        array $productData
    ) {
        $product = $proceed($product, $productData);

        if (isset($productData['options'])) {
            $productOptions = $productData['options'];
            unset($productData['options']);
        } else {
            $productOptions = [];
        }
        /**
         * Reinitialize product options
         */
        if (isset($productData['custom_options_hints']) && $productOptions && !$product->getOptionsReadonly()) {
            $hints = $productData['custom_options_hints'];

            // Mark custom options that should to fall back to default value
            $options = $subject->mergeProductOptions(
                $productOptions,
                $this->request->getPost('options_use_default')
            );

            $customOptions = [];
            foreach ($options as $customOptionData) {
                if (! empty($customOptionData['is_delete'])) {
                    continue;
                }
                if (empty($customOptionData['option_id'])) {
                    $customOptionData['option_id'] = null;
                }
                if (isset($customOptionData['values'])) {
                    $customOptionData['values'] = array_filter($customOptionData['values'], function ($valueData) {
                        return empty($valueData['is_delete']);
                    });
                }
                $customOption = $this->createCustomOption(['data' => $customOptionData]);
                $customOption->setProductSku($product->getSku());
                if (isset($hints[$customOptionData['option_id']]['hint'])) {
                    $customOption->setHint($hints[$customOptionData['option_id']]['hint']);
                }
                $customOptions[] = $customOption;
            }
            $product->setOptions($customOptions);
        }

        $product->setCanSaveCustomOptions(
            !empty($productData['affect_product_custom_options']) && !$product->getOptionsReadonly()
        );

        return $product;
    }

    /**
     * @param array $data
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionInterface
     */
    private function createCustomOption($data = [])
    {
        return $this->customOptionFactory->create($data);
    }
}
