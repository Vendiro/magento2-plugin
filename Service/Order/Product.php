<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Service\Order;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Vendiro\Connect\Exception as VendiroException;

class Product
{
    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param DataObjectFactory $dataObjectFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        DataObjectFactory $dataObjectFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $apiProduct
     * @return DataObject
     */
    public function createProductDataFromApiData($apiProduct): DataObject
    {
        return $this->dataObjectFactory->create(
            [
                'qty' => (int)$apiProduct['amount'],
                'custom_price' => (float)$apiProduct['value']
            ]
        );
    }

    /**
     * @param string $sku
     * @param int|null $storeId
     *
     * @return ProductInterface
     * @throws VendiroException
     */
    public function getBySku(string $sku, int $storeId = null): ProductInterface
    {
        try {
            $product = $this->productRepository->get($sku, false, $storeId);
        } catch (NoSuchEntityException $exception) {
            $errorMessage = "The requested product {$sku} wasn't found";
            throw new VendiroException(__($errorMessage));
        }

        return $product;
    }
}
