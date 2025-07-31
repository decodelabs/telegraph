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
 * @phpstan-type ConsentFieldArray array{
 *     id: string,
 *     description: string,
 *     type: string,
 * }
 */
class ConsentField implements JsonSerializable
{
    public protected(set) string $id;
    public protected(set) string $description;
    public protected(set) ConsentType $type;

    public function __construct(
        string $id,
        string $description,
        ?ConsentType $type = null
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->type = $this->normalizeType($type);
    }

    private function normalizeType(
        ?ConsentType $type
    ): ConsentType {
        if ($type !== null) {
            return $type;
        }

        $description = strtolower($this->description);

        if (str_contains($description, 'email')) {
            return ConsentType::Email;
        }

        if (str_contains($description, 'phone')) {
            return ConsentType::Phone;
        }

        if (str_contains($description, 'sms')) {
            return ConsentType::Sms;
        }

        if (str_contains($description, 'mail')) {
            return ConsentType::Mail;
        }

        if (str_contains($description, 'advertising')) {
            return ConsentType::Advertising;
        }

        return ConsentType::Other;
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(
        array $data
    ): self {
        return new self(
            id: Coercion::asString($data['id'] ?? null),
            description: Coercion::asString($data['description'] ?? null),
            type: ConsentType::tryFrom(Coercion::toString($data['type'] ?? null)),
        );
    }

    /**
     * @return ConsentFieldArray
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'type' => $this->type->value,
        ];
    }
}
