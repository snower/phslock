<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 19/3/20
 * Time: 下午4:16
 */

namespace Snower\Phslock;


use Snower\Phslock\Errors\LockUnlockedError;
use Snower\Phslock\Errors\LockNotOwnError;
use Snower\Phslock\Errors\LockTimeoutError;

class Semaphore
{
    protected $db = null;
    protected $db_id = 0;
    protected $semaphore_name = '';
    protected $timeout = 0;
    protected $expried = 0;
    protected $count = 1;

    public function __construct($db, $semaphore_name, $timeout=5, $expried=65, $count=1)
    {
        $this->db = $db;
        $this->db_id = $db->GetDbId();
        $this->semaphore_name = $semaphore_name;
        $this->timeout = $timeout;
        $this->expried = $expried;
        $this->count = $count;
    }

    public function Acquire()
    {
        $lock = new Lock($this->db, $this->semaphore_name, $this->timeout, $this->expried, null, $this->count);
        $lock->Acquire();
    }

    public function Release($n = 1)
    {
        $lock = new Lock($this->db, $this->semaphore_name, $this->timeout, $this->expried, str_repeat(hex2bin('00'), 16), $this->count);
        for ($i = 0; $i < $n; $i++){
            try {
                $lock->Release();
            } catch (LockNotOwnError $e) {
                return $i + 1;
            } catch (LockUnlockedError $e) {
                return $i + 1;
            }
        }
        return $n;
    }

    public function ReleaseAll()
    {
        $n = 0;
        $lock = new Lock($this->db, $this->semaphore_name, $this->timeout, $this->expried, str_repeat(hex2bin('00'), 16), $this->count);
        while (true){
            try {
                $lock->Release();
            } catch (LockNotOwnError $e) {
                return $n + 1;
            } catch (LockUnlockedError $e) {
                return $n + 1;
            }
            $n++;
        }
        return $n;
    }

    public function Count()
    {
        $lock = new Lock($this->db, $this->semaphore_name, 0, 0, null, $this->count);
        try {
            $lock->Release();
        } catch (LockNotOwnError $e) {
            return empty($e->result) ? 0 : $e->result->lcount;
        } catch (LockUnlockedError $e) {
            return 0;
        } catch (LockTimeoutError $e) {
            return $this->count;
        }
        return 0;
    }
}