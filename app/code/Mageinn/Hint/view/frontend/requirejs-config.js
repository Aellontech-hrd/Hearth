/**
 * Mageinn
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageinn.com license that is
 * available through the world-wide-web at this URL:
 * https://mageinn.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageinn
 * @package     Mageinn_Hint
 * @copyright   Copyright (c) 2017 Mageinn (http://mageinn.com/)
 * @license     https://mageinn.com/LICENSE.txt
 */

var config = {
    map: {
        '*': {
            tippedjs: 'Mageinn_Hint/js/tipped'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Mageinn_Hint/js/swatch-renderer-mixin': true
            }
        }
    }
};