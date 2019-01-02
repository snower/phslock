<?php
/**
 * Created by PhpStorm.
 * User: snower
 * Date: 18/7/31
 * Time: 下午2:48
 */

namespace Snower\Phslock;

use Snower\Phslock\Errors\ConnectionConnectError;
use Snower\Phslock\Errors\ConnectionClosedError;

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
        $address = @gethostbyname($this->host);

        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$socket) {
            throw new ConnectionConnectError(socket_strerror(socket_last_error()));
        }

        $result = @socket_connect($socket, $address, $this->port);
        if (!$result) {
            throw new ConnectionConnectError(socket_strerror(socket_last_error($socket)));
        }

        @socket_set_option($socket, SOL_SOCKET, TCP_NODELAY, 1);

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

        $data = $command->Dumps();
        $result = socket_write($this->socket, $data, 64);
        if (!$result) {
            $this->Close();
            $this->Open();
            $result = socket_write($this->socket, $data, 64);
            if(!$result){
                throw new ConnectionClosedError();
            }
        }
    }

    public function Read(){
        $data = socket_read($this->socket, 64);
        if (!$data) {
            $this->Close();
            throw new ConnectionClosedError();
        }
        return new Result($data);
    }
}