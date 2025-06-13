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

    protected(set) ?string $firstName = null;
    protected(set) ?string $lastName = null;
    protected(set) ?string $country = null;
    protected(set) ?string $language = null;
    protected(set) ?EmailType $emailType = null;

    /**
     * @var array<string>
     */
    protected(set) array $groupIds = [];

    /**
     * @var array<TagInfo>
     */
    protected(set) array $tags = [];

    /**
     * @param array<string> $groupIds
     * @param array<TagInfo> $tags
     */
    public function __construct(
        string $id,
        string $email,
        MemberStatus $status,
        ?DateTimeInterface $creationDate = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $country = null,
        ?string $language = null,
        ?EmailType $emailType = null,
        array $groupIds = [],
        array $tags = []
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->status = $status;
        $this->creationDate = $creationDate;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->country = $country;
        $this->language = $language;
        $this->emailType = $emailType;
        $this->groupIds = $groupIds;
        $this->tags = $tags;
    }
}
