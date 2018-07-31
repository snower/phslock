<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/7/31
 * Time: 下午2:49
 */

namespace Snower\Phslock;

use Snower\Phslock\Errors\LockLockedError;
use Snower\Phslock\Errors\LockUnlockedError;
use Snower\Phslock\Errors\LockTimeoutError;
use Snower\Phslock\Errors\EventWaitTimeoutError;

class Event
{
    protected $db = null;
    protected $db_id = 0;
    protected $event_name = '';
    protected $event_id = '';
    protected $event_lock = null;
    protected $check_lock = null;
    protected $wait_lock = null;
    protected $timeout = 0;
    protected $expried = 0;

    public function __construct($db, $event_name, $timeout=5, $expried=65)
    {
        $this->db = $db;
        $this->db_id = $db->GetDbId();
        $this->event_name = $event_name;
        $this->event_id = $event_name;
        $this->timeout = $timeout;
        $this->expried = $expried;
        $this->event_lock = null;
        $this->check_lock = null;
        $this->wait_lock = null;
    }

    public function Clear()
    {
        if($this->event_lock == null) {
            $this->event_lock = new Lock($this->db, $this->event_name, $this->timeout, $this->expried, $this->event_id);
        }
        try {
            $this->event_lock->Acquire();
        }catch(LockLockedError $e){
            return true;
        }
        return true;
    }

    public function Set(){
        if($this->event_lock == null){
            $this->event_lock = new Lock($this->db, $this->event_name, $this->timeout, $this->expried, $this->event_id);
        }
        try{
            $this->event_lock->Release();
        }catch(LockUnlockedError $e){
            return true;
        }
        return true;
    }

    public function GetIsSet(){
        $this->check_lock = new Lock($this->db, $this->event_name, 0, 0);
        try{
            $this->check_lock->Acquire();
        }catch(LockTimeoutError $e) {
            return True;
        }
        return False;
    }

    public function Wait($timeout = 60){
        $this->wait_lock = new Lock($this->db, $this->event_name, $timeout, 0);
        try{
            $this->wait_lock->Acquire();
        }catch(LockTimeoutError $e) {
            throw new EventWaitTimeoutError();
        }
        return True;
    }
}