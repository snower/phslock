<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/7/31
 * Time: 下午2:49
 */

namespace Snower\Phslock;

class Command
{
    public static $MAGIC = 0x56;
    public static $VERSION = 0x01;
    public static $COMMAND_TYPE_LOCK = 1;
    public static $COMMAND_TYPE_UNLOCK = 2;

    public $request_id = null;
    public $command = 0;
    public $flag = 0;
    public $lock_id = '';
    public $db_id = 0;
    public $lock_name = '';
    public $timeout = 5;
    public $expried = 10;
    public $count = 0;

    public function __construct($command, $lock_id, $db_id, $lock_name='', $timeout=0, $expried=0, $flag=0, $count=0)
    {
        $this->request_id = $this->Generate();
        $this->command = $command;
        $this->flag = $flag;
        $this->lock_id = $lock_id;
        $this->db_id = $db_id;
        $this->lock_name = $lock_name;
        $this->timeout = $timeout;
        $this->expried = $expried;
        $this->count = $count;
    }

    public static function Generate()
    {
        return hex2bin(uniqid('0')) . openssl_random_pseudo_bytes(9);
    }

    public function Dumps()
    {
        return pack("CCC", static::$MAGIC, static::$VERSION, $this->command) . $this->request_id .
            pack("CC", $this->flag, $this->db_id) . $this->lock_id . $this->lock_name .
            pack("VVvC", $this->timeout, $this->expried, $this->count, 0x00);
    }
}