<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Dovetail\Config;

use DecodeLabs\Coercion;
use DecodeLabs\Dovetail\Config;
use DecodeLabs\Dovetail\ConfigTrait;
use DecodeLabs\Telegraph\Config as ConfigInterface;

class Telegraph implements Config, ConfigInterface
{
    use ConfigTrait;

    public static function getDefaultValues(): array
    {
        return [
            '!example' => [
                'adapter' => 'Mailchimp',
                'apiKey' => "{{Env::asString('MAILCHIMP_API_KEY','abc1234567890')}}",
                'list' => '123b456f'
            ]
        ];
    }

    public function getDefaultSourceName(): ?string
    {
        $keys = $this->data->getKeys();

        if (empty($keys)) {
            return null;
        }

        $firstKey = Coercion::asString($keys[0]);

        if (str_starts_with($firstKey, '!')) {
            return null;
        }

        return $firstKey;
    }

    public function getSourceNames(): array
    {
        $output = [];

        foreach ($this->data->getKeys() as $key) {
            $key = (string)$key;

            if (str_starts_with($key, '!')) {
                continue;
            }

            $output[] = $key;
        }

        return $output;
    }

    public function getSourceAdapter(
        string $name
    ): ?string {
        return $this->data->__get($name)->adapter->as('?string');
    }

    public function getSourceRemoteId(
        string $name
    ): ?string {
        return $this->data->__get($name)->list->as('?string');
    }

    public function getSourceSettings(
        string $name
    ): array {
        /** @var array<string,mixed> */
        $output = $this->data->__get($name)->toArray();
        unset($output['adapter'], $output['list']);

        return $output;
    }
}
