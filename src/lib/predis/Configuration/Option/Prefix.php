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

use Mohamed205\voxum\lib\predis\Command\Processor\KeyPrefixProcessor;
use Mohamed205\voxum\lib\predis\Command\Processor\ProcessorInterface;
use Mohamed205\voxum\lib\predis\Configuration\OptionInterface;
use Mohamed205\voxum\lib\predis\Configuration\OptionsInterface;

/**
 * Configures a command processor that apply the specified prefix string to a
 * series of Redis commands considered prefixable.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Prefix implements OptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(OptionsInterface $options, $value)
    {
        if (is_callable($value)) {
            $value = call_user_func($value, $options);
        }

        if ($value instanceof ProcessorInterface) {
            return $value;
        }

        return new KeyPrefixProcessor((string) $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(OptionsInterface $options)
    {
        // NOOP
    }
}
