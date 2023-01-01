<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mohamed205\voxum\lib\predis\Configuration\Option;

use Mohamed205\voxum\lib\predis\Configuration\OptionInterface;
use Mohamed205\voxum\lib\predis\Configuration\OptionsInterface;
use Predis\Cluster\Hash;

/**
 * Configures an hash generator used by the redis-cluster connection backend.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class CRC16 implements OptionInterface
{
    /**
     * Returns an hash generator instance from a descriptive name.
     *
     * @param OptionsInterface $options     Client options.
     * @param string           $description Identifier of a hash generator (`predis`, `phpiredis`)
     *
     * @return callable
     */
    protected function getHashGeneratorByDescription(OptionsInterface $options, $description)
    {
        if ($description === 'predis') {
            return new \Mohamed205\voxum\lib\predis\Cluster\Hash\CRC16();
        } elseif ($description === 'phpiredis') {
            return new \Mohamed205\voxum\lib\predis\Cluster\Hash\PhpiredisCRC16();
        } else {
            throw new \InvalidArgumentException(
                'String value for the crc16 option must be either `predis` or `phpiredis`'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function filter(OptionsInterface $options, $value)
    {
        if (is_callable($value)) {
            $value = call_user_func($value, $options);
        }

        if (is_string($value)) {
            return $this->getHashGeneratorByDescription($options, $value);
        } elseif ($value instanceof \Mohamed205\voxum\lib\predis\Cluster\Hash\HashGeneratorInterface) {
            return $value;
        } else {
            $class = get_class($this);
            throw new \InvalidArgumentException("$class expects a valid hash generator");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(OptionsInterface $options)
    {
        return function_exists('phpiredis_utils_crc16')
            ? new \Mohamed205\voxum\lib\predis\Cluster\Hash\PhpiredisCRC16()
            : new \Mohamed205\voxum\lib\predis\Cluster\Hash\CRC16();
    }
}
