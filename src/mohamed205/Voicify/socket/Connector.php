<?php


namespace mohamed205\Voicify\socket;


use mohamed205\Voicify\Voicify;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class Connector
{

    private SocketThread $socketThread;

    public function __construct(SocketThread $socketThread)
    {
        $this->socketThread = $socketThread;
    }

    public function tcp(string $command, array $data)
    {
        $data = json_encode($data);

        $this->getSocketThread()->sendData($command, $data);
    }

    public function http(string $endpoint, array $data)
    {
        $data = json_encode($data);

        Server::getInstance()->getAsyncPool()->submitTask(new class($endpoint, $data) extends AsyncTask{

            private string $data;
            private string $endpoint;

            public function __construct(string $command, string $data)
            {
                $this->data = $data;
                $this->endpoint = $command;
            }

            public function onRun(): void
            {
                $data = urlencode($this->data);
                $stringedData = "data=$data&roomId=mo&auth=wip";
                Internet::postURL("https://voxum.mootje.be" . $this->endpoint, $stringedData);
            }
        });
    }

    /**
     * @return SocketThread
     */
    public function getSocketThread(): SocketThread
    {
        return $this->socketThread;
    }



}