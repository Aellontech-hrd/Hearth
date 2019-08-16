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

namespace Mageinn\Hint\Ui\DataProvider\Product\Form\Modifier;

/**
 * Class CustomOptions
 * @package Mageinn\Hint\Ui\DataProvider\Product\Form\Modifier
 */
class CustomOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    const CONTAINER_HEADER_NAME = 'container_header';

    /** @var \Magento\Catalog\Model\Locator\LocatorInterface */
    protected $locator;

    /** @var \Magento\Catalog\Model\Product\OptionFactory */
    protected $optionFactory;

    /** @var \Mageinn\Hint\Helper\Data */
    protected $helper;

    /** @var array */
    protected $meta = [];

    /**
     * @param \Magento\Catalog\Model\Locator\LocatorInterface $locator
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Mageinn\Hint\Helper\Data $helper
     */
    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Mageinn\Hint\Helper\Data $helper
    ) {
        $this->locator = $locator;
        $this->optionFactory = $optionFactory;
        $this->helper = $helper;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return array_replace_recursive($data, []);
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->addFieldset();

        return $this->meta;
    }

    /**
     * Adding custom UI elements
     *
     * @return void
     */
    protected function addFieldset()
    {
        if (! $this->helper->getActiveFlag()) {
            return;
        }
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $store = $this->locator->getStore();
        $storeId = $store->getId();
        $options = $this->createOption()->getProductOptionCollection($product->setStoreId($storeId));
        if (empty($options)) {
            return;
        }
        $meta = [
            'custom_options_hints' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Customizable Options Hints'),
                            'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                            'dataScope' => 'data.product',
                            'collapsible' => true,
                            'notice' => 'test',
                            'sortOrder' => 42,
                        ]
                    ]
                ],
                'children' => $this->getHeaderContainerConfig(10, count($options))
            ]
        ];
        foreach ($options as $order => $option) {
            $metaItem = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => $option->getTitle(),
                            'componentType' => \Magento\Ui\Component\Form\Fieldset::NAME,
                            'collapsible' => true,
                            'sortOrder' => $order
                        ]
                    ]
                ],
                'children' => []
            ];
            $optionContainer = 'hint_option_container_' . $option->getOptionId();
            $metaItem['children'][$optionContainer] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => null,
                            'componentType' => \Magento\Ui\Component\Container::NAME,
                            'sortOrder' => 0
                        ]
                    ]
                ],
                'children' => [
                    'hint_option_' . $option->getOptionId() . '_hint' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => null,
                                    'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                                    'formElement' => \Magento\Ui\Component\Form\Element\Wysiwyg::NAME,
                                    'dataScope' => 'custom_options_hints.' . $option->getOptionId() . '.hint',
                                    'value' => $option->getHint(),
                                    'collapsible' => true,
                                    'wysiwygConfigData' => ['height' => '50px'],
                                    'wysiwyg' => true,
                                    'sortOrder' => 0
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $meta['custom_options_hints']['children']['hint_option_' . $option->getOptionId()] = $metaItem;
        }

        $this->meta = array_replace_recursive($this->meta, $meta);
    }

    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @param int $count
     * @return array
     */
    protected function getHeaderContainerConfig($sortOrder, $count)
    {
        return $count ? [] : [
            static::CONTAINER_HEADER_NAME => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => 'Note',
                            'formElement' => \Magento\Ui\Component\Container::NAME,
                            'componentType' => \Magento\Ui\Component\Container::NAME,
                            'template' => 'ui/form/components/complex',
                            'sortOrder' => $sortOrder,
                            'content' => __('Please add custom options first and save the product in order to edit hints.'),
                        ],
                    ],
                ],
                'children' => []
            ]
        ];
    }

    /**
     * @return \Magento\Catalog\Model\Product\Option
     */
    private function createOption()
    {
        return $this->optionFactory->create();
    }
}
