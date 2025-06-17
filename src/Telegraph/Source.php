<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;
use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;

class Source extends SourceReference
{
    protected(set) Adapter $adapter;
    protected(set) Cache $cache;
    protected(set) ?Store $store = null;

    public function __construct(
        string $name,
        string $remoteId,
        Adapter $adapter,
        Cache $cache,
        ?Store $store = null
    ) {
        parent::__construct($name, $remoteId);
        $this->adapter = $adapter;
        $this->cache = $cache;
        $this->store = $store;
    }

    public function getListInfo(): ?ListInfo
    {
        if(null !== ($info = $this->cache->fetchListInfo($this))) {
            if($info === false) {
                return null;
            }

            return $info;
        }

        if(
            (!$info = $this->store?->fetchListInfo($this)) &&
            ($info = $this->adapter->fetchListInfo($this))
        ) {
            $this->store?->storeListInfo($this, $info);
        }

        $this->cache->storeListInfo($this, $info);
        return $info;
    }

    public function subscribe(
        MemberDataRequest $request
    ): SubscriptionResponse {
        return $this->adapter->subscribe($this, $request);
    }

    public function update(
        MemberDataRequest $request
    ): SubscriptionResponse {
        return $this->adapter->update($this, $request);
    }

    public function unsubscribe(
        string $email
    ): SubscriptionResponse {
        $output = $this->adapter->unsubscribe($this, $email);

        if($output->success) {
            $this->cache->clearMemberInfo($this, $email);
            $this->store?->clearMemberInfo($this, $email);
        }

        return $output;
    }

    public function getDiscipleMemberInfo(): ?MemberInfo
    {
        if(!class_exists(Disciple::class)) {
            throw Exceptional::ComponentUnavailable(
                'Disciple package is not installed'
            );
        }

        return $this->getUserMemberInfo(
            Disciple::getActiveId(),
            (string)Disciple::getEmail()
        );
    }

    public function getUserMemberInfo(
        string $userId,
        string $email
    ): ?MemberInfo {
        if(null !== ($info = $this->cache->fetchMemberInfo($this, $email))) {
            if($info === false) {
                return null;
            }

            return $info;
        }

        if($info = $this->store?->fetchMemberInfo($this, $userId)) {
            $email = $info->email;
        } else {
            if(!$listInfo = $this->getListInfo()) {
                return null;
            }

            if(
                $info = $this->adapter->fetchMemberInfo(
                    $this,
                    $listInfo,
                    $email,
                )
            ) {
                $this->store?->storeMemberInfo($this, $userId, $info);
            }
        }

        $this->cache->storeMemberInfo($this, $email, $info);
        return $info;
    }

    public function getMemberInfo(
        string $email,
    ): ?MemberInfo {
        if(null !== ($info = $this->cache->fetchMemberInfo($this, $email))) {
            if($info === false) {
                return null;
            }

            return $info;
        }

        if(!$listInfo = $this->getListInfo()) {
            return null;
        }

        $info = $this->adapter->fetchMemberInfo(
            $this,
            $listInfo,
            $email,
        );

        $this->cache->storeMemberInfo($this, $email, $info);
        return $info;
    }
}
