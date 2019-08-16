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

namespace Mageinn\Hint\Model\ResourceModel\Product;

/**
 * Class Option
 * @package Mageinn\Hint\Model\ResourceModel\Product
 */
class Option extends \Magento\Catalog\Model\ResourceModel\Product\Option
{
    /**
     * Save titles
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return void
     */
    protected function _saveValueTitles(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $titleTableName = $this->getTable('catalog_product_option_title');
        foreach ([\Magento\Store\Model\Store::DEFAULT_STORE_ID, $object->getStoreId()] as $storeId) {
            $existInCurrentStore = $this->getColFromOptionTable($titleTableName, (int)$object->getId(), (int)$storeId);
            $existInDefaultStore = $this->getColFromOptionTable(
                $titleTableName,
                (int)$object->getId(),
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );
            if ($object->getTitle()) {
                if ($existInCurrentStore) {
                    if ($object->getStoreId() == $storeId) {
                        $data = $this->_prepareDataForTable(
                            new \Magento\Framework\DataObject(['title' => $object->getTitle(), 'hint' => $object->getData('hint')]),
                            $titleTableName
                        );
                        $connection->update(
                            $titleTableName,
                            $data,
                            ['option_id = ?' => $object->getId(), 'store_id  = ?' => $storeId]
                        );
                    }
                } else {
                    // we should insert record into not default store only of if it does not exist in default store
                    if (($storeId == \Magento\Store\Model\Store::DEFAULT_STORE_ID && !$existInDefaultStore)
                        || ($storeId != \Magento\Store\Model\Store::DEFAULT_STORE_ID && !$existInCurrentStore)
                    ) {
                        $data = $this->_prepareDataForTable(
                            new \Magento\Framework\DataObject([
                                    'option_id' => $object->getId(),
                                    'store_id' => $storeId,
                                    'title' => $object->getTitle(),
                                    'hint' => $object->getData('hint'),
                               ]),
                            $titleTableName
                        );
                        $connection->insert($titleTableName, $data);
                    }
                }
            } else {
                if ($object->getId() && $object->getStoreId() > \Magento\Store\Model\Store::DEFAULT_STORE_ID
                    && $storeId
                ) {
                    $connection->delete(
                        $titleTableName,
                        ['option_id = ?' => $object->getId(),'store_id  = ?' => $object->getStoreId()]
                    );
                }
            }
        }
    }
}
