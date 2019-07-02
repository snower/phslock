<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 2019/7/2
 * Time: 10:48 AM
 */

namespace Snower\Phslock;


use Snower\Phslock\Errors\LockLockedError;
use Snower\Phslock\Errors\LockUnlockedError;

class RLock
{
    protected $db = null;
    protected $db_id = 0;
    protected $lock_name = '';
    protected $locks = null;
    protected $timeout = 0;
    protected $expried = 0;

    public function __construct($db, $lock_name, $timeout=5, $expried=65, $count=1)
    {
        $this->db = $db;
        $this->db_id = $db->GetDbId();
        $this->lock_name = $lock_name;
        $this->locks = [];
        $this->timeout = $timeout;
        $this->expried = $expried;
    }

    public function Acquire()
    {
        if(count($this->locks) >= 0xff) {
            throw new LockLockedError();
        }

        $lock = new Lock($this->db, $this->lock_name, $this->timeout, $this->expried, null, 1, 0xff);
        $lock->Acquire();
        array_push($this->locks, $lock);
    }

    public function Release()
    {
        $lock = array_pop($this->locks);
        if($lock == null) {
            throw new LockUnlockedError();
        }

        $lock->Release();
    }
}