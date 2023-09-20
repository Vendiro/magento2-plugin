<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Model\Marketplace;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Vendiro\Connect\Model\Invoice\Collection;
use Vendiro\Connect\Model\Invoice\ResourceModel;
use Vendiro\Connect\Api\Marketplace\DataInterface as MarketplaceInterface;
use Vendiro\Connect\Model\Marketplace\ResourceModel as MarketplaceResource;

/**
 * Class Marketplace
 */
class Data extends AbstractModel implements MarketplaceInterface
{
    /**
     * @var SerializerJson
     */
    private $serializer;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ResourceModel $resource
     * @param Collection $collection
     * @param SerializerJson $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ResourceModel $resource,
        Collection $collection,
        SerializerJson $serializer,
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $registry, $resource, $collection, $data);
    }

    /**
     * @return int
     */
    public function getMarketplaceId(): int
    {
        return (int)$this->getData(self::MARKETPLACE_ID);
    }

    /**
     * @param $value
     *
     * @return MarketplaceInterface
     */
    public function setMarketplaceId($value): MarketplaceInterface
    {
        return $this->setData(self::MARKETPLACE_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCountryCode(): string
    {
        return $this->getData(self::COUNTRY_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCountryCode($value): MarketplaceInterface
    {
        return $this->setData(self::COUNTRY_CODE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCurrency(): string
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * @inheritDoc
     */
    public function setCurrency($value): MarketplaceInterface
    {
        return $this->setData(self::CURRENCY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($value): MarketplaceInterface
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAllowedDocumentTypes(): array
    {
        $types = $this->getData(self::ALLOWED_DOCUMENT_TYPES);
        if (is_string($types)) {
            $types = $this->serializer->unserialize($types);
        }
        return $types;
    }

    /**
     * @inheritDoc
     */
    public function setAllowedDocumentTypes($value): MarketplaceInterface
    {
        return $this->setData(self::ALLOWED_DOCUMENT_TYPES, $value);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(MarketplaceResource::class);
    }
}
