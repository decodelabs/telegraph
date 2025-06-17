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

    public function subscribeDisciple(
        ?MemberDataRequest $request = null
    ): SubscriptionResponse {
        if(!class_exists(Disciple::class)) {
            throw Exceptional::ComponentUnavailable(
                'Disciple package is not installed'
            );
        }

        if($request === null) {
            $request = new MemberDataRequest();
        }

        if($request->email === null) {
            $request->email = Disciple::getEmail();
        }

        if($request->firstName === null) {
            $request->firstName = Disciple::getFirstName();
        }

        if($request->lastName === null) {
            $request->lastName = Disciple::getSurname();
        }

        if($request->country === null) {
            $request->country = Disciple::getCountry();
        }

        if($request->language === null) {
            $request->language = Disciple::getLanguage();
        }

        return $this->subscribeUser(
            Disciple::getActiveId(),
            $request
        );
    }

    public function subscribeUser(
        string $userId,
        MemberDataRequest $request
    ): SubscriptionResponse {
        if($request->email === null) {
            throw Exceptional::InvalidArgument(
                'Email address is required'
            );
        }

        if(!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->subscribe($this, $listInfo, $request);

        if($result->response->success) {
            if($result->memberInfo !== null) {
                $this->cache->storeMemberInfo($this, $request->email, $result->memberInfo);
                $this->store?->storeMemberInfo($this, $userId, $result->memberInfo);
            } else {
                $this->cache->clearMemberInfo($this, $request->email);
                $this->store?->clearMemberInfo($this, $userId);
            }
        }

        return $result->response;
    }

    public function subscribe(
        MemberDataRequest $request
    ): SubscriptionResponse {
        if($request->email === null) {
            throw Exceptional::InvalidArgument(
                'Email address is required'
            );
        }

        if(!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->subscribe($this, $listInfo, $request);

        if($result->response->success) {
            if($result->memberInfo !== null) {
                $this->cache->storeMemberInfo($this, $request->email, $result->memberInfo);
            } else {
                $this->cache->clearMemberInfo($this, $request->email);
            }
        }

        return $result->response;
    }

    public function updateDisciple(
        MemberDataRequest $request
    ): SubscriptionResponse {
        if(!class_exists(Disciple::class)) {
            throw Exceptional::ComponentUnavailable(
                'Disciple package is not installed'
            );
        }

        return $this->updateUser(
            Disciple::getActiveId(),
            (string)Disciple::getEmail(),
            $request
        );
    }

    public function updateUser(
        string $userId,
        string $email,
        MemberDataRequest $request
    ): SubscriptionResponse {
        if(!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->update($this, $listInfo, $email, $request);

        if($result->response->success) {
            if($result->memberInfo !== null) {
                $this->cache->storeMemberInfo($this, $email, $result->memberInfo);
                $this->store?->storeMemberInfo($this, $userId, $result->memberInfo);
            } else {
                $this->cache->clearMemberInfo($this, $email);
                $this->store?->clearMemberInfo($this, $userId);
            }
        }

        return $result->response;
    }

    public function update(
        string $email,
        MemberDataRequest $request
    ): SubscriptionResponse {
        if(!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->update($this, $listInfo, $email, $request);

        if($result->response->success) {
            if($result->memberInfo !== null) {
                $this->cache->storeMemberInfo($this, $email, $result->memberInfo);
            } else {
                $this->cache->clearMemberInfo($this, $email);
            }
        }

        return $result->response;
    }

    public function unsubscribeDisciple(): SubscriptionResponse
    {
        if(!class_exists(Disciple::class)) {
            throw Exceptional::ComponentUnavailable(
                'Disciple package is not installed'
            );
        }

        return $this->unsubscribeUser(
            Disciple::getActiveId(),
            (string)Disciple::getEmail()
        );
    }

    public function unsubscribeUser(
        string $userId,
        string $email
    ): SubscriptionResponse {
        if(!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->unsubscribe($this, $listInfo, $email);

        if($result->response->success) {
            $this->cache->clearMemberInfo($this, $email);
            $this->store?->clearMemberInfo($this, $userId);
        }

        return $result->response;
    }

    public function unsubscribe(
        string $email
    ): SubscriptionResponse {
        if(!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->unsubscribe($this, $listInfo, $email);

        if($result->response->success) {
            $this->cache->clearMemberInfo($this, $email);
        }

        return $result->response;
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

    private function newFailureResponse(
        FailureReason $reason = FailureReason::ServiceUnavailable
    ): SubscriptionResponse {
        return new SubscriptionResponse(
            source: $this,
            success: false,
            failureReason: $reason
        );
    }
}
