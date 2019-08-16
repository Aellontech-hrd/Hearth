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
 * Class ConfigurableWithSwatch
 * @package Mageinn\Hint\Block\Swatches
 */
class ConfigurableWithSwatch
{
    /** @var \Mageinn\Hint\Helper\Data $helper */
    private $helper;

    /**
     * @param \Mageinn\Hint\Helper\Data $helper
     */
    public function __construct(
        \Mageinn\Hint\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $subject
     * @param array $result
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @see \Magento\ConfigurableProduct\Model\ConfigurableAttributeData::getAttributesData()
     */
    public function afterGetAttributesData($subject, $result)
    {
        foreach ($result['attributes'] as &$item) {
            $item['hint'] = $this->helper->getTitleHint($item['id']);
        }

        return $result;
    }
}
