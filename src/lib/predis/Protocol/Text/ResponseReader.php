<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Protocol\Text;

use Mohamed205\voxum\lib\predis\CommunicationException;
use Mohamed205\voxum\lib\predis\Connection\CompositeConnectionInterface;
use Mohamed205\voxum\lib\predis\Protocol\ProtocolException;
use Mohamed205\voxum\lib\predis\Protocol\ResponseReaderInterface;

/**
 * Response reader for the standard Redis wire protocol.
 *
 * @link http://redis.io/topics/protocol
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseReader implements ResponseReaderInterface
{
    protected $handlers;

    /**
     *
     */
    public function __construct()
    {
        $this->handlers = $this->getDefaultHandlers();
    }

    /**
     * Returns the default handlers for the supported type of responses.
     *
     * @return array
     */
    protected function getDefaultHandlers()
    {
        return array(
            '+' => new \Mohamed205\voxum\lib\predis\Protocol\Text\Handler\StatusResponse(),
            '-' => new \Mohamed205\voxum\lib\predis\Protocol\Text\Handler\ErrorResponse(),
            ':' => new \Mohamed205\voxum\lib\predis\Protocol\Text\Handler\IntegerResponse(),
            '$' => new \Mohamed205\voxum\lib\predis\Protocol\Text\Handler\BulkResponse(),
            '*' => new \Mohamed205\voxum\lib\predis\Protocol\Text\Handler\MultiBulkResponse(),
        );
    }

    /**
     * Sets the handler for the specified prefix identifying the response type.
     *
     * @param string                           $prefix  Identifier of the type of response.
     * @param \Mohamed205\voxum\lib\predis\Protocol\Text\Handler\ResponseHandlerInterface $handler Response handler.
     */
    public function setHandler($prefix, \Mohamed205\voxum\lib\predis\Protocol\Text\Handler\ResponseHandlerInterface $handler)
    {
        $this->handlers[$prefix] = $handler;
    }

    /**
     * Returns the response handler associated to a certain type of response.
     *
     * @param string $prefix Identifier of the type of response.
     *
     * @return \Mohamed205\voxum\lib\predis\Protocol\Text\Handler\ResponseHandlerInterface
     */
    public function getHandler($prefix)
    {
        if (isset($this->handlers[$prefix])) {
            return $this->handlers[$prefix];
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function read(CompositeConnectionInterface $connection)
    {
        $header = $connection->readLine();

        if ($header === '') {
            $this->onProtocolError($connection, 'Unexpected empty reponse header');
        }

        $prefix = $header[0];

        if (!isset($this->handlers[$prefix])) {
            $this->onProtocolError($connection, "Unknown response prefix: '$prefix'");
        }

        return $this->handlers[$prefix]->handle($connection, substr($header, 1));
    }

    /**
     * Handles protocol errors generated while reading responses from a
     * connection.
     *
     * @param CompositeConnectionInterface $connection Redis connection that generated the error.
     * @param string                       $message    Error message.
     */
    protected function onProtocolError(CompositeConnectionInterface $connection, $message)
    {
        CommunicationException::handle(
            new ProtocolException($connection, "$message [{$connection->getParameters()}]")
        );
    }
}
