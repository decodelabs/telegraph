<?php

/**
 * @package Telegraph
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Telegraph;

use DecodeLabs\Disciple;
use DecodeLabs\Exceptional;
use DecodeLabs\Telegraph\Source\ConsentField;
use DecodeLabs\Telegraph\Source\ConsentType;
use DecodeLabs\Telegraph\Source\GroupInfo;
use DecodeLabs\Telegraph\Source\ListInfo;
use DecodeLabs\Telegraph\Source\MemberInfo;
use DecodeLabs\Telegraph\Source\MemberStatus;
use DecodeLabs\Telegraph\Source\TagInfo;

class Source extends SourceReference
{
    public protected(set) Adapter $adapter;
    public protected(set) Cache $cache;
    public protected(set) ?Store $store = null;
    protected ?Disciple $disciple = null;

    public function __construct(
        string $name,
        string $remoteId,
        Adapter $adapter,
        Cache $cache,
        ?Store $store = null,
        ?Disciple $disciple = null
    ) {
        parent::__construct($name, $remoteId);
        $this->adapter = $adapter;
        $this->cache = $cache;
        $this->store = $store;
        $this->disciple = $disciple;
    }

    public function getListInfo(): ?ListInfo
    {
        if (null !== ($info = $this->cache->fetchListInfo($this))) {
            if ($info === false) {
                return null;
            }

            return $info;
        }

        if (
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
        if ($info = $this->adapter->fetchListInfo($this)) {
            $this->store?->storeListInfo($this, $info);
        } else {
            $this->store?->clearListInfo($this);
        }

        $this->cache->storeListInfo($this, $info);
        return $info;
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

    /**
     * @return array<string,GroupInfo>
     */
    public function getGroups(): array
    {
        return $this->getListInfo()->groups ?? [];
    }

    public function getGroup(
        string $groupId
    ): ?GroupInfo {
        return $this->getListInfo()?->getGroup($groupId);
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

    /**
     * @return array<string,TagInfo>
     */
    public function getTags(): array
    {
        return $this->getListInfo()->tags ?? [];
    }

    public function getTag(
        string $tagId
    ): ?TagInfo {
        return $this->getListInfo()?->getTag($tagId);
    }

    public function getTagName(
        string $tagId
    ): ?string {
        return $this->getListInfo()?->getTagName($tagId);
    }


    /**
     * @return array<string,string>
     */
    public function getConsentFieldOptions(): array
    {
        return $this->getListInfo()?->getConsentFieldOptions() ?? [];
    }

    /**
     * @return array<string,ConsentField>
     */
    public function getConsentFields(): array
    {
        return $this->getListInfo()->consentFields ?? [];
    }

    public function getConsentField(
        string $consentFieldId
    ): ?ConsentField {
        return $this->getListInfo()?->getConsentField($consentFieldId);
    }

    public function getTypeConsentField(
        ConsentType $type
    ): ?ConsentField {
        return $this->getListInfo()?->getTypeConsentField($type);
    }




    public function subscribeDisciple(
        ?MemberDataRequest $request = null
    ): SubscriptionResponse {
        if (!$this->disciple) {
            throw Exceptional::ComponentUnavailable(
                'Disciple service is not available'
            );
        }

        if ($request === null) {
            $request = new MemberDataRequest();
        }

        if ($request->email === null) {
            $request->email = $this->disciple->email;
        }

        if ($request->firstName === null) {
            $request->firstName = $this->disciple->firstName;
        }

        if ($request->lastName === null) {
            $request->lastName = $this->disciple->surname;
        }

        if ($request->country === null) {
            $request->country = $this->disciple->country;
        }

        if ($request->language === null) {
            $request->language = $this->disciple->language;
        }

        return $this->subscribeUser(
            $this->disciple->activeId,
            $request
        );
    }

    public function subscribeUser(
        string $userId,
        MemberDataRequest $request
    ): SubscriptionResponse {
        if ($request->email === null) {
            throw Exceptional::InvalidArgument(
                'Email address is required'
            );
        }

        if (!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        if ($this->isUserSubscribed($userId, $request->email)) {
            $result = $this->adapter->update($this, $listInfo, $request->email, $request);
        } else {
            $result = $this->adapter->subscribe($this, $listInfo, $request);
        }

        if ($result->response->success) {
            if ($result->memberInfo !== null) {
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
        if ($request->email === null) {
            throw Exceptional::InvalidArgument(
                'Email address is required'
            );
        }

        if (!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->subscribe($this, $listInfo, $request);

        if ($result->response->success) {
            if ($result->memberInfo !== null) {
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
        if (!$this->disciple) {
            throw Exceptional::ComponentUnavailable(
                'Disciple service is not available'
            );
        }

        return $this->isUserSubscribed(
            userId: $this->disciple->activeId,
            email: (string)$this->disciple->email,
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
        if (!$this->disciple) {
            throw Exceptional::ComponentUnavailable(
                'Disciple service is not available'
            );
        }

        return $this->updateUser(
            $this->disciple->activeId,
            (string)$this->disciple->email,
            $request
        );
    }

    public function updateUser(
        string $userId,
        string $email,
        MemberDataRequest $request
    ): SubscriptionResponse {
        if (!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->update($this, $listInfo, $email, $request);

        if ($result->response->success) {
            if ($result->memberInfo !== null) {
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
        if (!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->update($this, $listInfo, $email, $request);

        if ($result->response->success) {
            if ($result->memberInfo !== null) {
                $this->cache->storeMemberInfo($this, $email, $result->memberInfo);
            } else {
                $this->cache->clearMemberInfo($this, $email);
            }
        }

        return $result->response;
    }

    public function unsubscribeDisciple(): SubscriptionResponse
    {
        if (!$this->disciple) {
            throw Exceptional::ComponentUnavailable(
                'Disciple service is not available'
            );
        }

        return $this->unsubscribeUser(
            $this->disciple->activeId,
            (string)$this->disciple->email
        );
    }

    public function unsubscribeUser(
        string $userId,
        string $email
    ): SubscriptionResponse {
        if (!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->unsubscribe($this, $listInfo, $email);

        if ($result->response->success) {
            $this->cache->clearMemberInfo($this, $email);
            $this->store?->clearMemberInfo($this, $userId);
        }

        return $result->response;
    }

    public function unsubscribe(
        string $email
    ): SubscriptionResponse {
        if (!$listInfo = $this->getListInfo()) {
            return $this->newFailureResponse();
        }

        $result = $this->adapter->unsubscribe($this, $listInfo, $email);

        if ($result->response->success) {
            $this->cache->clearMemberInfo($this, $email);
        }

        return $result->response;
    }

    public function getDiscipleMemberInfo(
        bool $force = false
    ): ?MemberInfo {
        if (!$this->disciple) {
            throw Exceptional::ComponentUnavailable(
                'Disciple service is not available'
            );
        }

        return $this->getUserMemberInfo(
            $this->disciple->activeId,
            (string)$this->disciple->email,
            $force
        );
    }

    public function refreshDiscipleMemberInfo(): ?MemberInfo
    {
        if (!$this->disciple) {
            throw Exceptional::ComponentUnavailable(
                'Disciple service is not available'
            );
        }

        return $this->refreshUserMemberInfo(
            $this->disciple->activeId,
            (string)$this->disciple->email
        );
    }

    public function getUserMemberInfo(
        string $userId,
        string $email,
        bool $force = false
    ): ?MemberInfo {
        if (null !== ($info = $this->cache->fetchMemberInfo($this, $email))) {
            if ($info === false) {
                return null;
            }

            return $this->checkMemberSubscribed($info, force: $force);
        }

        if ($info = $this->store?->fetchMemberInfo($this, $userId)) {
            $email = $info->email;
        } else {
            if (!$listInfo = $this->getListInfo()) {
                return null;
            }

            if (
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
        if (!$listInfo = $this->getListInfo()) {
            return null;
        }

        if ($info = $this->adapter->fetchMemberInfo($this, $listInfo, $email)) {
            $this->store?->storeMemberInfo($this, $userId, $info);
        } else {
            $this->store?->clearMemberInfo($this, $userId);
        }

        $this->cache->storeMemberInfo($this, $email, $info);
        return $this->checkMemberSubscribed($info);
    }

    public function getMemberInfo(
        string $email,
        bool $force = false
    ): ?MemberInfo {
        if (null !== ($info = $this->cache->fetchMemberInfo($this, $email))) {
            if ($info === false) {
                return null;
            }

            return $this->checkMemberSubscribed($info, force: $force);
        }

        if (!$listInfo = $this->getListInfo()) {
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
        if (!$listInfo = $this->getListInfo()) {
            return null;
        }

        $info = $this->adapter->fetchMemberInfo(
            $this,
            $listInfo,
            $email,
        );

        $this->cache->storeMemberInfo($this, $email, $info);
        return $this->checkMemberSubscribed($info);
    }

    private function isMemberSubscribed(
        ?MemberInfo $info,
        string|GroupInfo|null $group = null,
        string|TagInfo|null $tag = null
    ): bool {
        if (
            $info === null ||
            $info->status !== MemberStatus::Subscribed
        ) {
            return false;
        }

        if ($group instanceof GroupInfo) {
            $group = $group->id;
        }

        if ($tag instanceof TagInfo) {
            $tag = $tag->id;
        }

        if ($group !== null) {
            if (!isset($info->groups[$group])) {
                return false;
            }
        }

        if ($tag !== null) {
            if (!isset($info->tags[$tag])) {
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
        if ($this->isMemberSubscribed($info, $group, $tag)) {
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
