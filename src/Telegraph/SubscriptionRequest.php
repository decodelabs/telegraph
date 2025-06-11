<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Exceptional;

class SubscriptionRequest
{
    public string $listId;
    public string $email;
    public ?string $firstName = null;
    public ?string $surname = null;

    public ?string $country = null {
        set {
            if (empty($value)) {
                $this->country = null;
            } else {
                if(strlen($value) !== 2) {
                    throw Exceptional::InvalidArgument(
                        'Country code must be a 2-letter ISO code'
                    );
                }

                $this->country = strtoupper($value);
            }
        }
    }

    public ?string $language = null {
        set {
            if (empty($value)) {
                $this->language = null;
            } else {
                $this->language = strtolower($value);
            }
        }
    }

    /**
     * @var array<string,bool>
     */
    protected(set) array $groups = [];

    /**
     * @var array<string,bool>
     */
    protected(set) array $tags = [];



    public function __construct(
        string $listId,
        string $email,
        ?string $firstName = null,
        ?string $surname = null,
        ?string $country = null,
        ?string $language = null
    ) {
        $this->listId = $listId;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->surname = $surname;
        $this->country = $country;
        $this->language = $language;
    }



    public function addGroup(
        string $name
    ): void {
        $this->groups[$name] = true;
    }

    public function removeGroup(
        string $name
    ): void {
        $this->groups[$name] = false;
    }

    public function setGroupIntent(
        string $name,
        ?bool $intent
    ): void {
        match($intent) {
            true => $this->addGroup($name),
            false => $this->removeGroup($name),
            default => $this->unsetGroup($name)
        };
    }

    public function getGroupIntent(
        string $name
    ): ?bool {
        return $this->groups[$name] ?? null;
    }

    public function hasGroup(
        string $name
    ): bool {
        return isset($this->groups[$name]);
    }

    public function unsetGroup(
        string $name
    ): void {
        unset($this->groups[$name]);
    }


    public function addTag(
        string $name
    ): void {
        $this->tags[$name] = true;
    }

    public function removeTag(
        string $name
    ): void {
        $this->tags[$name] = false;
    }

    public function setTagIntent(
        string $name,
        ?bool $intent
    ): void {
        match($intent) {
            true => $this->addTag($name),
            false => $this->removeTag($name),
            default => $this->unsetTag($name)
        };
    }

    public function getTagIntent(
        string $name
    ): ?bool {
        return $this->tags[$name] ?? null;
    }

    public function hasTag(
        string $name
    ): bool {
        return isset($this->tags[$name]);
    }

    public function unsetTag(
        string $name
    ): void {
        unset($this->tags[$name]);
    }
}
