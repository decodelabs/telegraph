# Telegraph — Package Specification

> **Cluster:** `integration`
> **Language:** `php`
> **Milestone:** `m5`
> **Repo:** `https://github.com/decodelabs/telegraph`
> **Role:** Mailing List manager

## Overview

### Purpose

Telegraph provides a simple and opinionated way to manage and interact with third party mailing list services, normalising the intricacies of each service into a consistent API. It offers a unified interface for subscribing, updating, and unsubscribing members across different mailing list providers.

Key features:
- **Unified API**: Consistent interface across different mailing list services
- **Source management**: Multiple mailing list sources with independent configuration
- **Member operations**: Subscribe, update, and unsubscribe members
- **List information**: Fetch list details, groups, tags, and consent fields
- **Member information**: Fetch member details including status, groups, and tags
- **Caching**: PSR-6 cache support for reducing API calls
- **User stores**: Non-volatile storage for user-associated data
- **Disciple integration**: Automatic user data population from Disciple service
- **Component abstraction**: Simplified interface focusing on common operations

### Non-Goals

- Telegraph does not provide a full implementation of all features of each mailing list service.
- It does not handle email sending or email template management.
- It does not provide webhook handling or event processing.
- It does not handle campaign management or automation workflows.
- It does not provide analytics or reporting features.

## Role in the Ecosystem

### Cluster & Positioning

Telegraph belongs to the **integration** cluster, focusing on third-party service integration. It complements other integration packages by providing a unified interface for mailing list management across different providers.

### Usage Contexts

- **Newsletter subscriptions**: Managing newsletter subscriptions across multiple providers
- **User preferences**: Managing user mailing list preferences and groups
- **Consent management**: Handling consent fields and compliance
- **Member synchronization**: Synchronizing member data across services
- **Multi-provider support**: Supporting multiple mailing list providers simultaneously

## Public Surface

### Key Types

- **`Telegraph`** (class): Main service class providing source management and member operations. Implements `Service` for Kingdom integration.

- **`Source`** (class): Source class representing a mailing list source. Extends `SourceReference`. Provides methods for list and member operations.

- **`SourceReference`** (class): Source reference class containing source name and remote ID.

- **`Adapter`** (interface): Adapter interface for mailing list service implementations. Defines methods for list and member operations.

- **`AdapterActionResult`** (class): Adapter action result class containing response and member info.

- **`Config`** (interface): Configuration interface for source configuration. Defines methods for accessing source settings.

- **`Store`** (interface): Store interface for non-volatile storage of list and member information.

- **`Cache`** (class): Cache class for caching list and member information using PSR-6 cache.

- **`MemberDataRequest`** (class): Member data request class for subscription and update operations. Contains email, name, country, language, groups, tags, and consent fields.

- **`SubscriptionResponse`** (class): Subscription response class containing operation result, status, and failure reason.

- **`FailureReason`** (enum): Failure reason enumeration: `EmailInvalid`, `Throttled`, `Compliance`, `ServiceUnavailable`.

- **`Source\ListInfo`** (class): List information class containing list details, groups, tags, and consent fields. Extends `ListReference` and implements `JsonSerializable`.

- **`Source\ListReference`** (class): List reference class containing basic list information (ID, name, dates, URL, member count).

- **`Source\MemberInfo`** (class): Member information class containing member details, status, groups, tags, and consent. Implements `JsonSerializable`.

- **`Source\GroupInfo`** (class): Group information class containing group details and category information. Implements `JsonSerializable`.

- **`Source\TagInfo`** (class): Tag information class containing tag ID and name. Implements `JsonSerializable`.

- **`Source\ConsentField`** (class): Consent field class containing consent field details and type. Implements `JsonSerializable`.

- **`Source\ConsentType`** (enum): Consent type enumeration: `Email`, `Phone`, `Sms`, `Mail`, `Advertising`, `Other`.

- **`Source\EmailType`** (enum): Email type enumeration: `Html`, `Text`.

- **`Source\MemberStatus`** (enum): Member status enumeration: `Subscribed`, `Pending`, `Unsubscribed`, `Invalid`, `Archived`.

- **`Dovetail\Config\Telegraph`** (class): Dovetail configuration implementation for Telegraph.

- **`Commandment\Action\Telegraph\Refresh`** (class): Commandment action for refreshing cache.

- **`Commandment\Action\Telegraph\Info`** (class): Commandment action for showing list info.

