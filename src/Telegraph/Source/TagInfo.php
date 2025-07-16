<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

use DecodeLabs\Coercion;
use JsonSerializable;

/**
 * @phpstan-type TagInfoArray array{
 *     id: string,
 *     name: string,
 * }
 */
class TagInfo implements JsonSerializable
{
    public protected(set) string $id;
    public protected(set) string $name;

    public function __construct(
        string $id,
        string $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(
        array $data
    ): self {
        return new self(
            id: Coercion::asString($data['id'] ?? null),
            name: Coercion::asString($data['name'] ?? null),
        );
    }

    /**
     * @return TagInfoArray
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
