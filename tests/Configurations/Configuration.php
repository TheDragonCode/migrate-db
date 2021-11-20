<?php

namespace Tests\Configurations;

use DragonCode\Support\Concerns\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @method void setHost(string $value)
 * @method void setDatabase(string|null $value)
 * @method void setUsername(string $value)
 * @method void setPassword(string $value)
 * @method bool hasDatabase()
 * @method bool doesntDatabase()
 */
class Configuration implements Arrayable
{
    use Makeable;

    protected $config = [];

    public function __call(string $name, array $value)
    {
        $key = $this->resolveKeyName($name);

        switch (true) {
            case Str::startsWith($name, 'set'):
                return $this->set($key, $value);

            case Str::startsWith($name, 'has'):
                return $this->has($key);

            case Str::startsWith($name, 'doesnt'):
                return ! $this->has($key);

            default:
                throw new InvalidArgumentException('Unknown method: ' . $name);
        }
    }

    public function merge(array $config): self
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    public function toArray(): array
    {
        return $this->config;
    }

    protected function set(string $key, $value): self
    {
        Arr::set($this->config, $key, $this->castValue($value[0]));

        return $this;
    }

    protected function has(string $key): bool
    {
        $value = Arr::get($this->config, $key);

        return ! empty($value);
    }

    protected function resolveKeyName(string $name): string
    {
        return (string) Str::of($name)->snake()->after('_');
    }

    protected function castValue($value)
    {
        return is_array($value) ? $value : (string) $value;
    }
}
