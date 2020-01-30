<?php

namespace App;

class EventHandler
{
    public function track(object $object) : void
    {
        Application::getStorage()->track(Serializer::objectToArray($object), [
            'created_at' => time(),
            'remote_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'class' => get_class($object)
        ]);
    }

    public function flush() : void
    {
        $this->sendAll();
        Application::getStorage()->flush();
    }

    protected function sendAll()
    {
        $data = Application::getStorage()->all();
        $client = new Client(['method' => 'post']);
        $client->setUrl('https://google.com');
        $client->setData($data);
        return $client->sendAsJson();
    }

}