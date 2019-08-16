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

namespace Mageinn\Hint\Block\Product\View;

/**
 * Class Attributes
 * @package Mageinn\Hint\Block\Product\View
 */
class Attributes
{
    /** @var \Mageinn\Hint\Helper\Data $helper */
    protected $helper;

    /**
     * @param \Mageinn\Hint\Helper\Data $helper
     */
    public function __construct(
        \Mageinn\Hint\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\Attributes $subject
     * @param array $result
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetAdditionalData(\Magento\Catalog\Block\Product\View\Attributes $subject, $result)
    {
        // Prepare White List From extended Block
        $whiteList = [];
        if (!empty($result)) {
            foreach ($result as $attributeCode => $value) {
                $whiteList[] = $attributeCode;
            }
        }

        // Reconstruct $result by WhiteList and add hints inside row
        $data = [];
        $product = $subject->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            if (! in_array($attribute->getAttributeCode(), $whiteList)) {
                continue;
            }
            $value = $attribute->getFrontend()->getValue($product);
            $data[$attribute->getAttributeCode()] = [
                'label' => __($attribute->getStoreLabel()),
                'value' => $value,
                'code' => $attribute->getAttributeCode(),
                'hints' => [
                    'label' => $this->helper->getTitleHintHtml($attribute->getAttributeId()),
                    'value' => $this->helper->getOptionHintHtml(
                        $product->getData($attribute->getAttributeCode()),
                        $attribute->getAttributeId()
                    )
                ]
            ];
        }

        return $data;
    }
}
