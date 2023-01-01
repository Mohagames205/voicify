<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Cluster;

use Mohamed205\voxum\lib\predis\Cluster\Hash\CRC16;
use Mohamed205\voxum\lib\predis\Cluster\Hash\HashGeneratorInterface;
use Mohamed205\voxum\lib\predis\NotSupportedException;

/**
 * Default class used by Predis to calculate hashes out of keys of
 * commands supported by redis-cluster.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class RedisStrategy extends ClusterStrategy
{
    protected $hashGenerator;

    /**
     * @param HashGeneratorInterface $hashGenerator Hash generator instance.
     */
    public function __construct(HashGeneratorInterface $hashGenerator = null)
    {
        parent::__construct();

        $this->hashGenerator = $hashGenerator ?: new CRC16();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlotByKey($key)
    {
        $key = $this->extractKeyTag($key);

        return $this->hashGenerator->hash($key) & 0x3FFF;
    }

    /**
     * {@inheritdoc}
     */
    public function getDistributor()
    {
        $class = get_class($this);
        throw new NotSupportedException("$class does not provide an external distributor");
    }
}
