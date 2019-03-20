<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 19/3/20
 * Time: 下午4:16
 */

namespace Snower\Phslock;


use Snower\Phslock\Errors\LockUnlockedError;

class Semaphore
{
    protected $db = null;
    protected $db_id = 0;
    protected $semaphore_name = '';
    protected $rlocks = null;
    protected $wlock = null;
    protected $timeout = 0;
    protected $expried = 0;
    protected $count = 1;

    public function __construct($db, $semaphore_name, $timeout=5, $expried=65, $count=1)
    {
        $this->db = $db;
        $this->db_id = $db->GetDbId();
        $this->semaphore_name = $semaphore_name;
        $this->locks = [];
        $this->timeout = $timeout;
        $this->expried = $expried;
        $this->count = $count;
    }

    public function Acquire()
    {
        $lock = new Lock($this->db, $this->semaphore_name, $this->timeout, $this->expried, null, $this->count);
        $lock->Acquire();
        array_push($this->rlocks, $lock);
    }

    public function Release()
    {
        $lock = array_shift($this->rlocks);
        if($lock == null) {
            throw new LockUnlockedError();
        }

        $lock->Release();
    }
}