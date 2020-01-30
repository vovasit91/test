<?php


namespace App;


interface StructureInterface
{
    public function all() : array;
    public function allWithMetaData() : array;
}