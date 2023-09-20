<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Marketplace;

use Magento\Framework\Api\SearchResultsInterface as FrameworkSearchResultsInterface;

/**
 * Interface for marketplace search results.
 * @api
 */
interface SearchResultsInterface extends FrameworkSearchResultsInterface
{

    /**
     * Gets marketplace items.
     *
     * @return DataInterface[]
     */
    public function getItems(): array;

    /**
     * Sets marketplace items.
     *
     * @param DataInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}
