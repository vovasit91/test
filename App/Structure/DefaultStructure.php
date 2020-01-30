<?php
/**
 * Created by PhpStorm.
 * User: Vvv
 * Date: 28.01.2020
 * Time: 21:07
 */

namespace App\Structure;

use App\StructureInterface;

class DefaultStructure
{
    protected $structure;

    public function __construct(StructureInterface $structure)
    {
        $this->structure = $structure;
    }

    public function getData()
    {
        $data = $this->structure->allWithMetaData();
        $data = array_map(function($key, $value){
            return [
                'id'            => $key + 1,
                'response'      => $value,
                'data'          => $value['data'],
                'metadata'      => $value['metadata'],
                'received_at'   => $value['metadata']['created_at'],
            ];
        }, array_keys($data), $data);
        return $data;
    }
}