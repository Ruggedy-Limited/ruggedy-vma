<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\User
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`users`", uniqueConstraints={@ORM\UniqueConstraint(name="users_email_unique", columns={"`email`"})})
 */
class User extends AbstractEntity
{
    /** Table name constant */
    const TABLE_NAME = 'users';

    /** Column name constants */
    const NAME                                    = 'name';
    const EMAIL                                   = 'email';
    const PASSWORD                                = 'password';
    const REMEMBER_TOKEN                          = 'remember_token';
    const PHOTO_URL                               = 'photo_url';
    const COUNTRY_CODE                            = 'country_code';
    const PHONE                                   = 'phone';
    const IS_ADMIN                                = 'is_admin';
    const DELETED                                 = 'deleted';
    const ASSETS                                  = 'assets';
    const COMMENTS                                = 'comments';
    const COMPONENTPERMISSIONRELATEDBYUSERIDS     = 'componentPermissionRelatedByUserIds';
    const COMPONENTPERMISSIONRELATEDBYGRANTEDBIES = 'componentPermissionRelatedByGrantedBies';
    const FILES                                   = 'files';
    const FOLDERS                                 = 'folders';
    const WORKSPACES                              = 'workspaces';

    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`name`", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="`email`", type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\Column(name="`password`", type="string", length=60)
     */
    protected $password;

    /**
     * @ORM\Column(name="`remember_token`", type="string", length=100, nullable=true)
     */
    protected $remember_token;

    /**
     * @ORM\Column(name="`photo_url`", type="text", nullable=true)
     */
    protected $photo_url;

    /**
     * @ORM\Column(name="`country_code`", type="string", length=10, nullable=true)
     */
    protected $country_code;

    /**
     * @ORM\Column(name="`phone`", type="string", length=25, nullable=true)
     */
    protected $phone;

    /**
     * @ORM\Column(name="`is_admin`", type="boolean", options={"unsigned":true})
     */
    protected $is_admin;

    /**
     * @ORM\Column(name="`deleted`", type="boolean", options={"unsigned":true})
     */
    protected $deleted;

    /**
     * @ORM\Column(name="`created_at`", type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`user_id`", nullable=false)
     */
    protected $assets;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`user_id`", nullable=false)
     */
    protected $comments;

    /**
     * @ORM\OneToMany(targetEntity="ComponentPermission", mappedBy="userRelatedByUserId", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`user_id`", nullable=false)
     */
    protected $componentPermissionRelatedByUserIds;

    /**
     * @ORM\OneToMany(targetEntity="ComponentPermission", mappedBy="userRelatedByGrantedBy", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`granted_by`", nullable=false)
     */
    protected $componentPermissionRelatedByGrantedBies;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`user_id`", nullable=false)
     */
    protected $files;

    /**
     * @ORM\OneToMany(targetEntity="Folder", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`user_id`", nullable=false)
     */
    protected $folders;

    /**
     * @ORM\OneToMany(targetEntity="Workspace", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`user_id`", nullable=false)
     */
    protected $workspaces;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->componentPermissionRelatedByUserIds = new ArrayCollection();
        $this->componentPermissionRelatedByGrantedBies = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->folders = new ArrayCollection();
        $this->workspaces = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of name.
     *
     * @param string $name
     * @return \App\Entities\Base\User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of email.
     *
     * @param string $email
     * @return \App\Entities\Base\User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of password.
     *
     * @param string $password
     * @return \App\Entities\Base\User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of remember_token.
     *
     * @param string $remember_token
     * @return \App\Entities\Base\User
     */
    public function setRememberToken($remember_token)
    {
        $this->remember_token = $remember_token;

        return $this;
    }

    /**
     * Get the value of remember_token.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the value of photo_url.
     *
     * @param string $photo_url
     * @return \App\Entities\Base\User
     */
    public function setPhotoUrl($photo_url)
    {
        $this->photo_url = $photo_url;

        return $this;
    }

    /**
     * Get the value of photo_url.
     *
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photo_url;
    }

    /**
     * Set the value of country_code.
     *
     * @param string $country_code
     * @return \App\Entities\Base\User
     */
    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;

