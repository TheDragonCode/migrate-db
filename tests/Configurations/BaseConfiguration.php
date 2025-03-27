<?php

declare(strict_types=1);

namespace Tests\Configurations;

use DragonCode\Support\Concerns\Makeable;
use Illuminate\Contracts\Support\Arrayable;

abstract class BaseConfiguration implements Arrayable
{
    use Makeable;

    protected $config = [];

    protected $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration->merge($this->config);
    }

    public function merge(array $config): self
    {
        $this->configuration->merge($config);

        return $this;
    }

    public function setDatabase(?string $name = null): self
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
        $this->fillDatabase();
        $this->fillUsername();
        $this->fillPassword();
    }

    protected function fillDatabase(): void
    {
        if ($this->configuration->doesntDatabase()) {
            $this->configuration->setDatabase('default');
        }
    }

    protected function fillUsername(): void
    {
        $this->configuration->setUsername(env('DB_USERNAME'));
    }

    protected function fillPassword(): void
    {
        $this->configuration->setPassword(env('DB_PASSWORD'));
    }
}
