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

namespace Mageinn\Hint\Model\Product;

/**
 * Product option model
 * Class Option
 * @package Mageinn\Hint\Model\Product
 */
class Option extends \Magento\Catalog\Model\Product\Option
{
    /**
     * Retrieve option hint
     *
     * @return string
     */
    public function getHint()
    {
        return $this->_getData('store_hint') === null ?
            $this->_getData('default_hint') : $this->_getData('store_hint');
    }
}
