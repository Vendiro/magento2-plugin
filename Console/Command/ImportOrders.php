<?php
/**
 * Copyright © Vendiro. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vendiro\Connect\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vendiro\Connect\Api\Config\RepositoryInterface as ConfigProvider;
use Vendiro\Connect\Service\Order\Import as OrderImportService;

class ImportOrders extends Command
{

    private const NAME = 'vendiro:import:orders';
    private const LIMIT = 'limit';
    private const FORCE_STORE = 'force-store';
    private const FORCE_EXISTING = 'force-existing';

    /**
     * @var OrderImportService
     */
    private $orderImportService;
    /**
     * @var ConfigProvider
     */
    private $configProvider;
    /**
     * @var State
     */
    private $state;

    /**
     * @param OrderImportService $orderImportService
     * @param ConfigProvider $configProvider
     * @param State $state
     */
    public function __construct(
        OrderImportService $orderImportService,
        ConfigProvider $configProvider,
        State $state
    ) {
        $this->orderImportService = $orderImportService;
        $this->configProvider = $configProvider;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Run import orders')
            ->setDefinition($this->getOptions());

        parent::configure();
    }

    /**
     * @return InputOption[]
     */
    private function getOptions(): array
    {
        return [
            new InputOption(
                self::LIMIT,
                null,
                InputOption::VALUE_OPTIONAL,
                'Limit number of orders per run (default = 10)'
            ),
            new InputOption(
                self::FORCE_STORE,
                null,
                InputOption::VALUE_OPTIONAL,
                'Force store code to import orders into'
            ),
            new InputOption(
                self::FORCE_EXISTING,
                null,
                InputOption::VALUE_NONE,
                'Force to re-import existing orders'
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->isOrderImportEnabled()) {
            $output->writeln('<error>Module or order import not enabled.</error>');
            return Cli::RETURN_SUCCESS;
        }

        try {
            $this->state->setAreaCode(Area::AREA_FRONTEND);
            $forceStore = (string)$input->getOption(self::FORCE_STORE);
            $forceExisting = (bool)$input->getOption(self::FORCE_EXISTING);
            $limit = (int)$input->getOption(self::LIMIT);
            $results = $this->orderImportService->execute($limit, $forceStore, $forceExisting);

            if (!empty($results['success'])) {
                foreach ($results['success'] as $result) {
                    $output->writeln(sprintf('<info>%s</info>', $result));
                }
            }

            if (!empty($results['error'])) {
                foreach ($results['error'] as $result) {
                    $output->writeln(sprintf('<error>%s</error>', $result));
                }
            }
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @return bool
     */
    private function isOrderImportEnabled(): bool
    {
        return $this->configProvider->isEnabled() && $this->configProvider->importOrders();
    }
}
