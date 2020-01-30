<?php


namespace App\Object;


class User
{

    public $public;

    protected $protected;

    public function __construct()
    {
        try{
            $this->public = bin2hex(random_bytes(5));
            $this->protected = bin2hex(random_bytes(5));
        } catch (\Exception $exception) {
            $this->public = 123456;
            $this->protected = 789101;
        }
    }

}