- **`Commandment\Action\Telegraph\Probe`** (class): Commandment action for probing all lists.

### Main Entry Points

**Telegraph Service:**
- `new Telegraph(?Config $config, ?Store $store, ?Cache $cache, Archetype $archetype, ?Disciple $disciple = null)` — Constructor
- `Telegraph::provideService(ContainerAdapter $container): static` — Service provider method
- `$telegraph->config` — Configuration instance (public property)
- `$telegraph->store` — Store instance (public property)
- `$telegraph->cache` — Cache instance (public property)

**Source Management:**
- `$telegraph->loadDefault(): ?Source` — Load default source
- `$telegraph->load(string|SourceReference $name): ?Source` — Load source by name
- `$telegraph->loadAll(): array` — Load all sources
- `$telegraph->getSourceNames(): array` — Get source names
- `$telegraph->hasSource(string $name): bool` — Check if source exists
- `$telegraph->loadDefaultAdapter(): ?Adapter` — Load default adapter
- `$telegraph->loadAdapterFor(string $name): ?Adapter` — Load adapter for source
- `$telegraph->loadAdapter(string $name, array $settings = []): Adapter` — Load adapter by name

**List Information:**
- `$telegraph->getListInfo(string|SourceReference $source): ?ListInfo` — Get list info
- `$telegraph->refreshListInfo(string|SourceReference $source): ?ListInfo` — Refresh list info
- `$telegraph->refreshListInfoAll(): array` — Refresh all list info

**Groups:**
- `$telegraph->getGroupOptions(string|SourceReference $source, bool $forceCategories = false, ?string $noCategoryLabel = null): array` — Get group options
- `$telegraph->getCategorizedGroupOptions(string|SourceReference $source, ?string $noCategoryLabel = null): array` — Get categorized group options
- `$telegraph->getGroups(string|SourceReference $source): array` — Get groups
- `$telegraph->getGroup(string|SourceReference $source, string $groupId): ?GroupInfo` — Get group
- `$telegraph->getGroupName(string|SourceReference $source, string $groupId, bool $forceCategories = false, ?string $noCategoryLabel = null): ?string` — Get group name
- `$telegraph->getGroupCategoryOptions(string|SourceReference $source): array` — Get group category options
- `$telegraph->getGroupCategoryName(string|SourceReference $source, string $categoryId, ?string $noCategoryLabel = null): string` — Get category name
- `$telegraph->getGroupCategoryNameByGroupId(string|SourceReference $source, string $groupId, ?string $noCategoryLabel = null): ?string` — Get category name by group ID

**Tags:**
- `$telegraph->getTagOptions(string|SourceReference $source): array` — Get tag options
- `$telegraph->getTags(string|SourceReference $source): array` — Get tags
- `$telegraph->getTag(string|SourceReference $source, string $tagId): ?TagInfo` — Get tag
- `$telegraph->getTagName(string|SourceReference $source, string $tagId): ?string` — Get tag name

**Consent Fields:**
- `$telegraph->getConsentFields(string|SourceReference $source): array` — Get consent fields
- `$telegraph->getConsentField(string|SourceReference $source, string $consentFieldId): ?ConsentField` — Get consent field
- `$telegraph->getTypeConsentField(string|SourceReference $source, ConsentType $type): ?ConsentField` — Get consent field by type

**Subscription Operations:**
- `$telegraph->subscribe(string|SourceReference $source, MemberDataRequest $request): SubscriptionResponse` — Subscribe member
- `$telegraph->subscribeUser(string|SourceReference $source, string $userId, MemberDataRequest $request): SubscriptionResponse` — Subscribe user
- `$telegraph->subscribeDisciple(string|SourceReference $source, ?MemberDataRequest $request = null): SubscriptionResponse` — Subscribe Disciple user

**Update Operations:**
- `$telegraph->update(string|SourceReference $source, string $email, MemberDataRequest $request): SubscriptionResponse` — Update member
- `$telegraph->updateUser(string|SourceReference $source, string $userId, string $email, MemberDataRequest $request): SubscriptionResponse` — Update user
- `$telegraph->updateDisciple(string|SourceReference $source, MemberDataRequest $request): SubscriptionResponse` — Update Disciple user
- `$telegraph->updateAll(string $email, MemberDataRequest $request): array` — Update all sources
- `$telegraph->updateUserAll(string $userId, string $email, MemberDataRequest $request): array` — Update all sources for user
- `$telegraph->updateDiscipleAll(MemberDataRequest $request): array` — Update all sources for Disciple

