<?php
declare(strict_types=1);

namespace SlartyBartfast\Services;

use League\Flysystem\AdapterInterface;
use SlartyBartfast\Model\ApplicationModel;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppBuilder
{
    /** @var ApplicationModel */
    private $application;
    /** @var bool */
    private $force;
    /** @var AdapterInterface */
    private $filesystem;
    /** @var bool */
    private $shouldBuild;

    /**
     * AppBuilder constructor.
     *
     * @param ApplicationModel $application
     * @param AdapterInterface $filesystem
     * @param bool             $force
     */
    public function __construct(
        ApplicationModel $application,
        AdapterInterface $filesystem,
        bool $force
    ) {
        $this->application = $application;
        $this->force       = $force;
        $this->filesystem  = $filesystem;
    }

    public function getApplicationName(): string
    {
        return $this->application->getName();
    }

    public function shouldBuild(): bool
    {
        if ($this->force) {
            return true;
        }

        if ($this->shouldBuild !== null) {
            return $this->shouldBuild;
        }

        $buildFinder = new BuildFinder($this->application, $this->filesystem);

        // cache this since we may need to figure out if a build is needed
        // a few times
        $this->shouldBuild = $buildFinder->isBuildNeeded();
        return $this->shouldBuild;
    }

    public function doBuild($input, $output)
    {
        $currentDirectory = getcwd();
        $io = new SymfonyStyle($input, $output);
        $io->section(
            'Beginning build for ' . $this->getApplicationName() . ' application'
        );

        $command = $this->application->getBuildCommand();

        $io->writeln($command);

        chdir($this->application->getRoot());

        exec($command, $output, $exitCode);

        $io->writeln($output);

        if ($exitCode !== 0) {
            $io->error(
                [
                    'BUILD FAILURE ON '. $this->getApplicationName(),
                    'Build exited with code ' . $exitCode
                ]
            );
            chdir($currentDirectory);
            return false;
        }

        $io->text('Build succeeded for ' . $this->getApplicationName());

        chdir($currentDirectory);

        return true;
    }

    public function getApplication(): ApplicationModel
    {
        return $this->application;
    }
}
