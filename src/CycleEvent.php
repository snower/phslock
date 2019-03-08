<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 19/3/8
 * Time: 下午2:30
 */

namespace Snower\Phslock;


use Snower\Phslock\Errors\PhSlockException;
use Snower\Phslock\Errors\LockTimeoutError;
use Snower\Phslock\Errors\LockLockedError;
use Snower\Phslock\Errors\EventWaitTimeoutError;

class CycleEvent extends Event
{
    public function Wait($timeout = 60){
        $this->wait_lock = new Lock($this->db, $this->event_name, $timeout, 0);
        try{
            $this->wait_lock->Acquire();
        }catch(LockTimeoutError $e) {
            if($this->event_lock == null) {
                $this->event_lock = new Lock($this->db, $this->event_name, $this->timeout, $this->expried, $this->event_id);
            }
            try {
                $this->event_lock->Acquire(0x02);
            }catch(LockLockedError $e){
                throw new EventWaitTimeoutError();
            }

            try{
                $this->event_lock->Release();
            } catch (PhSlockException $e) {
                return true;
            }
        }
        return True;
    }
}