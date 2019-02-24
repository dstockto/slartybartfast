<?php
declare(strict_types=1);

namespace SlartyBartfast;

class DirectoryHasher
{
    const EMPTY_HASH = 'e69de29bb2d1d6434b8b29ae775ad8c2e48c5391';

    private $root;
    private $directories = [];

    /**
     * DirectoryHasher constructor.
     *
     * @param       $root
     * @param array $directories
     */
    public function __construct($root, array $directories)
    {
        $this->root        = $root;
        $this->directories = $directories;
    }

    public function getHash(): string
    {
        $currentDirectory = getcwd();

        if (!file_exists($this->root) || !is_dir($this->root)) {
            throw new \InvalidArgumentException(
                'Provided root directory does not exist'
            );
        }

        chdir($this->root);

        $invalidDirectories = array_reduce(
            $this->directories,
            function (array $invalid, string $directory) {
                if (file_exists($directory)) {
                    return $invalid;
                }

                $invalid[] = $this->root . '/' . $directory;
                return $invalid;
            },
            []
        );

        if (!empty($invalidDirectories)) {
            throw new \InvalidArgumentException(
                "Invalid directories or files provided:\n" .
                implode("\n", $invalidDirectories)
            );
        }

        $directories = array_map('escapeshellarg', $this->directories);

        $directoriesString = implode(' ', $directories);

        $cmd    = "git ls-files -s $directoriesString | git hash-object --stdin";
        $result = trim(shell_exec($cmd));
        chdir($currentDirectory);
        if ($result === self::EMPTY_HASH) {
            throw new \RuntimeException(
                "Provided directories are empty - check capitalization\n"
                . $directoriesString
            );
        }

        return $result;
    }
}
