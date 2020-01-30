<?php

namespace App\Storage;

use App\StorageInterface;
use App\StructureInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileStorage implements StorageInterface, StructureInterface
{

    private $path = MAIN_DIR . DIRECTORY_SEPARATOR . 'storage';

    private $fs;

    private $suffix = '.json';

    private $queueMetaData;

    private $storage;

    private $queue;

    public function __construct()
    {
        $this->fs = new Filesystem();
        $this->setQueue('main');
    }

    public function track(array $data, array $metadata = []) : void
    {
        try {
            $this->storage[count($this->storage)] = $data;
            $this->fs->dumpFile($this->getStorageFilePath(), json_encode($this->storage));

            $this->queueMetaData[count($this->queueMetaData)] = $metadata;
            $this->fs->dumpFile($this->getMetaDataFilePath(), json_encode($this->queueMetaData));
        } catch (\Exception $exception) {

        }
    }

    public function flush() : void
    {
        $this->fs->remove($this->getPath());
        $this->storage          = $this->getStorage();
        $this->queueMetaData    = $this->getMetaData();
    }

    public function all() : array
    {
        return $this->storage;
    }

    public function allWithMetaData() : array
    {
        return array_map(function($data, $meta){
            return ['data' => $data, 'metadata' => $meta];
        }, $this->storage, $this->queueMetaData);
    }

    public function setQueue(string $name) : void
    {
        $this->queue            = $name;
        $this->storage          = $this->getStorage();
        $this->queueMetaData    = $this->getMetaData();
    }

    private function getPath() : string
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->queue;
    }

    private function getStorage() : array
    {
        if(!$this->fs->exists($this->getStorageFilePath())){
            $this->fs->dumpFile($this->getStorageFilePath(), json_encode([]));
        }
        return json_decode(file_get_contents($this->getStorageFilePath()), true);
    }

    private function getMetaData() : array
    {
        if(!$this->fs->exists($this->getMetaDataFilePath())){
            $this->fs->dumpFile($this->getMetaDataFilePath(), json_encode([]));
        }
        return json_decode(file_get_contents($this->getMetaDataFilePath()), true);
    }

    private function getStorageFilePath() : string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . 'data' . $this->suffix;
    }

    private function getMetaDataFilePath() : string
    {
        return $this->getPath() . DIRECTORY_SEPARATOR . 'metadata' . $this->suffix;
    }
}