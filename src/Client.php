<?php

declare(strict_types=1);

namespace EasyWaimai;

use EasyWaimai\Traits\ChainableHttpClient;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @method \Symfony\Contracts\HttpClient\ResponseInterface get(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface post(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface patch(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface put(string|array $uri = [], array $options = [])
 * @method \Symfony\Contracts\HttpClient\ResponseInterface delete(string|array $uri = [], array $options = [])
 */
class Client
{
    use DecoratorTrait;
    use ChainableHttpClient;

    public function __construct(
        public array | Config $config,
        ?HttpClientInterface $client = null,
    ) {
        $this->client = $client ?? HttpClient::create();

        if (\is_array($this->config)) {
            $this->config = new Config($this->config);
        }
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->client->request($method, ltrim($url, '/'), $options);
    }
}
