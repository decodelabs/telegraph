<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

use Carbon\CarbonImmutable;
use DateTimeInterface;

class ListInfo
{
    protected(set) string $id;
    protected(set) string $name;

    protected(set) ?CarbonImmutable $creationDate = null {
        set(?DateTimeInterface $value) {
            $this->creationDate = $value ? CarbonImmutable::instance($value) : null;
        }
    }

    protected(set) ?string $subscribeUrl = null;
    protected(set) ?int $memberCount = null;

    public function __construct(
        string $id,
        string $name,
        ?DateTimeInterface $creationDate = null,
        ?string $subscribeUrl = null,
        ?int $memberCount = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->creationDate = $creationDate;
        $this->subscribeUrl = $subscribeUrl;
        $this->memberCount = $memberCount;
    }
}
