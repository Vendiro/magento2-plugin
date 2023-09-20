<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class TransactionActions
 * grid actions
 */
class ProductActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * *
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['view_product'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'catalog/product/edit',
                        ['id' => $item['entity_id']]
                    ),
                    'label' => (string)__('View'),
                    'hidden' => false,
                ];
                $item[$this->getData('name')]['sync'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'vendiro/stock/update',
                        ['entity_id' => $item['entity_id']]
                    ),
                    'label' => (string)__('Sync Manual'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}