**Unsubscribe Operations:**
- `$telegraph->unsubscribe(string|SourceReference $source, string $email): SubscriptionResponse` — Unsubscribe member
- `$telegraph->unsubscribeUser(string|SourceReference $source, string $userId, string $email): SubscriptionResponse` — Unsubscribe user
- `$telegraph->unsubscribeDisciple(string|SourceReference $source): SubscriptionResponse` — Unsubscribe Disciple user
- `$telegraph->unsubscribeAll(string $email): array` — Unsubscribe all sources
- `$telegraph->unsubscribeUserAll(string $userId, string $email): array` — Unsubscribe all sources for user
- `$telegraph->unsubscribeDiscipleAll(): array` — Unsubscribe all sources for Disciple

**Subscription Status:**
- `$telegraph->isSubscribed(string|SourceReference $source, string $email, string|GroupInfo|null $group = null, string|TagInfo|null $tag = null): bool` — Check if subscribed
- `$telegraph->isUserSubscribed(string|SourceReference $source, string $userId, string $email, string|GroupInfo|null $group = null, string|TagInfo|null $tag = null): bool` — Check if user subscribed
- `$telegraph->isDiscipleSubscribed(string|SourceReference $source, string|GroupInfo|null $group = null, string|TagInfo|null $tag = null): bool` — Check if Disciple subscribed

**Member Information:**
- `$telegraph->getMemberInfo(string|SourceReference $source, string $email, bool $force = false): ?MemberInfo` — Get member info
- `$telegraph->getUserMemberInfo(string|SourceReference $source, string $userId, string $email, bool $force = false): ?MemberInfo` — Get user member info
- `$telegraph->getDiscipleMemberInfo(string|SourceReference $source, bool $force = false): ?MemberInfo` — Get Disciple member info
- `$telegraph->refreshMemberInfo(string|SourceReference $source, string $email): ?MemberInfo` — Refresh member info
- `$telegraph->refreshUserMemberInfo(string|SourceReference $source, string $userId, string $email): ?MemberInfo` — Refresh user member info
- `$telegraph->refreshDiscipleMemberInfo(string|SourceReference $source): ?MemberInfo` — Refresh Disciple member info
- `$telegraph->refreshMemberInfoAll(string $email): array` — Refresh all sources
- `$telegraph->refreshUserMemberInfoAll(string $userId, string $email): array` — Refresh all sources for user
- `$telegraph->refreshDiscipleMemberInfoAll(): array` — Refresh all sources for Disciple

**Source Methods:**
- `$source->getListInfo(): ?ListInfo` — Get list info
- `$source->refreshListInfo(): ?ListInfo` — Refresh list info
- `$source->getGroups(): array` — Get groups
- `$source->getTags(): array` — Get tags
- `$source->getConsentFields(): array` — Get consent fields
- `$source->subscribe(MemberDataRequest $request): SubscriptionResponse` — Subscribe
- `$source->subscribeUser(string $userId, MemberDataRequest $request): SubscriptionResponse` — Subscribe user
- `$source->subscribeDisciple(?MemberDataRequest $request = null): SubscriptionResponse` — Subscribe Disciple
- `$source->update(string $email, MemberDataRequest $request): SubscriptionResponse` — Update
- `$source->updateUser(string $userId, string $email, MemberDataRequest $request): SubscriptionResponse` — Update user
- `$source->updateDisciple(MemberDataRequest $request): SubscriptionResponse` — Update Disciple
- `$source->unsubscribe(string $email): SubscriptionResponse` — Unsubscribe
- `$source->unsubscribeUser(string $userId, string $email): SubscriptionResponse` — Unsubscribe user
- `$source->unsubscribeDisciple(): SubscriptionResponse` — Unsubscribe Disciple
- `$source->isSubscribed(string $email, string|GroupInfo|null $group = null, string|TagInfo|null $tag = null): bool` — Check if subscribed
- `$source->isUserSubscribed(string $userId, string $email, string|GroupInfo|null $group = null, string|TagInfo|null $tag = null): bool` — Check if user subscribed
- `$source->isDiscipleSubscribed(string|GroupInfo|null $group = null, string|TagInfo|null $tag = null): bool` — Check if Disciple subscribed
- `$source->getMemberInfo(string $email, bool $force = false): ?MemberInfo` — Get member info
- `$source->getUserMemberInfo(string $userId, string $email, bool $force = false): ?MemberInfo` — Get user member info
- `$source->getDiscipleMemberInfo(bool $force = false): ?MemberInfo` — Get Disciple member info
- `$source->refreshMemberInfo(string $email): ?MemberInfo` — Refresh member info
- `$source->refreshUserMemberInfo(string $userId, string $email): ?MemberInfo` — Refresh user member info
- `$source->refreshDiscipleMemberInfo(): ?MemberInfo` — Refresh Disciple member info

