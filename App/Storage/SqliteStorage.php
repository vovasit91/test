<?php

namespace App\Storage;

use App\StorageInterface;
use App\StructureInterface;

class SqliteStorage implements StorageInterface, StructureInterface
{
    private $db = MAIN_DIR . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'db.sqlite';

    private $pdo;

    public function __construct()
    {
        $this->pdo = new \PDO("sqlite:" . $this->db);
        $this->init();
    }

    protected function init()
    {
        $this->pdo->query("CREATE TABLE IF NOT EXISTS storage (id integer PRIMARY KEY, data text, metadata text);");
    }

    public function track(array $data, array $metadata = []) : void
    {
        $sql = 'INSERT INTO "storage" ("data", "metadata") VALUES (:data, :metadata)';
        $statement = $this->pdo->prepare($sql);
        $data = json_encode($data);
        $metadata = json_encode($metadata);
        $statement->bindParam(':data', $data);
        $statement->bindParam(':metadata', $metadata);
        $statement->execute();
    }

    public function flush() : void
    {
        $sql = "DROP TABLE IF EXISTS storage"; //sqlite has no truncate so we recreating the table.
        $this->pdo->query($sql);
        $this->init();
    }

    public function all() : array
    {
        $sql = "SELECT data FROM storage";
        $data = $this->pdo->query($sql)->fetchAll();
        foreach ($data as &$datum) {
            $datum['data'] = json_decode($datum['data'], true);
        }
        return $data;
    }

    public function allWithMetaData() : array
    {
        $sql = "SELECT data, metadata FROM storage";
        $data = $this->pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($data as &$datum) {
            $datum['data'] = json_decode($datum['data'], true);
            $datum['metadata'] = json_decode($datum['metadata'], true);
        }
        return $data;
    }
}