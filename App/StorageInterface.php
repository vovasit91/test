<?php


namespace App;


interface StorageInterface
{
    public function track(array $data, array $metadata = []) : void;
    public function flush() : void;
    public function all() : array;
}