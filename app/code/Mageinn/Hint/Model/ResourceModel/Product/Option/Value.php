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

namespace Mageinn\Hint\Model\ResourceModel\Product\Option;

/**
 * Class Value
 * @package Mageinn\Hint\Model\ResourceModel\Product\Option
 */
class Value extends \Magento\Catalog\Model\ResourceModel\Product\Option\Value
{
    /**
     * Save option value title data
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     */
    protected function _saveValueTitles(\Magento\Framework\Model\AbstractModel $object)
    {
        foreach ([\Magento\Store\Model\Store::DEFAULT_STORE_ID, $object->getStoreId()] as $storeId) {
            $titleTable = $this->getTable('catalog_product_option_type_title');
            $select = $this->getConnection()->select()->from(
                $titleTable,
                ['option_type_id']
            )->where(
                'option_type_id = ?',
                (int)$object->getId()
            )->where(
                'store_id = ?',
                (int)$storeId
            );
            $optionTypeId = $this->getConnection()->fetchOne($select);
            $existInCurrentStore = $this->getOptionIdFromOptionTable($titleTable, (int)$object->getId(), (int)$storeId);
            if ($object->getTitle()) {
                if ($existInCurrentStore) {
                    if ($storeId == $object->getStoreId()) {
                        $where = [
                            'option_type_id = ?' => (int)$optionTypeId,
                            'store_id = ?' => $storeId,
                        ];
                        $bind = ['title' => $object->getTitle(), 'hint' => $object->getHint()];
                        $this->getConnection()->update($titleTable, $bind, $where);
                    }
                } else {
                    $existInDefaultStore = $this->getOptionIdFromOptionTable(
                        $titleTable,
                        (int)$object->getId(),
                        \Magento\Store\Model\Store::DEFAULT_STORE_ID
                    );
                    // we should insert record into not default store only of if it does not exist in default store
                    if (($storeId == \Magento\Store\Model\Store::DEFAULT_STORE_ID && !$existInDefaultStore)
                        || ($storeId != \Magento\Store\Model\Store::DEFAULT_STORE_ID && !$existInCurrentStore)
                    ) {
                        $bind = [
                            'option_type_id' => (int)$object->getId(),
                            'store_id' => $storeId,
                            'title' => $object->getTitle(),
                            'hint' => $object->getHint(),
                        ];
                        $this->getConnection()->insert($titleTable, $bind);
                    }
                }
            } else {
                if ($storeId
                    && $optionTypeId
                    && $object->getStoreId() > \Magento\Store\Model\Store::DEFAULT_STORE_ID
                ) {
                    $where = [
                        'option_type_id = ?' => (int)$optionTypeId,
                        'store_id = ?' => $storeId,
                    ];
                    $this->getConnection()->delete($titleTable, $where);
                }
            }
        }
    }
}
