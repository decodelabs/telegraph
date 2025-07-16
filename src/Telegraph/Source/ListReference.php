<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

use Carbon\CarbonImmutable;
use DateTimeInterface;

class ListReference
{
    public protected(set) string $id;
    public protected(set) string $name;
    public protected(set) CarbonImmutable $fetchDate {
        set(?DateTimeInterface $value) {
            $this->fetchDate = $value ?
                CarbonImmutable::instance($value) :
                CarbonImmutable::now();
        }
    }

    public protected(set) ?CarbonImmutable $creationDate = null {
        set(?DateTimeInterface $value) {
            $this->creationDate = $value ?
                CarbonImmutable::instance($value) :
                null;
        }
    }

    public protected(set) ?string $subscribeUrl = null;
    public protected(set) ?int $memberCount = null;

    public function __construct(
        string $id,
        string $name,
        ?DateTimeInterface $fetchDate = null,
        ?DateTimeInterface $creationDate = null,
        ?string $subscribeUrl = null,
        ?int $memberCount = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->fetchDate = $fetchDate;
        $this->creationDate = $creationDate;
        $this->subscribeUrl = $subscribeUrl;
        $this->memberCount = $memberCount;
    }
}
