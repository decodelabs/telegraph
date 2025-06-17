<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use DecodeLabs\Coercion;
use JsonSerializable;

/**
 * @phpstan-type GroupInfoArray array{
 *     id: string,
 *     name: string,
 *     categoryId: ?string,
 *     categoryName: ?string,
 *     creationDate: ?string,
 * }
 */
class GroupInfo implements JsonSerializable
{
    protected(set) string $id;
    protected(set) string $name;
    protected(set) ?string $categoryId = null;
    protected(set) ?string $categoryName = null;

    protected(set) ?CarbonImmutable $creationDate = null {
        set(?DateTimeInterface $value) {
            $this->creationDate = $value ? CarbonImmutable::instance($value) : null;
        }
    }

    public function __construct(
        string $id,
        string $name,
        ?string $categoryId = null,
        ?string $categoryName = null,
        ?DateTimeInterface $creationDate = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->creationDate = $creationDate;
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
            categoryId: Coercion::tryString($data['categoryId'] ?? null),
            categoryName: Coercion::tryString($data['categoryName'] ?? null),
            creationDate: Coercion::tryDateTime($data['creationDate'] ?? null),
        );
    }

    /**
     * @return GroupInfoArray
     */
    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'categoryId' => $this->categoryId,
            'categoryName' => $this->categoryName,
            'creationDate' => $this->creationDate?->toDateTimeString(),
        ];
    }
}
