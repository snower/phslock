<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/7/31
 * Time: 下午2:50
 */

namespace Snower\Phslock;

class Result
{
    public static $RESULT_SUCCED = 0;
    public static $RESULT_UNKNOWN_MAGIC = 1;
    public static $RESULTD_UNKNOWN_VERSION = 2;
    public static $RESULT_UNKNOWN_DB = 3;
    public static $RESULTD_UNKNOWN_COMMAND = 4;
    public static $RESULT_LOCKED_ERROR = 5;
    public static $RESULT_UNLOCK_ERROR = 6;
    public static $RESULT_UNOWN_ERROR = 7;
    public static $RESULT_TIMEOUT = 8;
    public static $RESULT_EXPRIED = 9;
    public static $RESULT_ERROR = 10;

    public $magic = 0;
    public $version = 0;
    public $command = 0;
    public $request_id = '';
    public $result = 0;
    public $flag = 0;
    public $db_id = 0;
    public $lock_id = '';
    public $lock_name = '';

    public function __construct($data)
    {
        $this->magic = ord($data[0]);
        $this->version = ord($data[1]);
        $this->command = ord($data[2]);
        $this->request_id = substr($data, 3, 16);
        $this->result = ord($data[19]);
        $this->flag = ord($data[20]);
        $this->db_id = ord($data[21]);
        $this->lock_id = substr($data, 11, 16);
        $this->lock_name = substr($data, 38, 16);
    }
}