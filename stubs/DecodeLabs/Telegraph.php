<?php
/**
 * This is a stub file for IDE compatibility only.
 * It should not be included in your projects.
 */
namespace DecodeLabs;

use DecodeLabs\Veneer\Proxy as Proxy;
use DecodeLabs\Veneer\ProxyTrait as ProxyTrait;
use DecodeLabs\Telegraph\Context as Inst;
use DecodeLabs\Telegraph\Config as Ref0;
use DecodeLabs\Telegraph\Cache as Ref1;
use Psr\Cache\CacheItemPoolInterface as Ref2;
use DecodeLabs\Telegraph\Store as Ref3;
use DecodeLabs\Telegraph\Source as Ref4;
use DecodeLabs\Telegraph\SourceReference as Ref5;
use DecodeLabs\Telegraph\Adapter as Ref6;
use DecodeLabs\Telegraph\Source\ListInfo as Ref7;
use DecodeLabs\Telegraph\MemberDataRequest as Ref8;
use DecodeLabs\Telegraph\SubscriptionResponse as Ref9;
use DecodeLabs\Telegraph\Source\MemberInfo as Ref10;

class Telegraph implements Proxy
{
    use ProxyTrait;

    public const Veneer = 'DecodeLabs\\Telegraph';
    public const VeneerTarget = Inst::class;

    protected static Inst $_veneerInstance;

    public static function setConfig(?Ref0 $config): void {}
    public static function getConfig(): ?Ref0 {
        return static::$_veneerInstance->getConfig();
    }
    public static function setCache(Ref1|Ref2 $cache): void {}
    public static function getCache(): Ref1 {
        return static::$_veneerInstance->getCache();
    }
    public static function setStore(Ref3 $store): void {}
    public static function getStore(): ?Ref3 {
        return static::$_veneerInstance->getStore();
    }
    public static function loadDefault(): ?Ref4 {
        return static::$_veneerInstance->loadDefault();
    }
    public static function load(Ref5|string $name): ?Ref4 {
        return static::$_veneerInstance->load(...func_get_args());
    }
    public static function loadAll(): array {
        return static::$_veneerInstance->loadAll();
    }
    public static function getSourceNames(): array {
        return static::$_veneerInstance->getSourceNames();
    }
    public static function hasSource(string $name): bool {
        return static::$_veneerInstance->hasSource(...func_get_args());
    }
    public static function loadDefaultAdapter(): ?Ref6 {
        return static::$_veneerInstance->loadDefaultAdapter();
    }
    public static function loadAdapterFor(string $name): ?Ref6 {
        return static::$_veneerInstance->loadAdapterFor(...func_get_args());
    }
    public static function loadAdapter(string $name, array $settings = []): Ref6 {
        return static::$_veneerInstance->loadAdapter(...func_get_args());
    }
    public static function getListInfo(Ref5|string $source): ?Ref7 {
        return static::$_veneerInstance->getListInfo(...func_get_args());
    }
    public static function subscribeDisciple(Ref5|string $source, ?Ref8 $request = NULL): Ref9 {
        return static::$_veneerInstance->subscribeDisciple(...func_get_args());
    }
    public static function subscribeUser(Ref5|string $source, string $userId, Ref8 $request): Ref9 {
        return static::$_veneerInstance->subscribeUser(...func_get_args());
    }
    public static function subscribe(Ref5|string $source, Ref8 $request): Ref9 {
        return static::$_veneerInstance->subscribe(...func_get_args());
    }
    public static function updateDisciple(Ref5|string $source, Ref8 $request): Ref9 {
        return static::$_veneerInstance->updateDisciple(...func_get_args());
    }
    public static function updateUser(Ref5|string $source, string $userId, string $email, Ref8 $request): Ref9 {
        return static::$_veneerInstance->updateUser(...func_get_args());
    }
    public static function update(Ref5|string $source, string $email, Ref8 $request): Ref9 {
        return static::$_veneerInstance->update(...func_get_args());
    }
    public static function updateDiscipleAll(Ref8 $request): array {
        return static::$_veneerInstance->updateDiscipleAll(...func_get_args());
    }
    public static function updateUserAll(string $userId, string $email, Ref8 $request): array {
        return static::$_veneerInstance->updateUserAll(...func_get_args());
    }
    public static function updateAll(string $email, Ref8 $request): array {
        return static::$_veneerInstance->updateAll(...func_get_args());
    }
    public static function unsubscribeDisciple(Ref5|string $source): Ref9 {
        return static::$_veneerInstance->unsubscribeDisciple(...func_get_args());
    }
    public static function unsubscribeUser(Ref5|string $source, string $userId, string $email): Ref9 {
        return static::$_veneerInstance->unsubscribeUser(...func_get_args());
    }
    public static function unsubscribe(Ref5|string $source, string $email): Ref9 {
        return static::$_veneerInstance->unsubscribe(...func_get_args());
    }
    public static function unsubscribeDiscipleAll(): array {
        return static::$_veneerInstance->unsubscribeDiscipleAll();
    }
    public static function unsubscribeUserAll(string $userId, string $email): array {
        return static::$_veneerInstance->unsubscribeUserAll(...func_get_args());
    }
    public static function unsubscribeAll(string $email): array {
        return static::$_veneerInstance->unsubscribeAll(...func_get_args());
    }
    public static function getDiscipleMemberInfo(Ref5|string $source): ?Ref10 {
        return static::$_veneerInstance->getDiscipleMemberInfo(...func_get_args());
    }
    public static function getUserMemberInfo(Ref5|string $source, string $userId, string $email): ?Ref10 {
        return static::$_veneerInstance->getUserMemberInfo(...func_get_args());
    }
    public static function getMemberInfo(Ref5|string $source, string $email): ?Ref10 {
        return static::$_veneerInstance->getMemberInfo(...func_get_args());
    }
};
