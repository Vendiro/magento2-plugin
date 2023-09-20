<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Invoice;

use Magento\Framework\Api\SearchResultsInterface as FrameworkSearchResultsInterface;

/**
 * Interface for invoice search results.
 * @api
 */
interface SearchResultsInterface extends FrameworkSearchResultsInterface
{

    /**
     * Gets invoice items.
     *
     * @return DataInterface[]
     */
    public function getItems(): array;

    /**
     * Sets invoice items.
     *
     * @param DataInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
