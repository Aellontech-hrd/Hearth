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
 * Class UpdateProduct
 * @package Mageinn\Hint\Model\Observer
 */
class UpdateProduct implements ObserverInterface
{
    /** @var \Mageinn\Hint\Helper\Data */
    private $helper;

    /**
     * UpdateProduct constructor.
     * @param \Magento\Framework\View\Element\Context $context
     */
    public function __construct(
        \Mageinn\Hint\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Save customisable options hints in request params
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (! $this->helper->getActiveFlag()) {
            return;
        }
        /** @var \Magento\Framework\App\RequestInterface|\Magento\Framework\App\Request\Http $request */
        $request = $observer->getEvent()->getRequest();
        $params = $request->getParams();
        $product = $request->getParam('product');
        if (! isset($params['product']['options'])) {
            return;
        }
        if (empty($params['product']['options'])) {
            return;
        }
        if (! isset($product['custom_options_hints'])) {
            return;
        }
        foreach ($params['product']['options'] as $optionKey => $option) {
            if (isset($product['custom_options_hints'][$option['option_id']]['hint'])) {
                $option['hint'] = $product['custom_options_hints'][$option['option_id']]['hint'];
            }
            if (isset($option['values']) && !empty($option['values'])) {
                foreach ($option['values'] as $valueKey => $value) {
                    if (isset($value['option_type_id']) &&
                        isset($product['custom_options_hints'][$option['option_id']]['values'][$value['option_type_id']]['hint'])
                    ) {
                        $value['hint'] = $product['custom_options_hints'][$option['option_id']]['values'][$value['option_type_id']]['hint'];
                    }

                    $option['values'][$valueKey] = $value;
                }
            }

            $product['options'][$optionKey] = $option;
        }

        $request->setParam('product', $product);
    }
}
