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
    protected $timeout = 0;
    protected $expried = 0;
    protected $lock = null;
    protected $lock_count = 0;

    public function __construct($db, $lock_name, $timeout=5, $expried=65, $count=1)
    {
        $this->db = $db;
        $this->db_id = $db->GetDbId();
        $this->lock_name = $lock_name;
        $this->timeout = $timeout;
        $this->expried = $expried;

        $this->lock = new Lock($this->db, $this->lock_name, $this->timeout, $this->expried, null, 1, 0xff);
        $this->lock_count = 0;
    }

    public function Acquire()
    {
        if($this->lock_count >= 0xff) {
            throw new LockLockedError();
        }

        $this->lock->Acquire();
        $this->lock_count++;
    }

    public function Release()
    {
        if($this->lock_count == 0) {
            throw new LockUnlockedError();
        }

        $this->lock->Release();
    }
}