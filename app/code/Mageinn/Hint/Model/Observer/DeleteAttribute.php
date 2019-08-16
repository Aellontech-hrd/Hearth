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
 * Class DeleteAttribute
 * @package Mageinn\Hint\Model\Observer
 */
class DeleteAttribute implements ObserverInterface
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
     * @event controller_action_predispatch_catalog_product_attribute_delete
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
     * After delete attribute delete all title and options hints
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->helper->getActiveFlag()) {
            return;
        }
        /** @var \Magento\Framework\App\RequestInterface|\Magento\Framework\App\Request\Http $request */
        $request = $observer->getEvent()->getRequest();
        $attribute_id = $request->getParam('attribute_id');

        if ($attribute_id) {
            $this
                ->deleteItems(
                    $this->createTitleHintCollection()->addFieldToFilter('attribute_id', $attribute_id)
                )->deleteItems(
                    $this->createOptionHintCollection()->addFieldToFilter('attr_id', $attribute_id)
                );
        }
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $collection
     * @return $this;
     * @throws \Exception
     */
    private function deleteItems($collection)
    {
        /** @var \Magento\Framework\Model\AbstractModel $item */
        foreach ($collection as $item) {
            $item->delete();
        }
        return $this;
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
