<?php

declare(strict_types=1);

namespace EasyWaimai;

use EasyWaimai\Exceptions\InvalidArgumentException;

class Platform
{
    public function __construct(protected Config|array $config)
    {
        if (\is_array($this->config)) {
            $this->config = new Config($this->config);
        }
    }

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    public static function make(?string $name, Config|array $config): Platform
    {
        $name = \ucfirst($name);

        $platform = __NAMESPACE__."\\Providers\\{$name}";

        if (!class_exists($platform)) {
            throw new InvalidArgumentException('Platform is not exist!');
        }

        return new $platform($config);
    }
}
