<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Stock;

use Magento\Framework\Api\SearchResultsInterface as FrameworkSearchResultsInterface;

/**
 * Interface for carrier search results.
 * @api
 */
interface SearchResultsInterface extends FrameworkSearchResultsInterface
{

    /**
     * Gets carrier items.
     *
     * @return DataInterface[]
     */
    public function getItems(): array;

    /**
     * Sets carrier items.
     *
     * @param DataInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
