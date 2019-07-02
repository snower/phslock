<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/7/31
 * Time: 下午2:48
 */

namespace Snower\Phslock;

class Client
{
    protected $connecton = null;
    protected $dbs = [];

    public function __construct($host = "127.0.0.1", $port = 5658)
    {
        $this->connecton = new Connection($host, $port);
    }

    public function Lock($lock_name, $timeout=5, $expried=10){
        return $this->SelectDB(0)->Lock($lock_name, $timeout, $expried);
    }

    public function Event($event_name, $timeout=5, $expried=10){
        return $this->SelectDB(0)->Event($event_name, $timeout, $expried);
    }

    public function CycleEvent($event_name, $timeout=5, $expried=10){
        return $this->SelectDB(0)->CycleEvent($event_name, $timeout, $expried);
    }

    public function Semaphore($semaphore_name, $timeout=5, $expried=10, $count=1){
        return $this->SelectDB(0)->Semaphore($semaphore_name, $timeout, $expried, $count);
    }

    public function RWLock($lock_name, $timeout=5, $expried=10){
        return $this->SelectDB(0)->RWLock($lock_name, $timeout, $expried);
    }

    public function RLock($lock_name, $timeout=5, $expried=10){
        return $this->SelectDB(0)->RLock($lock_name, $timeout, $expried);
    }

    public function SelectDB($db){
        if(!isset($this->dbs[$db])){
            $this->dbs[$db] = new Database($this, $db);
        }
        return $this->dbs[$db];
    }

    public function SendCommand($command){
        $this->connecton->Write($command);
        do{
            $result = $this->connecton->Read();
        } while($result->db_id != $command->db_id || $result->request_id != $command->request_id);
        return $result;
    }
}