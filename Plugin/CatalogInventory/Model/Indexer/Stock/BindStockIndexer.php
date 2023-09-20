<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Plugin\CatalogInventory\Model\Indexer\Stock;

use Magento\Framework\Indexer\ActionInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Vendiro\Connect\Model\Stock\Indexer;

/**
 * Binds stock indexer calls to the appropriate stock indexer ones
 */
class BindStockIndexer
{
    /**
     * @var Indexer
     */
    private $stockIndexer;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @param Indexer $stockIndexer
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        Indexer $stockIndexer,
        IndexerRegistry $indexerRegistry
    ) {
        $this->stockIndexer = $stockIndexer;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Binding execute method
     *
     * @param ActionInterface $subject
     * @param array $ids
     * @return mixed
     */
    public function beforeExecute(ActionInterface $subject, $ids)
    {
        if (!$this->indexerRegistry->get('vendiro_stock')->isScheduled()) {
            $this->stockIndexer->execute($ids);
        }

        return null;
    }

    /**
     * Binding executeList method
     *
     * @param ActionInterface $subject
     * @param array $ids
     * @return array
     */
    public function beforeExecuteList(ActionInterface $subject, array $ids)
    {
        if (!$this->indexerRegistry->get('vendiro_stock')->isScheduled()) {
            $this->stockIndexer->executeList($ids);
        }

        return null;
    }

    /**
     * Binding executeRow method
     *
     * @param ActionInterface $subject
     * @param int $id
     * @return mixed
     */
    public function beforeExecuteRow(ActionInterface $subject, $id)
    {
        if (!$this->indexerRegistry->get('vendiro_stock')->isScheduled()) {
            $this->stockIndexer->executeRow($id);
        }

        return null;
    }
}