**MemberDataRequest:**
- `new MemberDataRequest(?string $email = null, ?string $firstName = null, ?string $lastName = null, ?string $country = null, ?string $language = null, array $groups = [], array $tags = [], array $consent = [])` — Constructor
- `$request->email` — Email address (public property)
- `$request->firstName` — First name (public property)
- `$request->lastName` — Last name (public property)
- `$request->fullName` — Full name (readonly property)
- `$request->country` — Country code (public property, 2-letter ISO)
- `$request->language` — Language code (public property)
- `$request->emailType` — Email type (public property)
- `$request->groups` — Groups array (public property)
- `$request->tags` — Tags array (public property)
- `$request->consent` — Consent array (public property)
- `$request->addGroup(string $id): void` — Add group
- `$request->removeGroup(string $id): void` — Remove group
- `$request->setGroupIntent(string $id, ?bool $intent): void` — Set group intent
- `$request->getGroupIntent(string $id): ?bool` — Get group intent
- `$request->hasGroup(string $id): bool` — Check if has group
- `$request->unsetGroup(string $id): void` — Unset group
- `$request->addTag(string $id): void` — Add tag
- `$request->removeTag(string $id): void` — Remove tag
- `$request->setTagIntent(string $id, ?bool $intent): void` — Set tag intent
- `$request->getTagIntent(string $id): ?bool` — Get tag intent
- `$request->hasTag(string $id): bool` — Check if has tag
- `$request->unsetTag(string $id): void` — Unset tag
- `$request->addConsent(string $id): void` — Add consent
- `$request->removeConsent(string $id): void` — Remove consent
- `$request->setConsentIntent(string $id, ?bool $intent): void` — Set consent intent
- `$request->getConsentIntent(string $id): ?bool` — Get consent intent
- `$request->hasConsent(string $id): bool` — Check if has consent
- `$request->unsetConsent(string $id): void` — Unset consent

**SubscriptionResponse:**
- `new SubscriptionResponse(SourceReference $source, bool $success = false, ?FailureReason $failureReason = null, ?string $manualInputUrl = null, ?MemberStatus $status = null, ?Mailbox $mailbox = null)` — Constructor
- `$response->source` — Source reference (public property)
- `$response->success` — Success flag (public property)
- `$response->failureReason` — Failure reason (public property)
- `$response->manualInputUrl` — Manual input URL (public property)
- `$response->status` — Member status (public property)
- `$response->mailbox` — Mailbox (public property)
- `$response->subscribed` — Subscribed flag (readonly property)
- `$response->requiresManualInput` — Requires manual input flag (readonly property)

## Dependencies

### Decode Labs

- **`decodelabs/archetype`**: Used for adapter resolution and custom adapter discovery.
- **`decodelabs/coercion`**: Used for type coercion in data handling.
- **`decodelabs/exceptional`**: Used for exception handling throughout the package.
- **`decodelabs/kingdom`**: Used for service container integration (`Service` interface).
- **`decodelabs/nuance`**: Used for debugging and inspection capabilities.
- **`decodelabs/relay`**: Used for `Mailbox` class in subscription responses.

### External

- **PHP**: See `composer.json` for supported PHP versions.
- **`nesbot/carbon`**: Required for date/time handling (CarbonImmutable).
- **`psr/cache`**: Required for PSR-6 cache interface support.

### Optional

- **`decodelabs/dovetail`**: Detected at runtime if installed, used for configuration integration (suggested dependency).
- **`decodelabs/stash`**: Detected at runtime if installed, used as default cache implementation (PSR-6 compatible).
- **`decodelabs/disciple`**: Detected at runtime if installed, used for automatic user data population in Disciple-oriented methods.
- **`decodelabs/commandment`**: Detected at runtime if installed, used for CLI actions (dev dependency).

## Behaviour & Contracts

### Invariants

