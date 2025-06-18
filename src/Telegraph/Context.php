<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Archetype;
use DecodeLabs\Dovetail;
use DecodeLabs\Dovetail\Config\Telegraph as TelegraphConfig;
use DecodeLabs\Exceptional;
use DecodeLabs\Monarch;
use DecodeLabs\Stash;
use DecodeLabs\Telegraph;
use DecodeLabs\Telegraph\Source\GroupInfo;
use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;
use DecodeLabs\Telegraph\Source\TagInfo;
use DecodeLabs\Veneer;
use Psr\Cache\CacheItemPoolInterface;

class Context
{
    /**
     * @var array<string,Source>
     */
    protected array $sources = [];

    protected ?Config $config = null;
    protected ?Cache $cache = null;
    protected Store|false|null $store = false;


    public function setConfig(
        ?Config $config
    ): void {
        $this->config = $config;
    }

    public function getConfig(): ?Config
    {
        if (
            $this->config === null &&
            class_exists(Dovetail::class)
        ) {
            $this->config = TelegraphConfig::load();
        }

        return $this->config;
    }

    public function setCache(
        Cache|CacheItemPoolInterface $cache
    ): void {
        if($cache instanceof CacheItemPoolInterface) {
            $cache = new Cache($cache);
        }

        $this->cache = $cache;
    }

    public function getCache(): Cache {
        if(isset($this->cache)) {
            return $this->cache;
        }

        if(class_exists(Stash::class)) {
            return $this->cache = new Cache(
                Stash::load(self::class)
            );
        }

        return $this->cache = new Cache(null);
    }

    public function setStore(
        Store $store
    ): void {
        $this->store = $store;
    }

    public function getStore(): ?Store
    {
        if($this->store !== false) {
            return $this->store;
        }

        if(Monarch::$container->has(Store::class)) {
            $store = Monarch::$container->get(Store::class);

            if($store instanceof Store) {
                return $this->store = $store;
            }
        }

        return $this->store = null;
    }

    public function loadDefault(): ?Source
    {
        $config = $this->getConfig();

        if(!$sourceName = $config?->getDefaultSourceName()) {
            return null;
        }

        return $this->load($sourceName);
    }

    public function load(
        string|SourceReference $name
    ): ?Source {
        if($name instanceof Source) {
            return $name;
        }

        if($name instanceof SourceReference) {
            $name = $name->name;
        }

        if (isset($this->sources[$name])) {
            return $this->sources[$name];
        }

        $adapter = $this->loadAdapterFor($name);
        $remoteId = $this->getConfig()?->getSourceRemoteId($name);

        if(
            $remoteId === null ||
            $adapter === null
        ) {
            return null;
        }

        $source = new Source(
            name: $name,
            remoteId: $remoteId,
            adapter: $adapter,
            cache: $this->getCache(),
            store: $this->getStore(),
        );

        return $this->sources[$name] = $source;
    }

    /**
     * @return array<string,Source>
     */
    public function loadAll(): array
    {
        $names = $this->getConfig()?->getSourceNames() ?? [];

        foreach($names as $name) {
            $this->load($name);
        }

        return $this->sources;
    }

    /**
     * @return array<string>
     */
    public function getSourceNames(): array
    {
        return $this->getConfig()?->getSourceNames() ?? [];
    }

    public function hasSource(
        string $name
    ): bool {
        if(isset($this->sources[$name])) {
            return true;
        }

        return (bool) $this->getConfig()?->getSourceRemoteId($name);
    }



    public function loadDefaultAdapter(): ?Adapter
    {
        $config = $this->getConfig();

        if(!$sourceName = $config?->getDefaultSourceName()) {
            return null;
        }

        return $this->loadAdapterFor($sourceName);
    }

    public function loadAdapterFor(
        string $name
    ): ?Adapter {
        if (!$config = $this->getConfig()) {
            return null;
        }

        if(!$adapterName = $config->getSourceAdapter($name)) {
            return null;
        }

        $settings = $config->getSourceSettings($name);
        return $this->loadAdapter($adapterName, $settings);
    }

    /**
     * @param array<string,mixed> $settings
     */
    public function loadAdapter(
        string $name,
        array $settings = []
    ): Adapter {
        $name = ucfirst($name);

        if(!$class = Archetype::tryResolve(Adapter::class, $name)) {
            throw Exceptional::NotFound(
                'Telegraph adapter not found: ' . $name
            );
        }

        return new $class($settings);
    }

    protected function normalizeSourceReference(
        string|SourceReference $source
    ): SourceReference {
        if($source instanceof SourceReference) {
            return $source;
        }

        if(isset($this->sources[$source])) {
            return $this->sources[$source];
        }

        return new SourceReference($source, '--');
    }




