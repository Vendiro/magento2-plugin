<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Plugin\Catalog\Model\Indexer\Product\Eav;

use Magento\Framework\Indexer\ActionInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Vendiro\Connect\Model\Stock\Indexer;

/**
 * Binds stock indexer calls to the appropriate EAV indexer ones
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
     * BindDeltaIndexer constructor.
     *
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
     * @return void
     *
     */
    public function beforeExecute(ActionInterface $subject, $ids)
    {
        if (!$this->indexerRegistry->get('vendiro_stock')->isScheduled()) {
            $this->stockIndexer->execute($ids);
        }
    }

    /**
     * Binding executeList method
     *
     * @param ActionInterface $subject
     * @param array $ids
     * @return void
     *
     */
    public function beforeExecuteList(ActionInterface $subject, array $ids)
    {
        if (!$this->indexerRegistry->get('vendiro_stock')->isScheduled()) {
            $this->stockIndexer->executeList($ids);
        }
    }

    /**
     * Binding executeRow method
     *
     * @param ActionInterface $subject
     * @param int $id
     * @return void
     *
     */
    public function beforeExecuteRow(ActionInterface $subject, $id)
    {
        if (!$this->indexerRegistry->get('vendiro_stock')->isScheduled()) {
            $this->stockIndexer->executeRow($id);
        }
    }
}