- Sources are loaded lazily and cached per Telegraph instance.
- List info is cached and fetched from store before adapter if available.
- Member info is cached and fetched from store before adapter if available.
- User-oriented methods require Store for persistence.
- Disciple-oriented methods require Disciple service.
- Country codes must be 2-letter ISO codes.
- Language codes are normalized to lowercase.
- Groups and tags use boolean values: `true` = add/enable, `false` = remove/disable.

### Input & Output Contracts

**Source Loading:**
- Sources loaded by name from configuration.
- Adapters resolved via Archetype using adapter name.
- Source instances cached per Telegraph instance.
- Returns `null` if source not found or configuration invalid.

**List Information:**
- List info fetched from cache first, then store, then adapter.
- List info cached after fetch.
- Refresh operations bypass cache and store, fetch directly from adapter.
- List info contains groups, tags, and consent fields.

**Member Operations:**
- Subscribe: Creates new subscription or updates existing if already subscribed (user methods).
- Update: Updates existing member data atomically.
- Unsubscribe: Removes member from list.
- Operations return `SubscriptionResponse` with success status and failure reason.

**Member Information:**
- Member info fetched from cache first, then store (user methods), then adapter.
- Member info cached after fetch.
- Refresh operations bypass cache and store, fetch directly from adapter.
- Member info filtered by subscription status (returns `null` if not subscribed unless `force = true`).

**Groups and Tags:**
- Groups and tags managed via boolean intents in `MemberDataRequest`.
- `true` = add/enable, `false` = remove/disable, `null` = no change.
- Groups can be categorized (category ID and name).
- Group options can be forced to show categories.

**Consent Fields:**
- Consent fields represent consent requirements for different communication types.
- Consent types: Email, Phone, Sms, Mail, Advertising, Other.
- Consent type inferred from description if not specified.

**Caching:**
- List info cached indefinitely (until refresh).
- Member info cached for 2 hours (CarbonInterval).
- Cache uses PSR-6 interface, supports any PSR-6 implementation.
- Cache keys: source name for list info, `source|md5(email)` for member info.

**Stores:**
- Stores provide non-volatile storage for list and member information.
- Used for user-oriented operations to persist data across requests.
- Store operations: store, fetch, clear for list and member info.
- Store keys: source name for list info, source name + user ID for member info.

**Disciple Integration:**
- Disciple methods automatically populate member data from Disciple service.
- Email, firstName, lastName, country, language populated from Disciple.
- User ID taken from Disciple active ID.
- Requires Disciple service to be available.

## Error Handling

- **Invalid email**: `MemberDataRequest` throws `InvalidArgument` exception if email required but not provided.
- **Invalid country code**: `MemberDataRequest` throws `InvalidArgument` exception if country code not 2 letters.
- **Source not found**: `load()` returns `null` if source not found.
- **Adapter not found**: `loadAdapter()` throws `NotFound` exception if adapter not found.
- **Disciple unavailable**: Disciple methods throw `ComponentUnavailable` exception if Disciple service not available.
- **Operation failure**: Operations return `SubscriptionResponse` with `success = false` and `failureReason`.

## Configuration & Extensibility

### Custom Configuration

Implement `Config` interface:

```php
use DecodeLabs\Telegraph\Config;

class MyConfig implements Config
{
    public function getDefaultSourceName(): ?string
    {
        // Return default source name
    }

    public function getSourceNames(): array
    {
        // Return all source names
    }

    public function getSourceAdapter(string $name): ?string
    {
        // Return adapter name for source
    }

    public function getSourceRemoteId(string $name): ?string
    {
        // Return remote list ID for source
    }

    public function getSourceSettings(string $name): array
    {
        // Return adapter settings for source
    }
}
```

Register via Kingdom:

```php
use DecodeLabs\Monarch;
use DecodeLabs\Telegraph\Config;

Monarch::getKingdom()->container->setType(Config::class, MyConfig::class);
```

### Custom Adapters

Implement `Adapter` interface:

