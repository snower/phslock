<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/7/31
 * Time: 下午2:49
 */

namespace Snower\Phslock;

use Snower\Phslock\Errors\LockLockedError;
use Snower\Phslock\Errors\LockTimeoutError;
use Snower\Phslock\Errors\LockUnknownError;
use Snower\Phslock\Errors\LockUnlockedError;
use Snower\Phslock\Errors\LockUnlockNotOwnError;

class Lock
{
    protected $db = null;
    protected $db_id = 0;
    protected $lock_name = '';
    protected $lock_id = '';
    protected $timeout = 5;
    protected $expried = 10;
    protected $lock = false;
    protected $max_count = 0;

    public function __construct($db, $lock_name, $timeout = 5, $expried = 10, $lock_id = null, $max_count = 1)
    {
        $this->db = $db;
        $this->db_id = $db->GetDbId();
        $this->lock_name = $lock_name;
        $this->timeout = $timeout;
        $this->expried = $expried;
        if (empty($lock_id)) {
            $this->lock_id = static::GenerateLockId();
        } else {
            $this->lock_id = $lock_id;
        }
        $this->max_count = $max_count;

        for($i=strlen($this->lock_name); $i < 16; $i++){
            $this->lock_name .= pack("C", 0);
        }
        for($i=strlen($this->lock_id); $i < 16; $i++){
            $this->lock_id .= pack("C", 0);
        }
    }

    public static function GenerateLockId()
    {
        return hex2bin(uniqid('0')) . openssl_random_pseudo_bytes(9);
    }

    public function Acquire()
    {
        $command = new Command(Command::$COMMAND_TYPE_LOCK, $this->lock_id, $this->db_id, $this->lock_name, $this->timeout, $this->expried, 0, max($this->max_count - 1, 0));
        $result = $this->db->Command($command);
        return $this->CheckResult($result);
    }

    public function Release()
    {
        $command = new Command(Command::$COMMAND_TYPE_UNLOCK, $this->lock_id, $this->db_id, $this->lock_name, $this->timeout, $this->expried, 0, max($this->max_count - 1, 0));
        $result = $this->db->Command($command);
        return $this->CheckResult($result);
    }

    protected function CheckResult($result)
    {
        if ($result->command == Command::$COMMAND_TYPE_LOCK) {
            return $this->CheckLockResult($result);
        } else if ($result->command == Command::$COMMAND_TYPE_UNLOCK) {
            return $this->CheckUnLockResult($result);
        }
        return false;
    }

    protected function CheckLockResult($result){
        if($result->result == Result::$RESULT_SUCCED){
            $this->lock = True;
            return true;
        }

        if($result->result == Result::$RESULT_LOCKED_ERROR){
            throw new LockLockedError();
        }else if($result->result == Result::$RESULT_TIMEOUT){
            throw new LockTimeoutError();
        }else{
            throw new LockUnknownError();
        }
    }

    protected function CheckUnLockResult($result)
    {
        if ($result->result == Result::$RESULT_SUCCED) {
            $this->lock = false;
            return true;
        }

        if ($result->result == Result::$RESULT_UNLOCK_ERROR) {
            throw new LockUnlockedError();
        } else if ($result->result == Result::$RESULT_UNOWN_ERROR) {
            throw new LockUnlockNotOwnError();
        } else {
            throw new LockUnknownError();
        }
    }
}