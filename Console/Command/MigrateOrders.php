<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vendiro\Connect\Service\Migrate\Orders as OrderMigrateService;

class MigrateOrders extends Command
{

    private const NAME = 'vendiro:migrate:orders';

    /**
     * @var OrderMigrateService
     */
    private $orderMigrateService;

    /**
     * @param OrderMigrateService $orderMigrateService
     */
    public function __construct(
        OrderMigrateService $orderMigrateService
    ) {
        $this->orderMigrateService = $orderMigrateService;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Migrate orders from old module');

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $results = $this->orderMigrateService->execute();
            if (!empty($results['success'])) {
                $output->writeln(sprintf('<info>%s</info>', $results['message']));
            } else {
                $output->writeln(sprintf('<error>%s</error>', $results['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
        }

        return Cli::RETURN_SUCCESS;
    }
}