```php
use DecodeLabs\Telegraph\Adapter;
use DecodeLabs\Telegraph\SourceReference;
use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;
use DecodeLabs\Telegraph\MemberDataRequest;
use DecodeLabs\Telegraph\AdapterActionResult;

class MyAdapter implements Adapter
{
    public function __construct(array $settings)
    {
        // Initialize adapter with settings
    }

    public function fetchAllListReferences(): array
    {
        // Return all list references
    }

    public function fetchListInfo(SourceReference $source): ?ListInfo
    {
        // Fetch and return list info
    }

    public function subscribe(SourceReference $source, ListInfo $listInfo, MemberDataRequest $request): AdapterActionResult
    {
        // Subscribe member and return result
    }

    public function update(SourceReference $source, ListInfo $listInfo, string $email, MemberDataRequest $request): AdapterActionResult
    {
        // Update member and return result
    }

    public function unsubscribe(SourceReference $source, ListInfo $listInfo, string $email): AdapterActionResult
    {
        // Unsubscribe member and return result
    }

    public function fetchMemberInfo(SourceReference $source, ListInfo $listInfo, string $email): ?MemberInfo
    {
        // Fetch and return member info
    }
}
```

Register adapter via Archetype for `Adapter` interface with adapter name.

### Custom Stores

Implement `Store` interface:

```php
use DecodeLabs\Telegraph\Store;
use DecodeLabs\Telegraph\SourceReference;
use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;

class MyStore implements Store
{
    public function storeListInfo(SourceReference $source, ListInfo $list): void
    {
        // Store list info
    }

    public function fetchListInfo(SourceReference $source): ?ListInfo
    {
        // Fetch list info
    }

    public function clearListInfo(SourceReference $source): void
    {
        // Clear list info
    }

    public function storeMemberInfo(SourceReference $source, string $userId, MemberInfo $member): void
    {
        // Store member info
    }

    public function fetchMemberInfo(SourceReference $source, string $userId): ?MemberInfo
    {
        // Fetch member info
    }

    public function clearMemberInfo(SourceReference $source, string $userId): void
    {
        // Clear member info
    }
}
```

Set via `$telegraph->store = new MyStore()`.

### Custom Cache

Provide PSR-6 cache implementation:

```php
use Psr\Cache\CacheItemPoolInterface;

$telegraph->cache = new MyPsrCachePool();
```

## Interactions with Other Packages

- **Relay**: Used for `Mailbox` class in subscription responses. Provides email address handling.
- **Archetype**: Used for adapter resolution. Adapters registered via Archetype for `Adapter` interface.
- **Kingdom**: Used for service container integration. Telegraph implements `Service` interface.
- **Disciple**: Detected at runtime if installed, used for automatic user data population in Disciple-oriented methods.
- **Stash**: Detected at runtime if installed, used as default cache implementation.
- **Dovetail**: Detected at runtime if installed, used for configuration integration.
- **Commandment**: Detected at runtime if installed, used for CLI actions (dev dependency).

## Usage Examples

### Basic Configuration

```php
use DecodeLabs\Monarch;
use DecodeLabs\Telegraph;
use DecodeLabs\Dovetail\Env;

// Configuration (Dovetail)
return [
    'main' => [
        'adapter' => 'Mailchimp',
        'apiKey' => Env::asString('MAILCHIMP_API_KEY'),
        'list' => 'abc123abc123'
    ]
];

// Load service
$telegraph = Monarch::getService(Telegraph::class);
```

### Subscribe Member

```php
use DecodeLabs\Telegraph\MemberDataRequest;

$request = new MemberDataRequest(
    email: 'test@example.com',
    firstName: 'Test',
    lastName: 'User',
    country: 'GB',
    language: 'en',
    groups: [
        '1234567890' => true,
    ],
    tags: [
        'test' => true,
    ]
);

$response = $telegraph->subscribe('main', $request);

if ($response->success) {
    echo 'Subscription successful';
}
```

### Update Member

```php
use DecodeLabs\Telegraph\MemberDataRequest;

// Change name and email
$request = new MemberDataRequest(
    email: 'someone-else@example.com',
    firstName: 'Someone',
    lastName: 'Else',
);

$response = $telegraph->update('main', 'test@example.com', $request);

// Update groups and tags
$request = new MemberDataRequest(
    groups: [
        '1234567890' => false,
        '4567890123' => true,
    ],
    tags: [
        'test' => false,
        'new-tag' => true,
    ]
);

$response = $telegraph->update('main', 'someone-else@example.com', $request);
```

### Unsubscribe Member

```php
$response = $telegraph->unsubscribe('main', 'someone-else@example.com');
```

### Get Member Info

```php
$memberInfo = $telegraph->getMemberInfo('main', 'test@example.com');

echo $memberInfo->email;
echo $memberInfo->firstName;
echo $memberInfo->lastName;
echo $memberInfo->country;
echo $memberInfo->language;

foreach ($memberInfo->groups as $group) {
    echo $group->name;
}

foreach ($memberInfo->tags as $tag) {
    echo $tag->name;
}
```

