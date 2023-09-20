<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Api\Marketplace;

/**
 * Interface for marketplace data
 * @api
 */
interface DataInterface
{
    /**
     * Constants for keys of data array.
     */
    public const MARKETPLACE_ID = 'marketplace_id';
    public const COUNTRY_CODE = 'country_code';
    public const CURRENCY = 'currency';
    public const NAME = 'name';
    public const ALLOWED_DOCUMENT_TYPES = 'allowed_document_types';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return self
     */
    public function setEntityId($entityId);

    /**
     * @return int
     */
    public function getMarketplaceId(): int;

    /**
     * @param $value
     *
     * @return self
     */
    public function setMarketplaceId($value): self;

    /**
     * @return string
     */
    public function getCountryCode(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setCountryCode($value): self;

    /**
     * @return string
     */
    public function getCurrency(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setCurrency($value): self;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param $value
     *
     * @return self
     */
    public function setName($value): self;

    /**
     * @return array
     */
    public function getAllowedDocumentTypes(): array;

    /**
     * @param $value
     *
     * @return self
     */
    public function setAllowedDocumentTypes($value): self;
}
