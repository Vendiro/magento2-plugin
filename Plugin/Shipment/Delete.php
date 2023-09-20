<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Plugin\Shipment;

use Magento\Framework\Exception\LocalizedException;
use Vendiro\Connect\Api\TrackQueue\RepositoryInterface as TrackQueueRepository;
use Vendiro\Connect\Model\TrackQueue\Data;

class Delete
{
    /**
     * @var TrackQueueRepository
     */
    private $trackQueueRepository;

    /**
     * @param TrackQueueRepository $trackQueueRepository
     */
    public function __construct(
        TrackQueueRepository $trackQueueRepository
    ) {
        $this->trackQueueRepository = $trackQueueRepository;
    }

    /**
     * @param $subject
     * @throws LocalizedException
     */
    public function afterDelete($subject)
    {
        $tracks = (array)$this->trackQueueRepository->get($subject->getId());

        /** @var Data $track */
        $track = array_pop($tracks);
        $this->trackQueueRepository->delete($track);
    }
}
