<?php
declare(strict_types=1);

namespace SlartyBartfast;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HashCommand extends Command
{
    const EMPTY_HASH = 'e69de29bb2d1d6434b8b29ae775ad8c2e48c5391';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $root        = $input->getArgument('root');
        $directories = $input->getArgument('directories');
        $io          = new SymfonyStyle($input, $output);

        $hasher = new DirectoryHasher($root, $directories);

        try {
            $hash = $hasher->getHash();
        } catch (\InvalidArgumentException $exception) {
            $io->error(explode("\n", $exception->getMessage()));
            return 3;
        }

        $io->text($hash);

        if ($hash === self::EMPTY_HASH) {
            $output->writeln('Empty output');
            return 2;
        }

        return 0;
    }

    protected function configure()
    {
        $this->setName('hash')
            ->setDescription('Get directory hash')
            ->setHelp('Allows you to get a hash for directory')
            ->addArgument(
                'root',
                InputArgument::REQUIRED,
                'Directory Root to run command from'
            )
            ->addArgument(
                'directories',
                InputArgument::REQUIRED | InputArgument::IS_ARRAY,
                'Directories to include in hash calculation'
            );
    }
}
