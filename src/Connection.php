<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/7/31
 * Time: 下午2:48
 */

namespace Snower\Phslock;

use Snower\Phslock\Errors\ConnectionConnectError;

class Connection
{

    protected $host = "127.0.0.1";
    protected $port = 5658;
    protected $socket = null;

    public function __construct($host = "127.0.0.1", $port = 5658)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function Open(){
        $address = gethostbyname($this->port);

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            throw new ConnectionConnectError(socket_strerror(socket_last_error()));
        }

        $result = socket_connect($socket, $address, $this->port);
        if ($result === false) {
            throw new ConnectionConnectError(socket_strerror(socket_last_error($socket)));
        }

        $this->socket = $socket;
        return true;
    }

    public function Close() {
        if($this->socket) {
            socket_close($this->socket);
            $this->socket = null;
        }
    }

    public function Write($command){
        if($this->socket == null) {
            $this->Open();
        }

        $command = $command.Dumps();
        socket_write($this->socket, $command, 64);
    }

    public function Read(){
        $data = socket_read($this->socket, 64);
        return new Result($data);
    }
}