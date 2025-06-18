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
 * @phpstan-type ListInfoArray array{
 *     id: string,
 *     name: string,
 *     fetchDate: string,
 *     creationDate: ?string,
 *     subscribeUrl: ?string,
 *     memberCount: ?int,
 *     groups: array<GroupInfoArray>,
 *     tags: array<TagInfoArray>,
 * }
 */
class ListInfo extends ListReference implements JsonSerializable
{
    /**
     * @var array<string,GroupInfo>
     */
    protected(set) array $groups = [];

    /**
     * @var array<string,TagInfo>
     */
    protected(set) array $tags = [];


    /**
     * @param array<GroupInfo> $groups
     * @param array<TagInfo> $tags
     */
    public function __construct(
        string $id,
        string $name,
        ?DateTimeInterface $fetchDate = null,
        ?DateTimeInterface $creationDate = null,
        ?string $subscribeUrl = null,
        ?int $memberCount = null,
        array $groups = [],
        array $tags = []
    ) {
        parent::__construct(
            id: $id,
            name: $name,
            fetchDate: $fetchDate,
            creationDate: $creationDate,
            subscribeUrl: $subscribeUrl,
            memberCount: $memberCount
        );

        foreach($groups as $group) {
            $this->groups[$group->id] = $group;
        }

        foreach($tags as $tag) {
            $this->tags[$tag->id] = $tag;
        }
    }


    /**
     * @return array<string,string>
     */
    public function getGroupOptions(
        bool $forceCategories = false,
        ?string $noCategoryLabel = null
    ): array {
        $output = [];
        $categoryNames = [];
        $useCategory = $forceCategories;

        foreach($this->groups as $group) {
            $categoryName = $group->categoryName ?? $noCategoryLabel ?? 'No category';
            $categoryNames[$categoryName] = true;

            if(count($categoryNames) > 1) {
                $useCategory = true;
            }
        }

        foreach($this->groups as $group) {
            $name = $group->name;

            if(
                $useCategory &&
                $group->categoryName !== null
            ) {
                $name = $group->categoryName . ' / ' . $name;
            }

            $output[$group->id] = $name;
        }

        asort($output);
        return $output;
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function getCategorizedGroupOptions(
        ?string $noCategoryLabel = null
    ): array {
        $output = [];

        foreach($this->groups as $group) {
            $categoryName = $group->categoryName ?? $noCategoryLabel ?? 'No category';
            $output[$categoryName][$group->id] = $group->name;
        }

        foreach($output as $category => $groups) {
            asort($output[$category]);
        }

        ksort($output);
        return $output;
    }

    /**
     * @return array<string,string>
     */
    public function getGroupCategoryOptions(): array
    {
        $output = [];

        foreach($this->groups as $group) {
            if($group->categoryId === null) {
                continue;
            }

            $output[$group->categoryId] = $group->categoryName ?? $group->categoryId;
        }

        asort($output);
        return $output;
    }

    /**
     * @return array<string,string>
     */
    public function getTagOptions(): array
    {
        $output = [];

        foreach($this->tags as $tag) {
            $output[$tag->id] = $tag->name;
        }

        asort($output);
        return $output;
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
            fetchDate: Coercion::tryDateTime($data['fetchDate'] ?? null),
            creationDate: Coercion::tryDateTime($data['creationDate'] ?? null),
            subscribeUrl: Coercion::tryString($data['subscribeUrl'] ?? null),
            memberCount: Coercion::tryInt($data['memberCount'] ?? null),
            groups: array_map(
                fn($group) => GroupInfo::fromArray(Coercion::asArray($group)),
                Coercion::asArray($data['groups'] ?? [])
            ),
            tags: array_map(
                fn($tag) => TagInfo::fromArray(Coercion::asArray($tag)),
                Coercion::asArray($data['tags'] ?? [])
            ),
        );
    }

    /**
     * @return ListInfoArray
     */
    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'fetchDate' => $this->fetchDate->toDateTimeString(),
            'creationDate' => $this->creationDate?->toDateTimeString(),
            'subscribeUrl' => $this->subscribeUrl,
            'memberCount' => $this->memberCount,
            'groups' => array_map(
                fn(GroupInfo $group) => $group->jsonSerialize(),
                $this->groups
            ),
            'tags' => array_map(
                fn(TagInfo $tag) => $tag->jsonSerialize(),
                $this->tags
            ),
        ];
    }
}
