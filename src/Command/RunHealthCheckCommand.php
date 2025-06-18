<?php

namespace HealthCheckBundle\Command;

use HealthCheckBundle\Service\CheckerService;
use Laminas\Diagnostics\Result\Collection;
use Laminas\Diagnostics\Runner\Reporter\BasicConsole;
use Laminas\Diagnostics\Runner\Runner;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'health-check:run',
    description: '运行所有健康检查',
)]
class RunHealthCheckCommand extends Command
{
    public const NAME = 'health-check:run';
    public function __construct(
        private readonly CheckerService $checkerService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('开始执行健康检查');

        $checkers = $this->checkerService->getCheckers();
        if (empty($checkers)) {
            $io->warning('没有找到可用的健康检查器');
            return Command::SUCCESS;
        }

        $runner = new Runner(reporter: new BasicConsole());
        foreach ($checkers as $checker) {
            $runner->addCheck($checker);
        }

        $results = $runner->run();

        $this->outputSummary($io, $results);

        return $results->getFailureCount() > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function outputSummary(SymfonyStyle $io, Collection $results): void
    {
        $io->section('检查结果统计');

        $rows = [
            ['成功', $results->getSuccessCount()],
            ['失败', $results->getFailureCount()],
            ['警告', $results->getWarningCount()],
            ['跳过', $results->getSkipCount()],
            ['未知', $results->getUnknownCount()],
        ];

        $io->table(
            ['状态', '数量'],
            $rows
        );
    }
}
