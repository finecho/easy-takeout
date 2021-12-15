<?php

namespace EasyWaimai\Traits;

use EasyWaimai\Exceptions\InvalidArgumentException;

trait ChainableHttpClient
{
    protected string $uri = '/';

    public function withUri(string $uri): static
    {
        $clone = clone $this;

        if (\str_starts_with($uri, 'http://') || \str_starts_with($uri, 'https://')) {
            $clone->uri = $uri;
        } else {
            $clone->uri = \trim(\sprintf('/%s/%s', \trim($this->uri, '/'), \trim($uri, '/')), '/');
        }

        return $clone;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function __get($name)
    {
        return $this->withUri(\strval($name));
    }

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    public function __call($name, $arguments)
    {
        if (\in_array(\strtoupper($name), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $this->callWithShortcuts(\strtoupper($name), ...$arguments);
        }

        return \call_user_func_array([$this->client, $name], $arguments);
    }

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    protected function callWithShortcuts(
        string $method,
        string | array $uri = [],
        array $options = []
    ): \Symfony\Contracts\HttpClient\ResponseInterface {
        if (\is_string($uri)) {
            $uri = $this->withUri($uri)->getUri();
        } else {
            $options = $uri;
            $uri = $this->getUri();
        }

        $method = \strtoupper($method);

        [$uri, $options] = $this->replaceUriVariables($uri, $options);

        if (!\array_intersect(\array_keys($options), ['query', 'body', 'json'])) {
            $extraOptions = \array_filter($options, fn ($option) => \is_array($option));

            $options = \array_diff_key($options, $extraOptions);

            $options = match (true) {
                \in_array($method, ['GET', 'DELETE']) => ['query' => $options],
                \in_array($method, ['POST', 'PATCH']) => ['body' => $options],
                default => ['json' => $options],
            };

            $options = \array_merge($extraOptions, $options);
        }

        return $this->request($method, $uri, $options);
    }

    /**
     * @throws \EasyWaimai\Exceptions\InvalidArgumentException
     */
    public function replaceUriVariables(string $uri, array $options): array
    {
        return [
            \preg_replace_callback(
                '~\$(?<name>[a-z0-9_]+)~i',
                callback: function ($matches) use (&$options) {
                    if (empty($options[$matches['name']])) {
                        throw new InvalidArgumentException(\sprintf('Missing url variables "%s".', $matches['name']));
                    }

                    $value = $options[$matches['name']];

                    unset($options[$matches['name']]);

                    return $value;
                },
                subject: $uri
            ),
            $options,
        ];
    }
}
