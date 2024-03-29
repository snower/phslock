<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/7/31
 * Time: 下午2:48
 */

namespace Snower\Phslock;

class Database
{
    protected $client = null;
    protected $db_id = 0;

    public function __construct($client, $db_id)
    {
        $this->client = $client;
        $this->db_id = $db_id;
    }

    public function Lock($lock_name, $timeout=5, $expried=10){
        return new Lock($this, $lock_name, $timeout, $expried);
    }

    public function Event($event_name, $timeout=5, $expried=10, $default_seted=true){
        return new Event($this, $event_name, $timeout, $expried, $default_seted);
    }

    public function Semaphore($semaphore_name, $timeout=5, $expried=10, $count=1){
        return new Semaphore($this, $semaphore_name, $timeout, $expried, $count);
    }

    public function RWLock($lock_name, $timeout=5, $expried=10){
        return new RWLock($this, $lock_name, $timeout, $expried);
    }

    public function RLock($lock_name, $timeout=5, $expried=10){
        return new RLock($this, $lock_name, $timeout, $expried);
    }

    public function Command($command) {
        return $this->client->SendCommand($command);
    }

    public function GetDbId(){
        return $this->db_id;
    }
}