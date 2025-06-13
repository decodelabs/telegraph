<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

use Carbon\CarbonImmutable;
use DateTimeInterface;

class GroupInfo
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
}
