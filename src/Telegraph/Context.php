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
use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;
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

    public function getUserMemberInfo(
        string|SourceReference $source,
        string $userId,
        string $email
    ): ?MemberInfo {
        return $this->load($source)?->getUserMemberInfo($userId, $email);
    }

    public function getMemberInfo(
        string|SourceReference $source,
        string $email
    ): ?MemberInfo {
        return $this->load($source)?->getMemberInfo($email);
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
