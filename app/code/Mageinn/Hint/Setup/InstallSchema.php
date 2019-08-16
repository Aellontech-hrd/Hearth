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

/**
 * @category   Mageinn
 * @package    Mageinn_Hint
 * @author     Mageinn
 */
namespace Mageinn\Hint\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class InstallSchema
 * @package Mageinn\Hint\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        $tableName = $installer->getTable('eav_attribute_hint');
        if (! $connection->isTableExists($tableName)) {
            $table = $connection
                ->newTable($tableName)
                ->addColumn(
                    'attribute_hint_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'Attribute Hint Id'
                )
                ->addColumn(
                    'attribute_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ],
                    'Attribute Id'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ],
                    'Store Id'
                )
                ->addColumn(
                    'value',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Value'
                )
                ->setComment('Eav Attribute Hint');
            $connection->createTable($table);
        }

        $tableName = $installer->getTable('eav_attribute_option_hint');
        if (! $connection->isTableExists($tableName)) {
            $table = $connection
                ->newTable($tableName)
                ->addColumn(
                    'attribute_option_hint_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'Attribute Option Hint Id'
                )
                ->addColumn(
                    'attr_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ],
                    'Attribute Id'
                )
                ->addColumn(
                    'option_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ],
                    'Option Id'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                    ],
                    'Store Id'
                )
                ->addColumn(
                    'value',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Value'
                )->setComment('Eav Attribute Option Hint');
            $connection->createTable($table);
        }

        $tableName = $installer->getTable('catalog_product_option');
        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                'option_hint',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Hint',
                ]
            );
        }

        $tableName = $installer->getTable('catalog_product_option_type_value');
        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                'value_hint',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Hint',
                ]
            );
        }
        $installer->endSetup();
    }
}
