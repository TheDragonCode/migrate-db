<?php

namespace Tests\Configurations;

use Helldar\Support\Concerns\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @method void setDriver(string $value)
 * @method void setHost(string $value)
 * @method void setPort(string $value)
 * @method void setDatabase(string|null $value)
 * @method void setUsername(string $value)
 * @method void setPassword(string $value)
 * @method void setSchema(string $value)
 * @method void setSslmode(string $value)
 */
final class Configuration implements Arrayable
{
    use Makeable;

    protected $config = [
        'driver'         => null,
        'url'            => null,
        'host'           => null,
        'port'           => null,
        'database'       => null,
        'username'       => null,
        'password'       => null,
        'unix_socket'    => '',
        'charset'        => 'utf8mb4',
        'collation'      => 'utf8mb4_unicode_ci',
        'prefix'         => '',
        'prefix_indexes' => true,
        'strict'         => true,
        'engine'         => null,
        'options'        => [],
    ];

    public function __call(string $name, array $value): void
    {
        Arr::set($this->config, $this->resolveKeyName($name), $this->castValue($value[0]));
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

    protected function resolveKeyName(string $name): string
    {
        return (string) Str::of($name)->snake()->after('_');
    }

    protected function castValue($value)
    {
        return is_array($value) ? $value : (string) $value;
    }
}
