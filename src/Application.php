<?php

declare(strict_types=1);

namespace EasyWaimai;

use EasyWaimai\Platforms\Meituan;

class Application
{
    protected Config $config;
    protected Client $client;
    protected Platform $platform;

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    public function __construct(array|Config $config)
    {
        if (\is_array($config)) {
            $config = new Config($config);
        }

        $this->config = $config;
        $this->client = new Client($this->config);
        $this->setPlatform($this->config->get('default_platform', Meituan::PLATFORM_NAME));
    }

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    public function setPlatform(Platform|string $platform): static
    {
        if (!$platform instanceof Platform) {
            $platform = Platform::make($platform, $this->config);
        }

        $this->platform = $platform;

        return $this;
    }

    public function getPlatform(): Platform
    {
        return $this->platform;
    }
}
