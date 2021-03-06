<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Vendiro\Setup\V100\Schema;

use Magento\Framework\DB\Ddl\Table;
use TIG\Vendiro\Setup\AbstractTableInstaller;

class InstallStockTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_vendiro_stock';

    /**
     * @return void
     * @throws \Zend_Db_Exception
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $this->addEntityId();
        $this->addText('product_sku', 'Product SKU', 64, false);
        $this->addText('status', 'Status', 32, false);
        $this->addTimestamp('created_at', 'Created at', false, Table::TIMESTAMP_INIT);
        $this->addTimestamp('updated_at', 'Updated at', false, Table::TIMESTAMP_INIT_UPDATE);
        $this->addIndex(['product_sku']);
    }
}