### Get List Info

```php
$listInfo = $telegraph->getListInfo('main');

echo $listInfo->name;
echo $listInfo->memberCount;

foreach ($listInfo->groups as $group) {
    echo $group->name;
}

foreach ($listInfo->tags as $tag) {
    echo $tag->name;
}
```

### User-Oriented Operations

```php
$telegraph->store = new MyStore();

$telegraph->subscribeUser(
    source: 'main',
    userId: '1234567890',
    request: new MemberDataRequest(
        email: 'test@example.com',
        firstName: 'Test',
        lastName: 'User',
    )
);

$telegraph->updateUser(
    source: 'main',
    userId: '1234567890',
    email: 'test@example.com',
    request: new MemberDataRequest(
        firstName: 'Another',
        lastName: 'User',
    )
);

$telegraph->unsubscribeUser(
    source: 'main',
    userId: '1234567890',
    email: 'test@example.com'
);

$info = $telegraph->getUserMemberInfo(
    source: 'main',
    userId: '1234567890',
    email: 'test@example.com'
);
```

### Disciple Integration

```php
// Subscribe Disciple user (auto-populates data)
$telegraph->subscribeDisciple('main');

// Update Disciple user with groups/tags
$telegraph->updateDisciple('main', new MemberDataRequest(
    groups: [
        '1234567890' => true,
    ],
    tags: [
        'test' => true,
    ]
));

// Unsubscribe Disciple user
$telegraph->unsubscribeDisciple('main');

// Check if Disciple subscribed
if ($telegraph->isDiscipleSubscribed('main')) {
    echo 'User is subscribed';
}
```

### Check Subscription Status

```php
// Check if subscribed
if ($telegraph->isSubscribed('main', 'test@example.com')) {
    echo 'Subscribed';
}

// Check if subscribed to specific group
if ($telegraph->isSubscribed('main', 'test@example.com', group: '1234567890')) {
    echo 'Subscribed to group';
}

// Check if subscribed with specific tag
if ($telegraph->isSubscribed('main', 'test@example.com', tag: 'test')) {
    echo 'Has tag';
}
```

### Working with Groups

```php
// Get group options
$options = $telegraph->getGroupOptions('main');

// Get categorized group options
$categorized = $telegraph->getCategorizedGroupOptions('main');

// Get group name
$name = $telegraph->getGroupName('main', '1234567890');

// Get group category name
$category = $telegraph->getGroupCategoryName('main', 'category-id');
```

### Working with Tags

```php
// Get tag options
$options = $telegraph->getTagOptions('main');

// Get tag name
$name = $telegraph->getTagName('main', 'tag-id');
```

### Working with Consent Fields

```php
// Get consent fields
$fields = $telegraph->getConsentFields('main');

// Get consent field by type
$emailConsent = $telegraph->getTypeConsentField('main', ConsentType::Email);

// Add consent to request
$request = new MemberDataRequest(
    email: 'test@example.com',
    consent: [
        'consent-field-id' => true,
    ]
);
```

### Source Operations

```php
// Load source
$source = $telegraph->load('main');

// Get list info
$listInfo = $source->getListInfo();

// Subscribe
$response = $source->subscribe($request);

// Update
$response = $source->update('test@example.com', $request);

// Unsubscribe
$response = $source->unsubscribe('test@example.com');

// Check subscription
if ($source->isSubscribed('test@example.com')) {
    echo 'Subscribed';
}

// Get member info
$memberInfo = $source->getMemberInfo('test@example.com');
```

## Implementation Notes (for Contributors)

### Source Loading

- Sources loaded lazily on first access.
- Source instances cached per Telegraph instance.
- Adapters resolved via Archetype using adapter name from configuration.
- Adapter names capitalized (e.g., `Mailchimp` → `Mailchimp`).

### List Information Caching

- List info fetched in order: cache → store → adapter.
- Cache checked first, returns cached value if available.
- Store checked if cache miss, returns stored value if available.
- Adapter called if cache and store miss.
- List info cached after fetch (indefinitely).
- Refresh operations bypass cache and store, fetch directly from adapter.

### Member Information Caching

