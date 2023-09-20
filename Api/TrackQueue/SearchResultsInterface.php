<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\TrackQueue;

use Magento\Framework\Api\SearchResultsInterface as FrameworkSearchResultsInterface;

/**
 * Interface for track queue search results.
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
