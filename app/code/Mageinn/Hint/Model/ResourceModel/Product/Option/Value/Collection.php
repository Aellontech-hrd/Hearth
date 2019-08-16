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

namespace Mageinn\Hint\Model\ResourceModel\Product\Option\Value;

/**
 * Class Collection
 * @package Mageinn\Hint\Model\ResourceModel\Product\Option\Value
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection
{
    /**
     * Add title to result
     *
     * @param int $storeId
     * @return $this
     */
    public function addTitleToResult($storeId)
    {
        parent::addTitleToResult($storeId);
        $optionTitleTable = $this->getTable('catalog_product_option_type_title');

        $this->getSelect()->joinLeft(
            ['default_value_title_hints' => $optionTitleTable],
            'default_value_title_hints.option_type_title_id = default_value_title.option_type_title_id',
            ['default_hint' => 'hint']
        );
        $this->getSelect()->joinLeft(
            ['store_value_title_hints' => $optionTitleTable],
            'store_value_title_hints.option_type_title_id = store_value_title.option_type_title_id',
            ['store_hint' => 'hint']
        );

        return $this;
    }
}
