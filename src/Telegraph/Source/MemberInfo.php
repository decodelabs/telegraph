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
 * @phpstan-import-type GroupInfoArray from GroupInfo
 * @phpstan-import-type TagInfoArray from TagInfo
 * @phpstan-type MemberInfoArray array{
 *     id: string,
 *     email: string,
 *     status: string,
 *     fetchDate: string,
 *     creationDate: ?string,
 *     firstName: ?string,
 *     lastName: ?string,
 *     country: ?string,
 *     language: ?string,
 *     emailType: ?string,
 *     groups: array<GroupInfoArray>,
 *     tags: array<TagInfoArray>,
 * }
 */
class MemberInfo implements JsonSerializable
{
    public protected(set) string $id;
    public protected(set) string $email;
    public protected(set) MemberStatus $status;

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

    public protected(set) ?string $firstName = null;
    public protected(set) ?string $lastName = null;
    public protected(set) ?string $country = null;
    public protected(set) ?string $language = null;
    public protected(set) ?EmailType $emailType = null;

    /**
     * @var array<GroupInfo>
     */
    public protected(set) array $groups = [];

    /**
     * @var array<TagInfo>
     */
    public protected(set) array $tags = [];

    /**
     * @var array<ConsentField>
     */
    public protected(set) array $consent = [];

    /**
     * @param array<GroupInfo> $groups
     * @param array<TagInfo> $tags
     * @param array<ConsentField> $consent
     */
    public function __construct(
        string $id,
        string $email,
        MemberStatus $status,
        ?DateTimeInterface $fetchDate = null,
        ?DateTimeInterface $creationDate = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $country = null,
        ?string $language = null,
        ?EmailType $emailType = null,
        array $groups = [],
        array $tags = [],
        array $consent = []
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->status = $status;
        $this->fetchDate = $fetchDate;
        $this->creationDate = $creationDate;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->country = $country;
        $this->language = $language;
        $this->emailType = $emailType;

        foreach ($groups as $group) {
            $this->groups[$group->id] = $group;
        }

        foreach ($tags as $tag) {
            $this->tags[$tag->id] = $tag;
        }

        foreach ($consent as $consentField) {
            $this->consent[$consentField->id] = $consentField;
        }
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(
        array $data
    ): self {
        return new self(
            id: Coercion::asString($data['id'] ?? null),
            email: Coercion::asString($data['email'] ?? null),
            status: MemberStatus::from(Coercion::asString($data['status'] ?? null)),
            fetchDate: Coercion::tryDateTime($data['fetchDate'] ?? null),
            creationDate: Coercion::tryDateTime($data['creationDate'] ?? null),
            firstName: Coercion::tryString($data['firstName'] ?? null),
            lastName: Coercion::tryString($data['lastName'] ?? null),
            country: Coercion::tryString($data['country'] ?? null),
            language: Coercion::tryString($data['language'] ?? null),
            emailType: EmailType::tryFrom(Coercion::toString($data['emailType'] ?? '')),
            groups: array_map(
                fn ($group) => GroupInfo::fromArray(Coercion::asArray($group)),
                Coercion::asArray($data['groups'] ?? [])
            ),
            tags: array_map(
                fn ($tag) => TagInfo::fromArray(Coercion::asArray($tag)),
                Coercion::asArray($data['tags'] ?? [])
            ),
            consent: array_map(
                fn ($consent) => ConsentField::fromArray(Coercion::asArray($consent)),
                Coercion::asArray($data['consent'] ?? [])
            ),
        );
    }

    /**
     * @return MemberInfoArray
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'status' => $this->status->value,
            'fetchDate' => $this->fetchDate->toDateTimeString(),
            'creationDate' => $this->creationDate?->toDateTimeString(),
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'country' => $this->country,
            'language' => $this->language,
            'emailType' => $this->emailType?->value,
            'groups' => array_map(
                fn (GroupInfo $group) => $group->jsonSerialize(),
                $this->groups
            ),
            'tags' => array_map(
                fn (TagInfo $tag) => $tag->jsonSerialize(),
                $this->tags
            ),
            'consent' => array_map(
                fn (ConsentField $consent) => $consent->jsonSerialize(),
                $this->consent
            ),
        ];
    }
}
