<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/7/31
 * Time: 下午4:01
 */

namespace Snower\Phslock\Errors;


class LockException extends PhSlockException
{
    public $result;

    public function __construct($result = null, $message = "", $code = 0) {
        parent::__construct($message, $code);

        $this->result = $result;
    }
}