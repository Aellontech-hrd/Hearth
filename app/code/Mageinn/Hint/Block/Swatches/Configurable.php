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

namespace Mageinn\Hint\Block\Swatches;

/**
 * Class Configurable
 * @package Mageinn\Hint\Block\Swatches
 */
class Configurable
{
    /** @var \Magento\Framework\Json\EncoderInterface $jsonEncoder */
    protected $jsonEncoder;

    /** @var \Mageinn\Hint\Helper\Data $helper */
    protected $helper;

    /**
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Mageinn\Hint\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Mageinn\Hint\Helper\Data $helper
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetJsonSwatchConfig(\Magento\Swatches\Block\Product\Renderer\Configurable $subject, $result)
    {
        $config = \json_decode($result, true);

        foreach ($config as $attributeId => $swatchDataForAttribute) {
            foreach ($swatchDataForAttribute as $optionId => $optionData) {
                $hint = $this->helper->getOptionHint($optionId, $attributeId);
                if (! empty($hint)) {
                    $config[$attributeId][$optionId]['hint'] = $hint;
                }
            }
        }

        return $this->jsonEncoder->encode($config);
    }

    /**
     * Replace default templates
     *
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param string $result
     * @return string
     */
    public function afterGetTemplate(\Magento\Swatches\Block\Product\Renderer\Configurable $subject, $result)
    {
        return $result == \Magento\Swatches\Block\Product\Renderer\Configurable::CONFIGURABLE_RENDERER_TEMPLATE ?
            $this->helper->getConfigurableRendererTemplate() : $this->helper->getSwatchRendererTemplate();
    }
}
