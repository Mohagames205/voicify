<?php


namespace mohamed205\Voicify\socket;


use AttachableThreadedLogger;
use ErrorException;
use pocketmine\thread\Thread;

class SocketThread extends Thread
{

    private $socket;
    private bool $isRunning = true;
    private AttachableThreadedLogger $logger;
    private int $reconnectAttempts = 0;

    private $config = [
        "host" => "159.65.204.125",
        "port" => 3456,
    ];

    private $confi = [
        "host" => "localhost",
        "port" => 8080,
    ];

    public function __construct(AttachableThreadedLogger $attachableLogger)
    {
        $this->logger = $attachableLogger;

    }

    public function onRun(): void
    {
        set_time_limit(0);

        $this->socket = $socket = socket_create(AF_INET, SOCK_STREAM, 0) or $this->logger->error("Could not create socket\n");
        $result = socket_connect($socket, $this->config["host"], $this->config["port"]) or $this->logger->error("Could not connect to server\n");

        while ($this->isRunning) {

        }

    }

    public function sendData(string $command, string $data)
    {
        if(!$this->isRunning) return;
        try {
            $dataArray = json_decode($data);
            $commandArray = json_encode(["command" => $command, "data" => $dataArray, "auth" => "wip"]) . ";";
            socket_write($this->socket, $commandArray, strlen($commandArray)) or $this->logger->error("Could not send data to server\n");
        } catch (ErrorException $exception) {
            $this->reconnect();
            return;
        }

    }


    private function reconnect()
    {

        try {
            $this->socket = $socket = socket_create(AF_INET, SOCK_STREAM, 0) or $this->logger->error("Could not create socket\n");
            socket_connect($socket, $this->config["host"], $this->config["port"]) or $this->logger->error("Could not connect to server\n");
            $this->logger->alert("Succesfully reconnected to the socket!");
        } catch (ErrorException $exception) {
            if ($this->reconnectAttempts < 5) {
                $this->logger->error($exception->getMessage() . "\nAttempting to reconnect to the socket...");
                $this->reconnectAttempts++;
                $this->reconnect();
            } else {
                if($this->isRunning) $this->stop();
            }
        }
    }

    public function stop()
    {
        socket_close($this->socket);
        $this->isRunning = false;
        $this->quit();
    }


}