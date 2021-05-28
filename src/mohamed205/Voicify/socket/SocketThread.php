<?php


namespace mohamed205\Voicify\socket;


use pocketmine\Thread;

class SocketThread extends Thread
{

    private $socket;
    private bool $isRunning = true;

    public function run()
    {

        $config = [
            "host" => "159.65.204.125",
            "port" => 3456,
        ];

        set_time_limit(0);

        $this->socket = $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        $result = socket_connect($socket, $config["host"], $config["port"]) or die("Could not connect to server\n");

        while($this->isRunning)
        {

        }

    }

    public function sendData(string $data)
    {
        socket_write($this->socket, $data, strlen($data)) or die("Could not send data to server\n");
    }

    public function stop()
    {
        $this->isRunning = false;
    }


}