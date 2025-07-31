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
use DecodeLabs\Telegraph\Source\GroupInfo as Ref8;
use DecodeLabs\Telegraph\Source\TagInfo as Ref9;
use DecodeLabs\Telegraph\MemberDataRequest as Ref10;
use DecodeLabs\Telegraph\SubscriptionResponse as Ref11;
use DecodeLabs\Telegraph\Source\ConsentField as Ref12;
use DecodeLabs\Telegraph\Source\ConsentType as Ref13;
use DecodeLabs\Telegraph\Source\MemberInfo as Ref14;

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
    public static function refreshListInfo(Ref5|string $source): ?Ref7 {
        return static::$_veneerInstance->refreshListInfo(...func_get_args());
    }
    public static function refreshListInfoAll(): array {
        return static::$_veneerInstance->refreshListInfoAll();
    }
    public static function getGroupOptions(Ref5|string $source, bool $forceCategories = false, ?string $noCategoryLabel = NULL): array {
        return static::$_veneerInstance->getGroupOptions(...func_get_args());
    }
    public static function getCategorizedGroupOptions(Ref5|string $source, ?string $noCategoryLabel = NULL): array {
        return static::$_veneerInstance->getCategorizedGroupOptions(...func_get_args());
    }
    public static function getGroups(Ref5|string $source): array {
        return static::$_veneerInstance->getGroups(...func_get_args());
    }
    public static function getGroup(Ref5|string $source, string $groupId): ?Ref8 {
        return static::$_veneerInstance->getGroup(...func_get_args());
    }
    public static function getGroupName(Ref5|string $source, string $groupId, bool $forceCategories = false, ?string $noCategoryLabel = NULL): ?string {
        return static::$_veneerInstance->getGroupName(...func_get_args());
    }
    public static function getGroupCategoryOptions(Ref5|string $source): array {
        return static::$_veneerInstance->getGroupCategoryOptions(...func_get_args());
    }
    public static function getGroupCategoryName(Ref5|string $source, string $groupId, ?string $noCategoryLabel = NULL): ?string {
        return static::$_veneerInstance->getGroupCategoryName(...func_get_args());
    }
    public static function getGroupCategoryNameByGroupId(Ref5|string $source, string $groupId, ?string $noCategoryLabel = NULL): ?string {
        return static::$_veneerInstance->getGroupCategoryNameByGroupId(...func_get_args());
    }
    public static function getTagOptions(Ref5|string $source): array {
        return static::$_veneerInstance->getTagOptions(...func_get_args());
    }
    public static function getTags(Ref5|string $source): array {
        return static::$_veneerInstance->getTags(...func_get_args());
    }
    public static function getTag(Ref5|string $source, string $tagId): ?Ref9 {
        return static::$_veneerInstance->getTag(...func_get_args());
    }
    public static function getTagName(Ref5|string $source, string $tagId): ?string {
        return static::$_veneerInstance->getTagName(...func_get_args());
    }
    public static function subscribeDisciple(Ref5|string $source, ?Ref10 $request = NULL): Ref11 {
        return static::$_veneerInstance->subscribeDisciple(...func_get_args());
    }
    public static function getConsentFields(Ref5|string $source): array {
        return static::$_veneerInstance->getConsentFields(...func_get_args());
    }
    public static function getConsentField(Ref5|string $source, string $consentFieldId): ?Ref12 {
        return static::$_veneerInstance->getConsentField(...func_get_args());
    }
    public static function getTypeConsentField(Ref5|string $source, Ref13 $type): ?Ref12 {
        return static::$_veneerInstance->getTypeConsentField(...func_get_args());
    }
    public static function subscribeUser(Ref5|string $source, string $userId, Ref10 $request): Ref11 {
        return static::$_veneerInstance->subscribeUser(...func_get_args());
    }
    public static function subscribe(Ref5|string $source, Ref10 $request): Ref11 {
        return static::$_veneerInstance->subscribe(...func_get_args());
    }
    public static function isDiscipleSubscribed(Ref5|string $source, Ref8|string|null $group = NULL, Ref9|string|null $tag = NULL): bool {
        return static::$_veneerInstance->isDiscipleSubscribed(...func_get_args());
    }
    public static function isUserSubscribed(Ref5|string $source, string $userId, string $email, Ref8|string|null $group = NULL, Ref9|string|null $tag = NULL): bool {
        return static::$_veneerInstance->isUserSubscribed(...func_get_args());
    }
    public static function isSubscribed(Ref5|string $source, string $email, Ref8|string|null $group = NULL, Ref9|string|null $tag = NULL): bool {
        return static::$_veneerInstance->isSubscribed(...func_get_args());
    }
    public static function updateDisciple(Ref5|string $source, Ref10 $request): Ref11 {
        return static::$_veneerInstance->updateDisciple(...func_get_args());
    }
    public static function updateUser(Ref5|string $source, string $userId, string $email, Ref10 $request): Ref11 {
        return static::$_veneerInstance->updateUser(...func_get_args());
    }
    public static function update(Ref5|string $source, string $email, Ref10 $request): Ref11 {
        return static::$_veneerInstance->update(...func_get_args());
    }
    public static function updateDiscipleAll(Ref10 $request): array {
        return static::$_veneerInstance->updateDiscipleAll(...func_get_args());
    }
    public static function updateUserAll(string $userId, string $email, Ref10 $request): array {
        return static::$_veneerInstance->updateUserAll(...func_get_args());
    }
    public static function updateAll(string $email, Ref10 $request): array {
        return static::$_veneerInstance->updateAll(...func_get_args());
    }
    public static function unsubscribeDisciple(Ref5|string $source): Ref11 {
        return static::$_veneerInstance->unsubscribeDisciple(...func_get_args());
    }
    public static function unsubscribeUser(Ref5|string $source, string $userId, string $email): Ref11 {
        return static::$_veneerInstance->unsubscribeUser(...func_get_args());
    }
    public static function unsubscribe(Ref5|string $source, string $email): Ref11 {
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
    public static function getDiscipleMemberInfo(Ref5|string $source, bool $force = false): ?Ref14 {
        return static::$_veneerInstance->getDiscipleMemberInfo(...func_get_args());
    }
    public static function refreshDiscipleMemberInfo(Ref5|string $source): ?Ref14 {
        return static::$_veneerInstance->refreshDiscipleMemberInfo(...func_get_args());
    }
    public static function refreshDiscipleMemberInfoAll(): array {
        return static::$_veneerInstance->refreshDiscipleMemberInfoAll();
    }
    public static function getUserMemberInfo(Ref5|string $source, string $userId, string $email, bool $force = false): ?Ref14 {
        return static::$_veneerInstance->getUserMemberInfo(...func_get_args());
    }
    public static function refreshUserMemberInfo(Ref5|string $source, string $userId, string $email): ?Ref14 {
        return static::$_veneerInstance->refreshUserMemberInfo(...func_get_args());
    }
    public static function refreshUserMemberInfoAll(string $userId, string $email): array {
        return static::$_veneerInstance->refreshUserMemberInfoAll(...func_get_args());
    }
    public static function getMemberInfo(Ref5|string $source, string $email, bool $force = false): ?Ref14 {
        return static::$_veneerInstance->getMemberInfo(...func_get_args());
    }
    public static function refreshMemberInfo(Ref5|string $source, string $email): ?Ref14 {
        return static::$_veneerInstance->refreshMemberInfo(...func_get_args());
    }
    public static function refreshMemberInfoAll(string $email): array {
        return static::$_veneerInstance->refreshMemberInfoAll(...func_get_args());
    }
};
