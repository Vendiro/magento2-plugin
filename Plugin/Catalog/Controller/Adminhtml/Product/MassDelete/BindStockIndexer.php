<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Plugin\Catalog\Controller\Adminhtml\Product\MassDelete;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Controller\Adminhtml\Product\MassDelete;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Ui\Component\MassAction\Filter;
use Vendiro\Connect\Model\Stock\Indexer;

/**
 * Binds stock indexer calls to the mass delete controller
 */
class BindStockIndexer
{
    /**
     * @var Indexer
     */
    private $stockIndexer;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ProductCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @param Indexer $stockIndexer
     * @param Filter $filter
     * @param ProductCollectionFactory $collectionFactory
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        Indexer $stockIndexer,
        Filter $filter,
        ProductCollectionFactory $collectionFactory,
        IndexerRegistry $indexerRegistry
    ) {
        $this->stockIndexer = $stockIndexer;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * Execute delta indexation process for deleted products
     *
     * @param MassDelete $subject
     * @param callable $proceed
     * @return Redirect
     * @throws LocalizedException
     */
    public function aroundExecute(MassDelete $subject, callable $proceed)
    {
        $result = $proceed();

        if (!$this->indexerRegistry->get('vendiro_stock')->isScheduled()) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $this->stockIndexer->execute($collection->getAllIds());
        }

        return $result;
    }
}
