# Telegraph

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/telegraph?style=flat)](https://packagist.org/packages/decodelabs/telegraph)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/telegraph.svg?style=flat)](https://packagist.org/packages/decodelabs/telegraph)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/telegraph.svg?style=flat)](https://packagist.org/packages/decodelabs/telegraph)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/telegraph/integrate.yml?branch=develop)](https://github.com/decodelabs/telegraph/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/telegraph?style=flat)](https://packagist.org/packages/decodelabs/telegraph)

### Simple mailing list manager

Telegraph provides a simple and opinionated way to manage and interact with third party mailing list services, normalising the intricacies of each service into a consistent API.

---

## Installation

Install via Composer:

```bash
composer require decodelabs/telegraph
```

## Setup

The system is designed as a simplified abstraction in front of third party mailing list services. It doesn't aim to be a full implementation of all the features of each service, but rather to provide a consistent interface for managing the most common operations.

### Configuration

Configuration can be defined by any class that implements the `DecodeLabs\Telegraph\Config` interface. The package provides a [Dovetail](https://github.com/decodelabs/dovetail) implementation out of the box however it can be easily replaced with a custom implementation.

```php
use DecodeLabs\Telegraph;
Telegraph::setConfig(new MyConfig());
```

The configuration class must provide access to a list of sources which map a source name to an `Adapter` name, settings that allow the adapter to operate and the ID of the specific list to use from that service.

With the default `Dovetail` implementation, your configuration file could look like this:

```php
use DecodeLabs\Dovetail;

return [
    'main' => [
        'adapter' => 'Mailchimp',
        'apiKey' => Dovetail::envString('MAILCHIMP_API_KEY'),
        'list' => 'abc123abc123'
    ]
];
```

### Cache

Telegraph expects to be able to cache the results of API calls to avoid making unnecessary network requests. The main context accepts an instance of a `Psr\Cache\CacheItemPoolInterface` to use for caching, allowing you to use any cache system that implements the PSR-6 interface. If [Stash](https://github.com/decodelabs/stash) is installed, it will be used by default, otherwise:

```php
Telegraph::setCache(new MyPsrCachePool());
```

## Usage

Once configured, Telegraph provides a simple API for interacting with your sources. Either load a specific source by name and operate on it directly, or use the shortcut methods on the main context to streamline common operations.

```php
use DecodeLabs\Telegraph;
use DecodeLabs\Telegraph\MemberDataRequest;

$source = Telegraph::load('main');

$listInfo = $source->getListInfo();
echo $listInfo->name;

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

$response = $source->subscribe($request);

if($response->success) {
    echo 'Subscription successful';
}
```

Or using the shortcut methods:

```php
use DecodeLabs\Telegraph;
use DecodeLabs\Telegraph\MemberDataRequest;

$listInfo = Telegraph::getListInfo('main');
$request = new MemberDataRequest(...);
$response = Telegraph::subscribe('main', $request);
```

Fetch a subscribed member's information:

```php
use DecodeLabs\Telegraph;

$memberInfo = Telegraph::getMemberInfo('main', 'test@example.com');

echo $memberInfo->email;
echo $memberInfo->firstName;
echo $memberInfo->lastName;
echo $memberInfo->country;
echo $memberInfo->language;

foreach($memberInfo->groups as $group) {
    echo $group->name;
}

foreach($memberInfo->tags as $tag) {
    echo $tag->name;
}
```

### Updating a member

The `MemberDataRequest` class can also be used to update a member's data atomically:

```php
use DecodeLabs\Telegraph;
use DecodeLabs\Telegraph\MemberDataRequest;

// Change name and email
$request = new MemberDataRequest(
    email: 'someone-else@example.com',
    firstName: 'Someone',
    lastName: 'Else',
);

$response = Telegraph::update('main', 'test@example.com', $request);
```

Groups and tags can be enabled or disabled by passing a boolean value with the group or tag key:

```php
use DecodeLabs\Telegraph;
use DecodeLabs\Telegraph\MemberDataRequest;

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

$response = Telegraph::update('main', 'someone-else@example.com', $request);
```

Unsubscribing a member just requires the email address:

```php
use DecodeLabs\Telegraph;

$response = Telegraph::unsubscribe('main', 'someone-else@example.com');
```




## User Stores

When in heavy use, it is advisable to store the results of List and Member lookups in non-volatile storage to avoid piling up API requests when caches are cleared on application restart or deployment.

This only makes sense in the context of logged in users where non-volatile storage can be associated with a user account.

When using Telegraph in this way you should use the `*User()` methods instead of the regular methods - this will ensure that the results are stored against the user account and stores and caches are updated accordingly.

Take care not to mix the user and non-user oriented methods where possible - calls without a user ID can not affect the store so a mixed set of calls can leave the store out of sync. Generally, if an operation concerns a user with an account, you should operate with the user oriented methods.

You can provide a custom `Store` implementation to handle saving List and Member information to your database. See the [Store](./src/Telegraph/Store.php) interface for more details.

```php
Telegraph::setStore(new MyStore());

Telegraph::subscribeUser(
    source: 'main',
    userId: '1234567890',
    request: new MemberDataRequest(
        email: 'test@example.com',
        firstName: 'Test',
        lastName: 'User',
    )
);

Telegraph::updateUser(
    source: 'main',
    userId: '1234567890',
    email: 'test@example.com',
    request: new MemberDataRequest(
        firstName: 'Another',
        lastName: 'User',
    )
);

Telegraph::unsubscribeUser(
    source: 'main',
    userId: '1234567890',
    email: 'test@example.com'
);

$info = Telegraph::getUserMemberInfo(
    source: 'main',
    userId: '1234567890',
    email: 'test@example.com'
);
```

## Disciple

Telegraph can be used in conjunction with the [Disciple](https://github.com/decodelabs/disciple) package to provide a seamless experience for subscribing users to mailing lists.

If `Disciple` is installed, the following methods can automatically fill in the member data request with the current user's data. Note, you will still need to provide a `MemberDataRequest` instance if you need to assign groups or tags.


```php
use DecodeLabs\Telegraph;

Telegraph::subscribeDisciple();

Telegraph::updateDisciple(new MemberDataRequest(
    groups: [
        '1234567890' => true,
    ],
    tags: [
        'test' => true,
    ]
));

Telegraph::unsubscribeDisciple();
```


## Commandment Actions

This package also provides a few useful CLI actions using the [Commandment](https://github.com/decodelabs/commandment) package.

Assuming you have [Effigy](https://github.com/decodelabs/effigy) installed plus the necessary dependencies for CLI commands, you can run the following commands in your project root:

```bash
# Refresh the cache for all sources
effigy telegraph/refresh

# Refresh the cache for a specific source
effigy telegraph/refresh mySource

# Show ListInfo for specified source
effigy telegraph/info mySource

# Show a list of all available lists on all connected services
effigy telegraph/probe
```


## Licensing

Telegraph is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
