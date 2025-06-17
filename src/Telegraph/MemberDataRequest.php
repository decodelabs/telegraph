<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Exceptional;
use DecodeLabs\Telegraph\Source\EmailType;

class MemberDataRequest
{
    public string $email;

    public ?string $firstName = null;
    public ?string $lastName = null;

    public ?string $fullName {
        get {
            if (
                $this->firstName &&
                $this->lastName
            ) {
                return trim($this->firstName . ' ' . $this->lastName);
            }

            if ($this->firstName !== null) {
                return $this->firstName;
            }

            if ($this->lastName !== null) {
                return $this->lastName;
            }

            return null;
        }
    }

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

    public ?EmailType $emailType = null;

    /**
     * @var array<string,bool>
     */
    protected(set) array $groups = [];

    /**
     * @var array<string,bool>
     */
    protected(set) array $tags = [];


    /**
     * @param array<string,bool|string> $groups
     * @param array<string,bool|string> $tags
     */
    public function __construct(
        string $email,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $country = null,
        ?string $language = null,
        array $groups = [],
        array $tags = [],
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->country = $country;
        $this->language = $language;

        foreach($groups as $id => $intent) {
            if(is_string($intent)) {
                $id = $intent;
                $intent = true;
            }

            $this->setGroupIntent($id, $intent);
        }

        foreach($tags as $name => $intent) {
            if(is_string($intent)) {
                $name = $intent;
                $intent = true;
            }

            $this->setTagIntent($name, $intent);
        }
    }




    public function addGroup(
        string $id
    ): void {
        $this->groups[$id] = true;
    }

    public function removeGroup(
        string $id
    ): void {
        $this->groups[$id] = false;
    }

    public function setGroupIntent(
        string $id,
        ?bool $intent
    ): void {
        match($intent) {
            true => $this->addGroup($id),
            false => $this->removeGroup($id),
            default => $this->unsetGroup($id)
        };
    }

    public function getGroupIntent(
        string $id
    ): ?bool {
        return $this->groups[$id] ?? null;
    }

    public function hasGroup(
        string $id
    ): bool {
        return isset($this->groups[$id]);
    }

    public function unsetGroup(
        string $id
    ): void {
        unset($this->groups[$id]);
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
