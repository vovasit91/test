<?php


namespace App;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;

class Client
{
    private $method = 'get';
    private $url;
    private $data;

    public function __construct(array $config = [])
    {
        if(isset($config['method']) && in_array(strtolower($config['method']), ['GET', 'POST'])){
            $this->method = strtoupper($config['method']);
        }
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function sendAsJson()
    {
        $responses = [
            new Response(200, [], json_encode(['success' => true])),
            new Response(400, [], json_encode(['success' => false])),
        ];
        shuffle($responses);
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handlerStack]);
        try {
            $response = $client->request($this->method, $this->url, ['body' => \GuzzleHttp\json_encode($this->data)]);
            return $response->getBody()->getContents();
        } catch (ClientException $exception) {
            return $exception->getResponse()->getBody()->getContents();
        }
    }
}