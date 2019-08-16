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

namespace Mageinn\Hint\Model\Observer;

use \Magento\Framework\Event\ObserverInterface;

/**
 * Class UpdateAttribute
 * @package Mageinn\Hint\Model\Observer
 */
class UpdateAttribute implements ObserverInterface
{
    /** @var \Mageinn\Hint\Model\ResourceModel\Attribute\CollectionFactory */
    private $titleHintCollectionFactory;

    /** @var \Mageinn\Hint\Model\ResourceModel\Option\CollectionFactory */
    private $optionHintCollectionFactory;

    /** @var \Mageinn\Hint\Helper\Data */
    private $helper;

    /**
     * @param \Mageinn\Hint\Model\ResourceModel\Attribute\CollectionFactory $titleHintCollectionFactory
     * @param \Mageinn\Hint\Model\ResourceModel\Option\CollectionFactory $optionHintCollectionFactory
     * @param \Mageinn\Hint\Helper\Data $helper
     */
    public function __construct(
        \Mageinn\Hint\Model\ResourceModel\Attribute\CollectionFactory $titleHintCollectionFactory,
        \Mageinn\Hint\Model\ResourceModel\Option\CollectionFactory $optionHintCollectionFactory,
        \Mageinn\Hint\Helper\Data $helper
    ) {
        $this->titleHintCollectionFactory = $titleHintCollectionFactory;
        $this->optionHintCollectionFactory = $optionHintCollectionFactory;
        $this->helper = $helper;
    }

    /**
     * After update attribute save all attribute title and options hints
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Exception
     * @event controller_action_predispatch_catalog_product_attribute_save
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->helper->getActiveFlag()) {
            return;
        }
        /** @var \Magento\Framework\App\RequestInterface|\Magento\Framework\App\Request\Http $request */
        $request = $observer->getEvent()->getRequest();
        $attributeId = $request->getParam('attribute_id');
        if ($attributeId) {
            $this->saveTitles($attributeId, $request->getParam('hint_title'));
            $this->saveOptions($attributeId, $request->getParam('hint_option'));
        }
    }

    /**
     * @param int|string $attributeId
     * @param array $titles
     * @throws \Exception
     */
    private function saveTitles($attributeId, $titles = [])
    {
        if (empty($titles)) {
            return;
        }
        foreach ($titles as $storeId => $html) {
            $collection = $this->createTitleHintCollection();
            $collection->addFieldToFilter('attribute_id', $attributeId);
            $collection->addFieldToFilter('store_id', $storeId);
            /** @var \Mageinn\Hint\Model\Attribute $model */
            $model = $collection->setPageSize(1)->getFirstItem();
            $model->addData([
                'attribute_id' => $attributeId,
                'store_id'     => $storeId,
                'value'        => $html,
            ]);
            $model->save();
        }
    }

    /**
     * @param int|string $attributeId
     * @param array $options
     * @throws \Exception
     */
    private function saveOptions($attributeId, $options = [])
    {
        if (empty($options)) {
            return;
        }
        foreach ($options as $optionId => $value) {
            foreach ($value as $storeId => $html) {
                $collection = $this->createOptionHintCollection();
                $collection->addFieldToFilter('attr_id', $attributeId);
                $collection->addFieldToFilter('store_id', $storeId);
                $collection->addFieldToFilter('option_id', $optionId);
                /** @var \Mageinn\Hint\Model\Option $model */
                $model = $collection->setPageSize(1)->getFirstItem();
                $model->addData([
                    'attr_id'   => $attributeId,
                    'store_id'  => $storeId,
                    'option_id' => $optionId,
                    'value'     => $html,
                ]);
                $model->save();
            }
        }
    }

    /**
     * @return \Mageinn\Hint\Model\ResourceModel\Attribute\Collection
     */
    private function createTitleHintCollection()
    {
        return $this->titleHintCollectionFactory->create();
    }

    /**
     * @return \Mageinn\Hint\Model\ResourceModel\Option\Collection
     */
    private function createOptionHintCollection()
    {
        return $this->optionHintCollectionFactory->create();
    }
}
