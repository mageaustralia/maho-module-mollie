<?php
/**
 * Copyright (c) 2012-2019, Mollie B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * Consolidated install script for Mollie_Mpm on Maho.
 * Merges install-5.0.0 + all upgrades through 5.4.0 into a single fresh install.
 *
 * @category    Mollie
 * @package     Mollie_Mpm
 * @copyright   Copyright (c) 2012-2019 Mollie B.V. (https://www.mollie.nl)
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD-License 2
 */

/** @var Mage_Sales_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$connection = $installer->getConnection();

// --- 1. EAV attributes on sales entities ---

$installer->addAttribute('order', 'mollie_transaction_id', [
    'type'             => 'varchar',
    'default'          => null,
    'label'            => 'Mollie Transaction ID',
    'visible'          => false,
    'required'         => false,
    'visible_on_front' => false,
    'user_defined'     => false,
]);

$installer->addAttribute('shipment', 'mollie_shipment_id', [
    'type'             => 'varchar',
    'default'          => null,
    'label'            => 'Mollie Shipment ID',
    'visible'          => false,
    'required'         => false,
    'visible_on_front' => false,
    'user_defined'     => false,
]);

// --- 2. Order lines table ---

$table = $connection
    ->newTable($installer->getTable('mpm/orderLines'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,
    ], 'OrderLine Id')
    ->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true, 'nullable' => false, 'default' => 0,
    ], 'Item Id')
    ->addColumn('line_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable' => false,
    ], 'Line Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable' => false,
    ], 'Order Id')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable' => false,
    ], 'OrderLine Type')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 255, [
        'nullable' => false,
    ], 'Product SKU')
    ->addColumn('qty_ordered', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true, 'nullable' => false, 'default' => 0,
    ], 'Qty Ordered')
    ->addColumn('qty_paid', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true, 'nullable' => false, 'default' => 0,
    ], 'Qty Paid')
    ->addColumn('qty_canceled', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true, 'nullable' => false, 'default' => 0,
    ], 'Qty Canceled')
    ->addColumn('qty_shipped', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true, 'nullable' => false, 'default' => 0,
    ], 'Qty Shipped')
    ->addColumn('qty_refunded', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true, 'nullable' => false, 'default' => 0,
    ], 'Qty Refunded')
    ->addColumn('unit_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable' => false,
    ], 'Unit Price')
    ->addColumn('discount_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable' => false,
    ], 'Discount Amount')
    ->addColumn('total_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable' => false,
    ], 'Total Amount')
    ->addColumn('vat_rate', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable' => false,
    ], 'Vat Rate')
    ->addColumn('vat_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', [
        'nullable' => false,
    ], 'Vat Amount')
    ->addColumn('currency', Varien_Db_Ddl_Table::TYPE_TEXT, 3, [
        'nullable' => false,
    ], 'Currency Code')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => false,
    ], 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, [
        'nullable' => true,
    ], 'Updated At')
    ->addIndex($installer->getIdxName('mpm/orderLines', ['item_id']), ['item_id'])
    ->addIndex($installer->getIdxName('mpm/orderLines', ['line_id']), ['line_id'])
    ->addIndex($installer->getIdxName('mpm/orderLines', ['order_id']), ['order_id'])
    ->addIndex($installer->getIdxName('mpm/orderLines', ['type']), ['type'])
    ->setComment('Mollie Order Lines');

$connection->createTable($table);

// --- 3. Payment fee columns on sales flat tables ---

$feeDefinition = 'decimal(12,4) null default null';
$feeTables = [
    'sales/order',
    'sales/quote_address',
    'sales/invoice',
    'sales/creditmemo',
];

foreach ($feeTables as $tableName) {
    $feeTable = $installer->getTable($tableName);
    $connection->addColumn($feeTable, 'mollie_mpm_payment_fee', $feeDefinition);
    $connection->addColumn($feeTable, 'base_mollie_mpm_payment_fee', $feeDefinition);
    $connection->addColumn($feeTable, 'mollie_mpm_payment_fee_tax', $feeDefinition);
    $connection->addColumn($feeTable, 'base_mollie_mpm_payment_fee_tax', $feeDefinition);
}

$installer->endSetup();
