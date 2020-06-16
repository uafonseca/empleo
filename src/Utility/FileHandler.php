<?php

namespace App\Utility;


use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class FileHandler
{
    private $file;
    private $text;
    private $commandLauncher;

    public function __construct(CommandLauncher $commandLauncher)
    {
        $this->commandLauncher = $commandLauncher;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param $file
     *
     * @return $this
     * @throws FileNotFoundException
     *
     */
    public function setFile($file)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException();
        }

        $this->file = $file;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function preppend()
    {
        return $this->commandLauncher
            ->setCommand("sed -i ':text' :file")
            ->setParameters([
                ':text' => $this->text,
                ':file' => $this->file,
            ])->exec();
    }

    public function append()
    {
        return $this->commandLauncher
            ->setCommand("echo ':text' >> :file;")
            ->setParameters([
                ':text' => $this->text,
                ':file' => $this->file,
            ])->exec();
    }

    public function diff($fileOld, $fileNew, $fileDiff)
    {
        $this->commandLauncher
            ->setCommand('grep -Fxvf :fileold :filenew >> :filediff;')
            ->setParameters([
                ':fileold' => $fileOld,
                ':filenew' => $fileNew,
                ':filediff' => $fileDiff,
            ])->exec();
    }

    public function rename($fileOld, $fileNew)
    {
        $this->commandLauncher
            ->setCommand('mv :fileold :filenew;')
            ->setParameters([
                ':fileold' => $fileOld,
                ':filenew' => $fileNew,
            ])->exec();
    }

    public function delete($file)
    {
        $this->commandLauncher
            ->setCommand('rm :file;')
            ->setParameters([
                ':file' => $file,
            ])->exec();
    }

    public function copy($file, $folder)
    {
        return $this->commandLauncher
            ->setCommand("cp ':file' :folder;")
            ->setParameters([
                ':file' => $file,
                ':folder' => $folder
            ])->exec();
    }
}
