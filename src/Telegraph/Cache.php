<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use Carbon\CarbonInterval;
use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;
use Psr\Cache\CacheItemPoolInterface;

class Cache
{
    protected ?CacheItemPoolInterface $driver;

    public function __construct(
        ?CacheItemPoolInterface $driver
    ) {
        $this->driver = $driver;
    }

    public function clearAll(): void
    {
        $this->driver?->clear();
    }

    public function storeListInfo(
        SourceReference $source,
        ?ListInfo $list
    ): void {
        if (!$this->driver) {
            return;
        }

        $item = $this->driver->getItem($source->name);
        $item->set($list ?? false);
        $this->driver->save($item);
    }

    public function fetchListInfo(
        SourceReference $source
    ): ListInfo|false|null {
        $output = $this->driver?->getItem($source->name)->get();

        if (
            $output === null ||
            $output === false ||
            $output instanceof ListInfo
        ) {
            return $output;
        }

        return null;
    }

    public function clearListInfo(
        SourceReference $source
    ): void {
        $this->driver?->deleteItem($source->name);
    }

    public function storeMemberInfo(
        SourceReference $source,
        string $email,
        ?MemberInfo $member
    ): void {
        if (!$this->driver) {
            return;
        }

        $hash = md5($email);
        $item = $this->driver->getItem($source->name . '|' . $hash);
        $item->set($member ?? false);
        $item->expiresAfter(CarbonInterval::hours(2));
        $this->driver->save($item);
    }

    public function fetchMemberInfo(
        SourceReference $source,
        string $email
    ): MemberInfo|false|null {
        $hash = md5($email);
        $output = $this->driver?->getItem($source->name . '|' . $hash)->get();

        if (
            $output === null ||
            $output === false ||
            $output instanceof MemberInfo
        ) {
            return $output;
        }

        return null;
    }

    public function clearMemberInfo(
        SourceReference $source,
        string $email
    ): void {
        $hash = md5($email);
        $this->driver?->deleteItem($source->name . '|' . $hash);
    }
}