    public function getListInfo(
        string|SourceReference $source
    ): ?ListInfo {
        return $this->load($source)?->getListInfo();
    }

    public function refreshListInfo(
        string|SourceReference $source
    ): ?ListInfo {
        return $this->load($source)?->refreshListInfo();
    }

    /**
     * @return array<string,?ListInfo>
     */
    public function refreshListInfoAll(): array
    {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $source->refreshListInfo();
        }

        return $output;
    }


    /**
     * @return array<string,string>
     */
    public function getGroupOptions(
        string|SourceReference $source,
        bool $forceCategories = false,
        ?string $noCategoryLabel = null
    ): array {
        return $this->getListInfo($source)?->getGroupOptions($forceCategories, $noCategoryLabel) ?? [];
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function getCategorizedGroupOptions(
        string|SourceReference $source,
        ?string $noCategoryLabel = null
    ): array {
        return $this->getListInfo($source)?->getCategorizedGroupOptions($noCategoryLabel) ?? [];
    }

    public function getGroup(
        string|SourceReference $source,
        string $groupId
    ): ?GroupInfo {
        return $this->getListInfo($source)?->groups[$groupId] ?? null;
    }

    public function getGroupName(
        string|SourceReference $source,
        string $groupId,
        bool $forceCategories = false,
        ?string $noCategoryLabel = null
    ): ?string {
        return $this->getListInfo($source)?->getGroupName($groupId, $forceCategories, $noCategoryLabel);
    }

    /**
     * @return array<string,string>
     */
    public function getGroupCategoryOptions(
        string|SourceReference $source
    ): array {
        return $this->getListInfo($source)?->getGroupCategoryOptions() ?? [];
    }

    public function getGroupCategoryName(
        string|SourceReference $source,
        string $groupId,
        ?string $noCategoryLabel = null
    ): ?string {
        return $this->getListInfo($source)?->getGroupCategoryName($groupId, $noCategoryLabel);
    }

    public function getGroupCategoryNameByGroupId(
        string|SourceReference $source,
        string $groupId,
        ?string $noCategoryLabel = null
    ): ?string {
        return $this->getListInfo($source)?->getGroupCategoryNameByGroupId($groupId, $noCategoryLabel);
    }

    /**
     * @return array<string,string>
     */
    public function getTagOptions(
        string|SourceReference $source
    ): array {
        return $this->getListInfo($source)?->getTagOptions() ?? [];
    }

    public function getTag(
        string|SourceReference $source,
        string $tagId
    ): ?TagInfo {
        return $this->getListInfo($source)?->tags[$tagId] ?? null;
    }

    public function getTagName(
        string|SourceReference $source,
        string $tagId
    ): ?string {
        return $this->getListInfo($source)?->getTagName($tagId);
    }

    public function subscribeDisciple(
        string|SourceReference $source,
        ?MemberDataRequest $request = null
    ): SubscriptionResponse {
        return $this->load($source)
            ?->subscribeDisciple($request)
            ?? $this->newFailureResponse($source);
    }

    public function subscribeUser(
        string|SourceReference $source,
        string $userId,
        MemberDataRequest $request
    ): SubscriptionResponse {
        return $this->load($source)
            ?->subscribeUser($userId, $request)
            ?? $this->newFailureResponse($source);
    }

    public function subscribe(
        string|SourceReference $source,
        MemberDataRequest $request
    ): SubscriptionResponse {
        return $this->load($source)
            ?->subscribe($request)
            ?? $this->newFailureResponse($source);
    }

    public function isDiscipleSubscribed(
        string|SourceReference $source,
        string|GroupInfo|null $group = null,
        string|TagInfo|null $tag = null
    ): bool {
        return $this->load($source)?->isDiscipleSubscribed($group, $tag) ?? false;
    }

    public function isUserSubscribed(
        string|SourceReference $source,
        string $userId,
        string $email,
        string|GroupInfo|null $group = null,
        string|TagInfo|null $tag = null
    ): bool {
        return $this->load($source)?->isUserSubscribed($userId, $email, $group, $tag) ?? false;
    }

    public function isSubscribed(
        string|SourceReference $source,
        string $email,
        string|GroupInfo|null $group = null,
        string|TagInfo|null $tag = null
    ): bool {
        return $this->load($source)?->isSubscribed($email, $group, $tag) ?? false;
    }





    public function updateDisciple(
        string|SourceReference $source,
        MemberDataRequest $request
    ): SubscriptionResponse {
        return $this->load($source)
            ?->updateDisciple($request)
            ?? $this->newFailureResponse($source);
    }

    public function updateUser(
        string|SourceReference $source,
        string $userId,
        string $email,
        MemberDataRequest $request
    ): SubscriptionResponse {
        return $this->load($source)
            ?->updateUser($userId, $email, $request)
            ?? $this->newFailureResponse($source);
    }

    public function update(
        string|SourceReference $source,
        string $email,
        MemberDataRequest $request
    ): SubscriptionResponse {
        return $this->load($source)
            ?->update($email, $request)
            ?? $this->newFailureResponse($source);
    }

    /**
     * @return array<string,SubscriptionResponse>
     */
    public function updateDiscipleAll(
        MemberDataRequest $request
    ): array {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $this->updateDisciple($source, $request);
        }

        return $output;
    }

    /**
     * @return array<string,SubscriptionResponse>
     */
    public function updateUserAll(
        string $userId,
        string $email,
        MemberDataRequest $request
    ): array {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $this->updateUser($source, $userId, $email, $request);
        }

        return $output;
    }

    /**
     * @return array<string,SubscriptionResponse>
     */
    public function updateAll(
        string $email,
        MemberDataRequest $request
    ): array {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $this->update($source, $email, $request);
        }

        return $output;
    }

    public function unsubscribeDisciple(
        string|SourceReference $source,
    ): SubscriptionResponse {
        return $this->load($source)
            ?->unsubscribeDisciple()
            ?? $this->newFailureResponse($source);
    }

    public function unsubscribeUser(
        string|SourceReference $source,
        string $userId,
        string $email
    ): SubscriptionResponse {
        return $this->load($source)
            ?->unsubscribeUser($userId, $email)
            ?? $this->newFailureResponse($source);
    }

    public function unsubscribe(
        string|SourceReference $source,
        string $email
    ): SubscriptionResponse {
        return $this->load($source)
            ?->unsubscribe($email)
            ?? $this->newFailureResponse($source);
    }

    /**
     * @return array<string,SubscriptionResponse>
     */
    public function unsubscribeDiscipleAll(): array
    {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $this->unsubscribeDisciple($source);
        }

        return $output;
    }

    /**
     * @return array<string,SubscriptionResponse>
     */
    public function unsubscribeUserAll(
        string $userId,
        string $email
    ): array {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $this->unsubscribeUser($source, $userId, $email);
        }

        return $output;
    }

    /**
     * @return array<string,SubscriptionResponse>
     */
    public function unsubscribeAll(
        string $email
    ): array {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $this->unsubscribe($source, $email);
        }

        return $output;
    }

    public function getDiscipleMemberInfo(
        string|SourceReference $source,
    ): ?MemberInfo {
        return $this->load($source)?->getDiscipleMemberInfo();
    }

    public function refreshDiscipleMemberInfo(
        string|SourceReference $source,
    ): ?MemberInfo {
        return $this->load($source)?->refreshDiscipleMemberInfo();
    }

    /**
     * @return array<string,?MemberInfo>
     */
    public function refreshDiscipleMemberInfoAll(): array
    {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $source->refreshDiscipleMemberInfo();
        }

        return $output;
    }

    public function getUserMemberInfo(
        string|SourceReference $source,
        string $userId,
        string $email
    ): ?MemberInfo {
        return $this->load($source)?->getUserMemberInfo($userId, $email);
    }

    public function refreshUserMemberInfo(
        string|SourceReference $source,
        string $userId,
        string $email
    ): ?MemberInfo {
        return $this->load($source)?->refreshUserMemberInfo($userId, $email);
    }

    /**
     * @return array<string,?MemberInfo>
     */
    public function refreshUserMemberInfoAll(
        string $userId,
        string $email
    ): array {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $this->refreshUserMemberInfo($source, $userId, $email);
        }

        return $output;
    }

    public function getMemberInfo(
        string|SourceReference $source,
        string $email
    ): ?MemberInfo {
        return $this->load($source)?->getMemberInfo($email);
    }

    public function refreshMemberInfo(
        string|SourceReference $source,
        string $email
    ): ?MemberInfo {
        return $this->load($source)?->refreshMemberInfo($email);
    }

    /**
     * @return array<string,?MemberInfo>
     */
    public function refreshMemberInfoAll(
        string $email
    ): array {
        $output = [];

        foreach($this->loadAll() as $source) {
            $output[$source->name] = $this->refreshMemberInfo($source, $email);
        }

        return $output;
    }

    private function newFailureResponse(
        string|SourceReference $source,
        FailureReason $reason = FailureReason::ServiceUnavailable
    ): SubscriptionResponse {
        return new SubscriptionResponse(
            source: $this->normalizeSourceReference($source),
            success: false,
            failureReason: $reason
        );
    }
}


// Veneer
Veneer\Manager::getGlobalManager()->register(
    Context::class,
    Telegraph::class
);