- Member info fetched in order: cache → store (user methods) → adapter.
- Cache checked first, returns cached value if available.
- Store checked if cache miss (user methods only), returns stored value if available.
- Adapter called if cache and store miss.
- Member info cached after fetch (2 hours TTL).
- Refresh operations bypass cache and store, fetch directly from adapter.
- Member info filtered by subscription status (returns `null` if not subscribed unless `force = true`).

### Subscription Logic

- Subscribe operations check if member already subscribed (user methods).
- If already subscribed, performs update instead of subscribe.
- Operations return `AdapterActionResult` containing response and member info.
- Member info stored in cache and store (user methods) on success.
- Member info cleared from cache and store on failure.

### Groups and Tags

- Groups and tags managed via boolean intents in `MemberDataRequest`.
- `true` = add/enable, `false` = remove/disable, `null` = no change.
- Groups can be categorized (category ID and name).
- Group options can be forced to show categories via `forceCategories` parameter.
- Group names can include category prefix when categories are used.

### Consent Fields

- Consent fields represent consent requirements for different communication types.
- Consent types: Email, Phone, Sms, Mail, Advertising, Other.
- Consent type inferred from description if not specified (heuristic matching).
- Consent fields stored in `MemberInfo` and `MemberDataRequest`.

### Cache Implementation

- Cache uses PSR-6 interface (`CacheItemPoolInterface`).
- List info cached with key: source name.
- Member info cached with key: `source|md5(email)`.
- Member info TTL: 2 hours (CarbonInterval).
- Cache can be `null` (no caching).

### Store Implementation

- Stores provide non-volatile storage for list and member information.
- Used for user-oriented operations to persist data across requests.
- Store keys: source name for list info, source name + user ID for member info.
- Store can be `null` (no persistence).

### Disciple Integration

- Disciple methods automatically populate member data from Disciple service.
- Email, firstName, lastName, country, language populated from Disciple.
- User ID taken from Disciple active ID.
- Requires Disciple service to be available (throws exception if not).

### Adapter Pattern

- Adapters implement `Adapter` interface.
- Adapters registered via Archetype for `Adapter` interface.
- Adapter names capitalized and resolved via Archetype.
- Adapters receive settings array from configuration.
- Adapters return `AdapterActionResult` containing response and optional member info.

### Failure Handling

- Operations return `SubscriptionResponse` with success status.
- Failure reasons: `EmailInvalid`, `Throttled`, `Compliance`, `ServiceUnavailable`.
- Manual input URL provided if operation requires manual intervention.
- Member status included in response.

## Testing & Quality

**Current Status:**
- Code quality: 4.5/5
- README quality: 4/5
- Documentation: 0/5 (no formal docs yet)
- Tests: 0/5 (no test suite yet)

**Testing Considerations:**
- Source loading should be tested for:
  - Valid source names
  - Invalid source names
  - Adapter resolution
  - Configuration handling

- List operations should be tested for:
  - Fetching list info
  - Refreshing list info
  - Caching behavior
  - Store integration

- Member operations should be tested for:
  - Subscribe (new and existing)
  - Update
  - Unsubscribe
  - Member info fetching
  - Caching behavior
  - Store integration

- Groups and tags should be tested for:
  - Group/tag management
  - Category handling
  - Options generation

- Consent fields should be tested for:
  - Consent field fetching
  - Type inference
  - Consent management

- Disciple integration should be tested for:
  - Automatic data population
  - User ID handling
  - Error handling when Disciple unavailable

- Edge cases should be tested for:
  - Invalid email addresses
  - Invalid country codes
  - Missing configuration
  - Adapter failures
  - Cache/store failures
  - Network failures

## Roadmap & Future Ideas

- **More adapters**: Support for additional mailing list providers
- **Webhook handling**: Support for webhook processing and event handling
- **Campaign management**: Support for campaign creation and management
- **Analytics**: Support for analytics and reporting
- **Batch operations**: Support for batch subscribe/update/unsubscribe operations
- **Sync operations**: Support for synchronizing data across multiple sources
- **Better error messages**: More detailed error messages for operation failures
- **Performance optimization**: Caching and optimization for large-scale operations

## References

- Package repository: https://github.com/decodelabs/telegraph
- Composer package: https://packagist.org/packages/decodelabs/telegraph
- Related packages:
  - `decodelabs/relay` — Email address handling
  - `decodelabs/archetype` — Adapter resolution
  - `decodelabs/kingdom` — Service container
  - `decodelabs/disciple` — User data integration
  - `decodelabs/stash` — Cache implementation
  - `decodelabs/dovetail` — Configuration integration

