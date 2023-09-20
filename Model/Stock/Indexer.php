<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Stock;

use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Vendiro\Connect\Service\Stock\Set as SetStock;

/**
 * Delta Indexer Class
 */
class Indexer implements IndexerActionInterface, MviewActionInterface
{

    /**
     * @var SetStock
     */
    private $setStock;

    /**
     * Indexer constructor.
     * @param SetStock $setStock
     */
    public function __construct(
        SetStock $setStock
    ) {
        $this->setStock = $setStock;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($ids)
    {
        $this->setStock->execute($ids);
    }

    /**
     * {@inheritdoc}
     */
    public function executeFull()
    {
        $this->setStock->execute([]);
    }

    /**
     * {@inheritdoc}
     */
    public function executeList(array $ids)
    {
        $this->setStock->execute($ids);
    }

    /**
     * {@inheritdoc}
     */
    public function executeRow($id)
    {
        $this->setStock->execute([$id]);
    }
}