        return $this;
    }

    /**
     * Get the value of country_code.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * Set the value of phone.
     *
     * @param string $phone
     * @return \App\Entities\Base\User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Get the value of is_admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Set the value of is_admin
     *
     * @param mixed $is_admin
     * @return User
     */
    public function setIsAdmin($is_admin)
    {
        $this->is_admin = $is_admin;

        return $this;
    }

    /**
     * Get the value of deleted.
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set the value of deleted
     *
     * @param bool $deleted
     * @return User
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\User
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of created_at.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set the value of updated_at.
     *
     * @param \DateTime $updated_at
     * @return \App\Entities\Base\User
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * Get the value of updated_at.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Add Asset entity to collection (one to many).
     *
     * @param \App\Entities\Base\Asset $asset
     * @return \App\Entities\Base\User
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[] = $asset;

        return $this;
    }

    /**
     * Remove Asset entity from collection (one to many).
     *
     * @param \App\Entities\Base\Asset $asset
     * @return \App\Entities\Base\User
     */
    public function removeAsset(Asset $asset)
    {
        $this->assets->removeElement($asset);

        return $this;
    }

    /**
     * Get Asset entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssets()
    {
        return $this->assets;
    }

    /**
     * Add Comment entity to collection (one to many).
     *
     * @param \App\Entities\Base\Comment $comment
     * @return \App\Entities\Base\User
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove Comment entity from collection (one to many).
     *
     * @param \App\Entities\Base\Comment $comment
     * @return \App\Entities\Base\User
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);

        return $this;
    }

    /**
     * Get Comment entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add ComponentPermission entity related by `user_id` to collection (one to many).
     *
     * @param \App\Entities\Base\ComponentPermission $componentPermission
     * @return \App\Entities\Base\User
     */
    public function addComponentPermissionRelatedByUserId(ComponentPermission $componentPermission)
    {
        $this->componentPermissionRelatedByUserIds[] = $componentPermission;

        return $this;
    }

    /**
     * Remove ComponentPermission entity related by `user_id` from collection (one to many).
     *
     * @param \App\Entities\Base\ComponentPermission $componentPermission
     * @return \App\Entities\Base\User
     */
    public function removeComponentPermissionRelatedByUserId(ComponentPermission $componentPermission)
    {
        $this->componentPermissionRelatedByUserIds->removeElement($componentPermission);

        return $this;
    }

    /**
     * Get ComponentPermission entity related by `user_id` collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComponentPermissionRelatedByUserIds()
    {
        return $this->componentPermissionRelatedByUserIds;
    }

    /**
     * Add ComponentPermission entity related by `granted_by` to collection (one to many).
     *
     * @param \App\Entities\Base\ComponentPermission $componentPermission
     * @return \App\Entities\Base\User
     */
    public function addComponentPermissionRelatedByGrantedBy(ComponentPermission $componentPermission)
    {
        $this->componentPermissionRelatedByGrantedBies[] = $componentPermission;

        return $this;
    }

    /**
     * Remove ComponentPermission entity related by `granted_by` from collection (one to many).
     *
     * @param \App\Entities\Base\ComponentPermission $componentPermission
     * @return \App\Entities\Base\User
     */
    public function removeComponentPermissionRelatedByGrantedBy(ComponentPermission $componentPermission)
    {
        $this->componentPermissionRelatedByGrantedBies->removeElement($componentPermission);

        return $this;
    }

    /**
     * Get ComponentPermission entity related by `granted_by` collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComponentPermissionRelatedByGrantedBies()
    {
        return $this->componentPermissionRelatedByGrantedBies;
    }

    /**
     * Add File entity to collection (one to many).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\User
     */
    public function addFile(File $file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Remove File entity from collection (one to many).
     *
     * @param \App\Entities\Base\File $file
     * @return \App\Entities\Base\User
     */
    public function removeFile(File $file)
    {
        $this->files->removeElement($file);

        return $this;
    }

    /**
     * Get File entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add Folder entity to collection (one to many).
     *
     * @param \App\Entities\Base\Folder $folder
     * @return \App\Entities\Base\User
     */
    public function addFolder(Folder $folder)
    {
        $this->folders[] = $folder;

        return $this;
    }

    /**
     * Remove Folder entity from collection (one to many).
     *
     * @param \App\Entities\Base\Folder $folder
     * @return \App\Entities\Base\User
     */
    public function removeFolder(Folder $folder)
    {
        $this->folders->removeElement($folder);

        return $this;
    }

    /**
     * Get Folder entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Add Workspace entity to collection (one to many).
     *
     * @param \App\Entities\Base\Workspace $workspace
     * @return \App\Entities\Base\User
     */
    public function addWorkspace(Workspace $workspace)
    {
        $this->workspaces[] = $workspace;

        return $this;
    }

    /**
     * Remove Workspace entity from collection (one to many).
     *
     * @param \App\Entities\Base\Workspace $workspace
     * @return \App\Entities\Base\User
     */
    public function removeWorkspace(Workspace $workspace)
    {
        $this->workspaces->removeElement($workspace);

        return $this;
    }

    /**
     * Get Workspace entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorkspaces()
    {
        return $this->workspaces;
    }

    /**
     * Get the display name for the entity
     *
     * @param bool $plural
     * @return string
     */
    public function getDisplayName(bool $plural = false): string
    {
        return $plural === false ? 'User' : 'Users';
    }

    public function __sleep()
    {
        return array('id', 'name', 'email', 'password', 'remember_token', 'photo_url', 'country_code', 'phone', 'created_at', 'updated_at');
    }
}