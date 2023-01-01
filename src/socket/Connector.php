<?php


namespace Mohamed205\voxum\socket;


use Mohamed205\voxum\Settings;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\Internet;

class Connector
{


    public function __construct(private SocketThread $socketThread, private Settings $settings){}

    public function tcp(string $command, array $data)
    {
        $data = json_encode($data);

        $this->getSocketThread()->sendData($command, $data);
    }

    public function http(string $endpoint, array $data)
    {
        $data = json_encode($data);

        Server::getInstance()->getAsyncPool()->submitTask(new class($endpoint, $data, $this->settings) extends AsyncTask{

            public function __construct(private string $endpoint,
                                        private string $data,
                                        private Settings $settings){}

            public function onRun(): void
            {
                $data = urlencode($this->data);
                $stringedData = "data=$data&roomId=mo&auth=wip";
                Internet::postURL($this->settings->getDomainEndpoint() . $this->endpoint, $stringedData);
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