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
 * @package     Mageinn_AdminExtra
 * @copyright   Copyright (c) 2017 Mageinn. (http://mageinn.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mageinn\Hint\Block\Adminhtml\Attribute\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Hints
 * @package Mageinn\Hint\Block\Adminhtml\Attribute\Edit\Tab
 */
class Hints extends Generic implements TabInterface
{
    /** @var \Magento\Eav\Model\Entity\AttributeFactory */
    private $attributeFactory;

    /** @var \Magento\Cms\Model\Wysiwyg\Config */
    private $wysiwygConfig;

    /** @var \Mageinn\Hint\Helper\Data */
    private $helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Mageinn\Hint\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Mageinn\Hint\Helper\Data $helper,
        array $data = []
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritDoc
     */
    public function getTabLabel()
    {
        return __('Hints');
    }

    /**
     * @inheritDoc
     */
    public function getTabTitle()
    {
        return __('Hints');
    }

    /**
     * @inheritDoc
     */
    public function canShowTab()
    {
        return $this->getRequestAttributeId() && $this->helper->getActiveFlag();
    }

    /**
     * @inheritDoc
     */
    public function isHidden()
    {
        return ! $this->canShowTab();
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset(
            'title_hint',
            ['legend' => __('Manage Title Hint')]
        );

        foreach ($this->getStores() as $store) {
            $fieldset->addField(
                'hint_title_' . $store->getId(),
                'editor',
                [
                    'name' => 'hint_title[' . $store->getId() . ']',
                    'label' => $store->getName(),
                    'config' => $this->wysiwygConfig->getConfig(),
                    'value' => $this->helper->getTitleHint($this->getAttribute()->getAttributeId(), $store->getId())
                ]
            );
        }

        if (!empty($this->getOptions())) {
            $i = 0;
            foreach ($this->getOptions() as $option) {
                $fieldset = $form->addFieldset(
                    'title_option_' . $i,
                    ['legend' => __('Manage Option Hint For %1', $option['label'])]
                );
                $k = 0;
                foreach ($this->getStores() as $store) {
                    $fieldset->addField(
                        'title_option_' . $i . '_' . $k,
                        'editor',
                        [
                            'name' => 'hint_option[' . $option['value'] . '][' . $store->getId() . ']',
                            'label' => $store->getName(),
                            'config' => $this->wysiwygConfig->getConfig(),
                            'value' => $this->helper->getOptionHint(
                                $option['value'],
                                $this->getAttribute()->getAttributeId(),
                                $store->getId()
                            )
                        ]
                    );
                    $k++;
                }

                $i++;
            }
        }

        return $this->setForm($form);
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    private function getAttribute()
    {
        if (! $attributeId = $this->getRequestAttributeId()) {
            throw new LocalizedException(__('Attribute ID was not specified'));
        }
        $model = $this->createAttribute()->load($this->getRequestAttributeId());
        if (! $model->getId()) {
            throw new NoSuchEntityException(__('The attribute with a "%1" attributeId doesn\'t exist', $attributeId));
        }
        return $model;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    private function getOptions()
    {
        $sourceModel = $this->getAttribute()->getSource();
        $options = $sourceModel->getAllOptions();
        // unset empty option
        if (!empty($options)) {
            foreach ($options as $key => $option) {
                if ((bool)trim($option['label']) !== true && (bool)trim($option['value']) !== true) {
                    unset($options[$key]);
                }
            }
        }

        return $options;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    private function getStores()
    {
        // Admin store - true flag
        return $this->_storeManager->getStores(false);
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    private function createAttribute()
    {
        return $this->attributeFactory->create();
    }

    /**
     * @return int|null
     */
    private function getRequestAttributeId()
    {
        return $this->getRequest()->getParam('attribute_id');
    }
}
