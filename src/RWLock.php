<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 19/3/20
 * Time: 下午4:17
 */

namespace Snower\Phslock;


use Snower\Phslock\Errors\LockUnlockedError;

class RWLock
{
    protected $db = null;
    protected $db_id = 0;
    protected $lock_name = '';
    protected $rlocks = null;
    protected $wlock = null;
    protected $timeout = 0;
    protected $expried = 0;

    public function __construct($db, $lock_name, $timeout=5, $expried=65)
    {
        $this->db = $db;
        $this->db_id = $db->GetDbId();
        $this->lock_name = $lock_name;
        $this->rlocks = [];
        $this->wlock = null;
        $this->timeout = $timeout;
        $this->expried = $expried;
    }

    public function RAcquire()
    {
        $lock = new Lock($this->db, $this->lock_name, $this->timeout, $this->expried, null,0x10000);
        $lock->Acquire();
        array_push($this->rlocks, $lock);
    }

    public function RRelease()
    {
        $lock = array_shift($this->rlocks);
        if($lock == null) {
            throw new LockUnlockedError();
        }

        $lock->Release();
    }

    public function Acquire()
    {
        if($this->wlock == null) {
            $this->wlock = new Lock($this->db, $this->lock_name, $this->timeout, $this->expried, null,1);
        }
        $this->wlock->Acquire();
    }

    public function Release()
    {
        if($this->wlock == null) {
            throw new LockUnlockedError();
        }

        $this->wlock->Release();
    }
}