<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;
use DecodeLabs\Telegraph\Source\GroupInfo;
use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;
use DecodeLabs\Telegraph\Source\MemberStatus;
use DecodeLabs\Telegraph\Source\TagInfo;

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

    public function refreshListInfo(): ?ListInfo
    {
        $this->store?->clearListInfo($this);
        $this->cache->clearListInfo($this);

        return $this->getListInfo();
    }

    /**
     * @return array<string,string>
     */
    public function getGroupOptions(
        bool $forceCategories = false,
        ?string $noCategoryLabel = null
    ): array {
        return $this->getListInfo()?->getGroupOptions($forceCategories, $noCategoryLabel) ?? [];
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function getCategorizedGroupOptions(
        ?string $noCategoryLabel = null
    ): array {
        return $this->getListInfo()?->getCategorizedGroupOptions($noCategoryLabel) ?? [];
    }

    public function getGroup(
        string $groupId
    ): ?GroupInfo {
        return $this->getListInfo()?->groups[$groupId] ?? null;
    }

    public function getGroupName(
        string $groupId,
        bool $forceCategories = false,
        ?string $noCategoryLabel = null
    ): ?string {
        return $this->getListInfo()?->getGroupName($groupId, $forceCategories, $noCategoryLabel);
    }

    /**
     * @return array<string,string>
     */
    public function getGroupCategoryOptions(): array
    {
        return $this->getListInfo()?->getGroupCategoryOptions() ?? [];
    }

    public function getGroupCategoryName(
        string $groupId,
        ?string $noCategoryLabel = null
    ): ?string {
        return $this->getListInfo()?->getGroupCategoryName($groupId, $noCategoryLabel);
    }

    public function getGroupCategoryNameByGroupId(
        string $groupId,
        ?string $noCategoryLabel = null
    ): ?string {
        return $this->getListInfo()?->getGroupCategoryNameByGroupId($groupId, $noCategoryLabel);
    }

    /**
     * @return array<string,string>
     */
    public function getTagOptions(): array
    {
        return $this->getListInfo()?->getTagOptions() ?? [];
    }

    public function getTag(
        string $tagId
    ): ?TagInfo {
        return $this->getListInfo()?->tags[$tagId] ?? null;
    }

    public function getTagName(
        string $tagId
    ): ?string {
        return $this->getListInfo()?->getTagName($tagId);
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

        if($this->isUserSubscribed($userId, $request->email)) {
            $result = $this->adapter->update($this, $listInfo, $request->email, $request);
        } else {
            $result = $this->adapter->subscribe($this, $listInfo, $request);
        }

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


    public function isDiscipleSubscribed(
        string|GroupInfo|null $group = null,
        string|TagInfo|null $tag = null
    ): bool {
        return $this->isUserSubscribed(
            userId: Disciple::getActiveId(),
            email: (string)Disciple::getEmail(),
            group: $group,
            tag: $tag
        );
    }

    public function isUserSubscribed(
        string $userId,
        string $email,
        string|GroupInfo|null $group = null,
        string|TagInfo|null $tag = null
    ): bool {
        $info = $this->getUserMemberInfo(
            userId: $userId,
            email: $email
        );

        return $this->isMemberSubscribed($info, $group, $tag);
    }

    public function isSubscribed(
        string $email,
        string|GroupInfo|null $group = null,
        string|TagInfo|null $tag = null
    ): bool {
        $info = $this->getMemberInfo($email);
        return $this->isMemberSubscribed($info, $group, $tag);
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

    public function getDiscipleMemberInfo(
        bool $force = false
    ): ?MemberInfo {
        if(!class_exists(Disciple::class)) {
            throw Exceptional::ComponentUnavailable(
                'Disciple package is not installed'
            );
        }

        return $this->getUserMemberInfo(
            Disciple::getActiveId(),
            (string)Disciple::getEmail(),
            $force
        );
    }

    public function refreshDiscipleMemberInfo(): ?MemberInfo {
        return $this->refreshUserMemberInfo(
            Disciple::getActiveId(),
            (string)Disciple::getEmail()
        );
    }

    public function getUserMemberInfo(
        string $userId,
        string $email,
        bool $force = false
    ): ?MemberInfo {
        if(null !== ($info = $this->cache->fetchMemberInfo($this, $email))) {
            if($info === false) {
                return null;
            }

            return $this->checkMemberSubscribed($info, force: $force);
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
        return $this->checkMemberSubscribed($info, force: $force);
    }

    public function refreshUserMemberInfo(
        string $userId,
        string $email
    ): ?MemberInfo {
        $this->store?->clearMemberInfo($this, $userId);
        $this->cache->clearMemberInfo($this, $email);

        return $this->getUserMemberInfo($userId, $email);
    }

    public function getMemberInfo(
        string $email,
        bool $force = false
    ): ?MemberInfo {
        if(null !== ($info = $this->cache->fetchMemberInfo($this, $email))) {
            if($info === false) {
                return null;
            }

            return $this->checkMemberSubscribed($info, force: $force);
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
        return $this->checkMemberSubscribed($info, force: $force);
    }

    public function refreshMemberInfo(
        string $email
    ): ?MemberInfo {
        $this->cache->clearMemberInfo($this, $email);
        return $this->getMemberInfo($email);
    }

    private function isMemberSubscribed(
        ?MemberInfo $info,
        string|GroupInfo|null $group = null,
        string|TagInfo|null $tag = null
    ): bool {
        if(
            $info === null ||
            $info->status !== MemberStatus::Subscribed
        ) {
            return false;
        }

        if($group instanceof GroupInfo) {
            $group = $group->id;
        }

        if($tag instanceof TagInfo) {
            $tag = $tag->id;
        }

        if($group !== null) {
            if(!isset($info->groups[$group])) {
                return false;
            }
        }

        if($tag !== null) {
            if(!isset($info->tags[$tag])) {
                return false;
            }
        }

        return true;
    }

    private function checkMemberSubscribed(
        ?MemberInfo $info,
        string|GroupInfo|null $group = null,
        string|TagInfo|null $tag = null,
        bool $force = false
    ): ?MemberInfo {
        if($this->isMemberSubscribed($info, $group, $tag)) {
            return $info;
        }

        return $force ? $info : null;
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
