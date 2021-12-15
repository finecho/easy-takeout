<?php

declare(strict_types=1);

namespace EasyWaimai;

use ArrayAccess;
use EasyWaimai\Exceptions\InvalidArgumentException;
use JsonSerializable;

class Config implements ArrayAccess, JsonSerializable
{
    public function __construct(protected array $options)
    {
    }

    public function get(string $key, $default = null)
    {
        $config = $this->options;

        if (is_null($key)) {
            return $config;
        }

        if (isset($config[$key])) {
            return $config[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    public function set(string $key, $value)
    {
        if (is_null($key)) {
            throw new InvalidArgumentException('Invalid config key.');
        }

        $keys = explode('.', $key);
        $config = &$this->options;

        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($config[$key]) || !is_array($config[$key])) {
                $config[$key] = [];
            }
            $config = &$config[$key];
        }

        $config[array_shift($keys)] = $value;

        return $config;
    }

    public function has(string $key): bool
    {
        return (bool) $this->get($key);
    }

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    public function extend(array $options): Config
    {
        return new Config(\array_merge($this->options, $options));
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->options);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    public function jsonSerialize()
    {
        return $this->options;
    }

    public function __toString()
    {
        return \json_encode($this, \JSON_UNESCAPED_UNICODE);
    }
}
