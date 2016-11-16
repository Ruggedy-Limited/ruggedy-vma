<?php

namespace App\Transformers;

use App\Entities\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'team',
        'announcements',
        'permissions',
        'files',
        'invites',
        'notifications',
        'teams',
        'workspaces',
    ];

    /**
     * Transform a User entity for the API
     *
     * @param User $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'                   => $user->getId(),
            'name'                 => $user->getName(),
            'emailAddress'         => $user->getEmail(),
            'countryCode'          => $user->getCountryCode(),
            'phoneNo'              => $user->getPhone(),
            'photo'                => $user->getPhotoUrl(),
            'twoFactorAuthEnabled' => $user->getUsesTwoFactorAuth(),
            'createdDate'          => $user->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate'         => $user->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }

    /**
     * Optional include for the related, active Team
     *
     * @param User $user
     * @return \League\Fractal\Resource\Item
     */
    public function includeTeam(User $user)
    {
        return $this->item($user->getTeam(), new TeamTransformer());
    }

    /**
     * Optional include for the related Announcements
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeAnnouncements(User $user)
    {
        return $this->collection($user->getAnnouncements(), new AnnouncementTransformer());
    }

    /**
     * Optional include for the related Permissions
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includePermissions(User $user)
    {
        return $this->collection($user->getComponentPermissionRelatedByUserIds(), new ComponentPermissionTransformer());
    }

    /**
     * Optional include for the related Files
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFiles(User $user)
    {
        return $this->collection($user->getFiles(), new FileTransformer());
    }

    /**
     * Optional include for the related Invitations
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeInvites(User $user)
    {
        return $this->collection($user->getInvitations(), new InvitationTransformer());
    }

    /**
     * Optional include for the related Notifications
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeNotifications(User $user)
    {
        return $this->collection($user->getNotificationRelatedByUserIds(), new NotificationTransformer());
    }

    /**
     * Optional include for the related Teams
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeTeams(User $user)
    {
        return $this->collection($user->getTeams(), new TeamTransformer());
    }

    /**
     * Optional include for the related Workspaces
     *
     * @param User $user
     * @return \League\Fractal\Resource\Collection
     */
    public function includeWorkspaces(User $user)
    {
        return $this->collection($user->getWorkspaces(), new WorkspaceTransformer());
    }
}