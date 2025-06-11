<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph\Source;

use Carbon\CarbonImmutable;
use DateTimeInterface;

class MemberInfo
{
    protected(set) string $id;
    protected(set) string $email;
    protected(set) MemberStatus $status;

    protected(set) ?CarbonImmutable $creationDate = null {
        set(?DateTimeInterface $value) {
            $this->creationDate = $value ? CarbonImmutable::instance($value) : null;
        }
    }

    protected(set) ?string $country = null;
    protected(set) ?string $language = null;
    protected(set) ?EmailType $emailType = null;

    /**
     * @var array<string>
     */
    protected(set) array $groupIds = [];

    /**
     * @var array<string>
     */
    protected(set) array $tagIds = [];
}
