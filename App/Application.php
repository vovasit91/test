<?php
/**
 * Created by PhpStorm.
 * User: Vvv
 * Date: 28.01.2020
 * Time: 20:33
 */

namespace App;

use App\Storage\FileStorage;
use App\Storage\SqliteStorage;
use App\Structure\DefaultStructure;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Finder\Finder;

class Application
{
    /** @var StorageInterface|StructureInterface */
    protected static $storage;

    public function __construct()
    {
        (\Dotenv\Dotenv::createImmutable(dirname(__DIR__)))->load();
        switch($_ENV['storage_type']) {
            case 'sqlite': self::$storage = new SqliteStorage(); break;
            default: self::$storage = new FileStorage(); self::$storage->setQueue($_ENV['storage_queue'] ?? 'main'); break;
        }
    }

    public static function getStorage() : StorageInterface
    {
        return self::$storage;
    }

    public static function getStructure() : StructureInterface
    {
        return self::$storage;
    }

    public function run() : void
    {
        $payload = $this->getPayload(); //Axios package sends requests directly into php://input to allow casting data types withing javascript
        if(!empty($payload)){
            switch ($payload['action'] ?? 'index') {
                case 'built-in'      : $this->submissionBuiltInModel(); break;
                case 'object'        : $this->submissionObject();       break;
                case 'flush'         : $this->flush();                  break;
                default              : $this->methodNotAllowed();
            }
        }
        else{
            switch ($_GET['action'] ?? 'index') {
                case 'getModels'    : $this->actionGetModels(); break; //returns to browser only allowed to submit models
                case 'index'        :
                default             : $this->actionIndex();
            }
        }
    }

    /**
     * @return array|null
     */
    public function getPayload()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function actionIndex() : void
    {
        switch ($_GET['structure'] ?? 'default'){
            case 'default'  :
            default         : $structure = new DefaultStructure(Application::getStructure());
        }
        $this->asJson($structure->getData());
    }

    public function asJson($data) : void
    {
        \Http\Response\send(
            new Response(
                200,
                [
                    'Content-Type' => 'application/json',
                    'Access-Control-Allow-Origin' => '*',
                    'Access-Control-Allow-Methods' => 'POST,GET',
                    'Access-Control-Allow-Headers' => 'Access-Control-Allow-Headers, Access-Control-Allow-Origin, Accept, X-Requested-With, Content-Type, Access-Control-Request-Methods',
                ],
                \GuzzleHttp\json_encode($data)
            )
        );
    }

    public function methodNotAllowed() : void
    {
        $this->asJson(['success' => false, 'message' => 'Method not allowed']);
    }

    public function actionGetModels() : void
    {
        $this->asJson($this->getAllowedClasses());
    }

    public function submissionBuiltInModel() : void
    {
        $payload = $this->getPayload();
        $data = $payload['data'];
        if(class_exists($data['model']) && in_array($data['model'], $this->getAllowedClasses())){
            for ($i = 0; $i < $data['quantity'] ?? 0; $i++)
                (new EventHandler())->track(new $data['model']);
            $this->actionIndex();
        }
    }

    public function submissionObject() : void
    {
        $payload = $this->getPayload();
        $data = $payload['data'];
        if($data['quantity'] ?? 0){
            $model = new \stdClass();
            foreach ($data['properties'] as $property) {
                $model->{$property['key']} = $property['value'];
            }
            for ($i = 0; $i < $data['quantity']; $i++)
                (new EventHandler())->track($model);
            $this->actionIndex();
        }
    }

    public function getAllowedClasses() : array
    {
        $finder = new Finder();
        $finder->files()->in(MAIN_DIR . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Object');
        $classes = [];
        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $class = Serializer::extractClassName($file->getRealPath());
                if($class)
                    $classes[] = $class;
            }
        }
        return $classes;
    }

    public function flush() : void
    {
        $handler = new EventHandler();
        $handler->flush();
        $this->actionIndex();
    }
}