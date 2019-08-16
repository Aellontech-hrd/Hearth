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

namespace Mageinn\Hint\Block\Product\View\Options\Type;

/**
 * Class Text
 * @package Mageinn\Hint\Block\Product\View\Options\Type
 */
class Text
{
    /**
     * @param \Magento\Catalog\Block\Product\View\Options\Type\Text $subject
     * @param string $result
     * @return string
     * @see \Magento\Catalog\Block\Product\View\Options\Type\Text::getTemplate()
     */
    public function afterGetTemplate(\Magento\Catalog\Block\Product\View\Options\Type\Text $subject, $result)
    {
        return 'Mageinn_Hint::product/view/options/type/text.phtml';
    }
}
