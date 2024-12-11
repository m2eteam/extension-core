<?php

declare(strict_types=1);

namespace M2E\Core\Model\VariablesDir;

class Adapter
{
    private \Magento\Framework\Filesystem\DriverInterface $fileDriver;
    private ?string $childFolder;
    private string $pathVariablesDirBase;
    private string $pathVariablesDirChildFolder;

    public function __construct(
        string $extensionNameBaseFolder,
        \Magento\Framework\Filesystem\DriverPool $driverPool,
        \Magento\Framework\Filesystem $filesystem,
        ?string $childFolder = null
    ) {
        $this->fileDriver = $driverPool->getDriver(\Magento\Framework\Filesystem\DriverPool::FILE);

        if (empty($childFolder)) {
            $childFolder = null;
        }

        $this->childFolder = $childFolder;

        $varDir = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        $this->pathVariablesDirBase = $varDir->getAbsolutePath() . $extensionNameBaseFolder;

        if ($childFolder !== null) {
            if ($childFolder[0] !== DIRECTORY_SEPARATOR) {
                $childFolder = DIRECTORY_SEPARATOR . $childFolder;
            }
            if ($childFolder[strlen($childFolder) - 1] !== DIRECTORY_SEPARATOR) {
                $childFolder .= DIRECTORY_SEPARATOR;
            }

            $this->pathVariablesDirChildFolder = $this->pathVariablesDirBase . $childFolder;
            $this->pathVariablesDirBase .= DIRECTORY_SEPARATOR;
            $this->childFolder = $childFolder;
        } else {
            $this->pathVariablesDirBase .= DIRECTORY_SEPARATOR;
            $this->pathVariablesDirChildFolder = $this->pathVariablesDirBase;
            $this->childFolder = '';
        }

        $this->pathVariablesDirBase = str_replace(
            ['/', '\\'],
            DIRECTORY_SEPARATOR,
            $this->pathVariablesDirBase
        );
        $this->pathVariablesDirChildFolder = str_replace(
            ['/', '\\'],
            DIRECTORY_SEPARATOR,
            $this->pathVariablesDirChildFolder
        );
        $this->childFolder = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $this->childFolder);
    }

    public function getBasePath(): string
    {
        return $this->pathVariablesDirBase;
    }

    public function getPath(): string
    {
        return $this->pathVariablesDirChildFolder;
    }

    // ---------------------------------------

    public function isBaseExist(): bool
    {
        return (bool)$this->fileDriver->isDirectory($this->getBasePath());
    }

    public function isExist(): bool
    {
        return (bool)$this->fileDriver->isDirectory($this->getPath());
    }

    // ---------------------------------------

    public function createBase(): void
    {
        if ($this->isBaseExist()) {
            return;
        }

        $this->fileDriver->createDirectory($this->getBasePath(), 0777);
    }

    public function create(): void
    {
        if ($this->isExist()) {
            return;
        }

        $this->createBase();

        if ($this->childFolder !== null) {
            $tempPath = $this->getBasePath();
            $tempChildFolders = explode(
                DIRECTORY_SEPARATOR,
                substr($this->childFolder, 1, strlen($this->childFolder) - 2)
            );

            foreach ($tempChildFolders as $key => $value) {
                if (!$this->fileDriver->isDirectory($tempPath . $value . DIRECTORY_SEPARATOR)) {
                    $this->fileDriver->createDirectory($tempPath . $value . DIRECTORY_SEPARATOR, 0777);
                }
                $tempPath = $tempPath . $value . DIRECTORY_SEPARATOR;
            }
        } else {
            $this->fileDriver->createDirectory($this->getPath(), 0777);
        }
    }

    // ---------------------------------------

    public function removeBase(): void
    {
        if (!$this->isBaseExist()) {
            return;
        }

        $this->fileDriver->deleteDirectory($this->getBasePath());
    }

    public function removeBaseForce(): void
    {
        if (!$this->isBaseExist()) {
            return;
        }

        $directoryIterator = new \RecursiveDirectoryIterator($this->getBasePath(), \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $path) {
            $path->isFile()
                ? $this->fileDriver->deleteFile($path->getPathname())
                : $this->fileDriver->deleteDirectory($path->getPathname());
        }

        $this->fileDriver->deleteDirectory($this->getBasePath());
    }

    public function remove(): void
    {
        if (!$this->isExist()) {
            return;
        }

        $this->fileDriver->deleteDirectory($this->getPath());
    }
}
