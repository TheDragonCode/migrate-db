<?php

namespace Tests\Configurations;

use Helldar\Support\Concerns\Makeable;
use Illuminate\Contracts\Support\Arrayable;

abstract class BaseConfiguration implements Arrayable
{
    use Makeable;

    protected $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function merge(array $config): self
    {
        $this->configuration->merge($config);

        return $this;
    }

    public function setDatabase(?string $name): self
    {
        $this->configuration->setDatabase($name);

        return $this;
    }

    public function toArray(): array
    {
        $this->fill();

        return $this->configuration->toArray();
    }

    protected function fill(): void
    {
        $this->configuration->setDatabase('default');

        $this->configuration->setUsername(env('DB_USERNAME'));
        $this->configuration->setPassword(env('DB_PASSWORD'));
    }
}
