<?php

namespace mohamed205\Voicify;

class Settings
{

    public function __construct(
        private string $domainEndpoint,
        private string $ip,
        private int $port,
        private string $socketPassword,
        private string $apiPassword
    ){}

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getSocketPassword(): string
    {
        return $this->socketPassword;
    }

    /**
     * @return string
     */
    public function getApiPassword(): string
    {
        return $this->apiPassword;
    }

    /**
     * @return string
     */
    public function getDomainEndpoint(): string
    {
        return $this->domainEndpoint;
    }

}