<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * App\Entities\Base\User
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`users`", uniqueConstraints={@ORM\UniqueConstraint(name="users_email_unique", columns={"`email`"})})
 */
class User extends AbstractEntity
{
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
     * @ORM\Column(name="`uses_two_factor_auth`", type="smallint")
     */
    protected $uses_two_factor_auth;

    /**
     * @ORM\Column(name="`authy_id`", type="string", length=255, nullable=true)
     */
    protected $authy_id;

    /**
     * @ORM\Column(name="`country_code`", type="string", length=10, nullable=true)
     */
    protected $country_code;

    /**
     * @ORM\Column(name="`phone`", type="string", length=25, nullable=true)
     */
    protected $phone;

    /**
     * @ORM\Column(name="`two_factor_reset_code`", type="string", length=100, nullable=true)
     */
    protected $two_factor_reset_code;

    /**
     * @ORM\Column(name="`current_team_id`", type="integer", nullable=true)
     */
    protected $current_team_id;

    /**
     * @ORM\Column(name="`stripe_id`", type="string", length=255, nullable=true)
     */
    protected $stripe_id;

    /**
     * @ORM\Column(name="`current_billing_plan`", type="string", length=255, nullable=true)
     */
    protected $current_billing_plan;

    /**
     * @ORM\Column(name="`card_brand`", type="string", length=255, nullable=true)
     */
    protected $card_brand;

    /**
     * @ORM\Column(name="`card_last_four`", type="string", length=255, nullable=true)
     */
    protected $card_last_four;

    /**
     * @ORM\Column(name="`card_country`", type="string", length=255, nullable=true)
     */
    protected $card_country;

    /**
     * @ORM\Column(name="`billing_address`", type="string", length=255, nullable=true)
     */
    protected $billing_address;

    /**
     * @ORM\Column(name="`billing_address_line_2`", type="string", length=255, nullable=true)
     */
    protected $billing_address_line_2;

    /**
     * @ORM\Column(name="`billing_city`", type="string", length=255, nullable=true)
     */
    protected $billing_city;

    /**
     * @ORM\Column(name="`billing_state`", type="string", length=255, nullable=true)
     */
    protected $billing_state;

    /**
     * @ORM\Column(name="`billing_zip`", type="string", length=25, nullable=true)
     */
    protected $billing_zip;

    /**
     * @ORM\Column(name="`billing_country`", type="string", length=2, nullable=true)
     */
    protected $billing_country;

    /**
     * @ORM\Column(name="`vat_id`", type="string", length=50, nullable=true)
     */
    protected $vat_id;

    /**
     * @ORM\Column(name="`extra_billing_information`", type="text", nullable=true)
     */
    protected $extra_billing_information;

    /**
     * @ORM\Column(name="`trial_ends_at`", type="datetime", nullable=true)
     */
    protected $trial_ends_at;

    /**
     * @ORM\Column(name="`last_read_announcements_at`", type="datetime", nullable=true)
     */
    protected $last_read_announcements_at;

    /**
     * @ORM\Column(name="`created_at`", type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`user_id`", nullable=false)
     */
    protected $projects;

    /**
     * @ORM\OneToMany(targetEntity="Workspace", mappedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="`id`", referencedColumnName="`user_id`", nullable=false)
     */
    protected $workspaces;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
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
     * Set the value of uses_two_factor_auth.
     *
     * @param integer $uses_two_factor_auth
     * @return \App\Entities\Base\User
     */
    public function setUsesTwoFactorAuth($uses_two_factor_auth)
    {
        $this->uses_two_factor_auth = $uses_two_factor_auth;

        return $this;
    }

    /**
     * Get the value of uses_two_factor_auth.
     *
     * @return integer
     */
    public function getUsesTwoFactorAuth()
    {
        return $this->uses_two_factor_auth;
    }

    /**
     * Set the value of authy_id.
     *
     * @param string $authy_id
     * @return \App\Entities\Base\User
     */
    public function setAuthyId($authy_id)
    {
        $this->authy_id = $authy_id;

        return $this;
    }

    /**
     * Get the value of authy_id.
     *
     * @return string
     */
    public function getAuthyId()
    {
        return $this->authy_id;
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
     * Set the value of two_factor_reset_code.
     *
     * @param string $two_factor_reset_code
     * @return \App\Entities\Base\User
     */
    public function setTwoFactorResetCode($two_factor_reset_code)
    {
        $this->two_factor_reset_code = $two_factor_reset_code;

        return $this;
    }

    /**
     * Get the value of two_factor_reset_code.
     *
     * @return string
     */
    public function getTwoFactorResetCode()
    {
        return $this->two_factor_reset_code;
    }

    /**
     * Set the value of current_team_id.
     *
     * @param integer $current_team_id
     * @return \App\Entities\Base\User
     */
    public function setCurrentTeamId($current_team_id)
    {
        $this->current_team_id = $current_team_id;

        return $this;
    }

    /**
     * Get the value of current_team_id.
     *
     * @return integer
     */
    public function getCurrentTeamId()
    {
        return $this->current_team_id;
    }

    /**
     * Set the value of stripe_id.
     *
     * @param string $stripe_id
     * @return \App\Entities\Base\User
     */
    public function setStripeId($stripe_id)
    {
        $this->stripe_id = $stripe_id;

        return $this;
    }

    /**
     * Get the value of stripe_id.
     *
     * @return string
     */
    public function getStripeId()
    {
        return $this->stripe_id;
    }

    /**
     * Set the value of current_billing_plan.
     *
     * @param string $current_billing_plan
     * @return \App\Entities\Base\User
     */
    public function setCurrentBillingPlan($current_billing_plan)
    {
        $this->current_billing_plan = $current_billing_plan;

        return $this;
    }

    /**
     * Get the value of current_billing_plan.
     *
     * @return string
     */
    public function getCurrentBillingPlan()
    {
        return $this->current_billing_plan;
    }

    /**
     * Set the value of card_brand.
     *
     * @param string $card_brand
     * @return \App\Entities\Base\User
     */
    public function setCardBrand($card_brand)
    {
        $this->card_brand = $card_brand;

        return $this;
    }

    /**
     * Get the value of card_brand.
     *
     * @return string
     */
    public function getCardBrand()
    {
        return $this->card_brand;
    }

    /**
     * Set the value of card_last_four.
     *
     * @param string $card_last_four
     * @return \App\Entities\Base\User
     */
    public function setCardLastFour($card_last_four)
    {
        $this->card_last_four = $card_last_four;

        return $this;
    }

    /**
     * Get the value of card_last_four.
     *
     * @return string
     */
    public function getCardLastFour()
    {
        return $this->card_last_four;
    }

    /**
     * Set the value of card_country.
     *
     * @param string $card_country
     * @return \App\Entities\Base\User
     */
    public function setCardCountry($card_country)
    {
        $this->card_country = $card_country;

        return $this;
    }

    /**
     * Get the value of card_country.
     *
     * @return string
     */
    public function getCardCountry()
    {
        return $this->card_country;
    }

    /**
     * Set the value of billing_address.
     *
     * @param string $billing_address
     * @return \App\Entities\Base\User
     */
    public function setBillingAddress($billing_address)
    {
        $this->billing_address = $billing_address;

        return $this;
    }

    /**
     * Get the value of billing_address.
     *
     * @return string
     */
    public function getBillingAddress()
    {
        return $this->billing_address;
    }

    /**
     * Set the value of billing_address_line_2.
     *
     * @param string $billing_address_line_2
     * @return \App\Entities\Base\User
     */
    public function setBillingAddressLine2($billing_address_line_2)
    {
        $this->billing_address_line_2 = $billing_address_line_2;

        return $this;
    }

    /**
     * Get the value of billing_address_line_2.
     *
     * @return string
     */
    public function getBillingAddressLine2()
    {
        return $this->billing_address_line_2;
    }

    /**
     * Set the value of billing_city.
     *
     * @param string $billing_city
     * @return \App\Entities\Base\User
     */
    public function setBillingCity($billing_city)
    {
        $this->billing_city = $billing_city;

        return $this;
    }

    /**
     * Get the value of billing_city.
     *
     * @return string
     */
    public function getBillingCity()
    {
        return $this->billing_city;
    }

    /**
     * Set the value of billing_state.
     *
     * @param string $billing_state
     * @return \App\Entities\Base\User
     */
    public function setBillingState($billing_state)
    {
        $this->billing_state = $billing_state;

        return $this;
    }

    /**
     * Get the value of billing_state.
     *
     * @return string
     */
    public function getBillingState()
    {
        return $this->billing_state;
    }

    /**
     * Set the value of billing_zip.
     *
     * @param string $billing_zip
     * @return \App\Entities\Base\User
     */
    public function setBillingZip($billing_zip)
    {
        $this->billing_zip = $billing_zip;

        return $this;
    }

    /**
     * Get the value of billing_zip.
     *
     * @return string
     */
    public function getBillingZip()
    {
        return $this->billing_zip;
    }

    /**
     * Set the value of billing_country.
     *
     * @param string $billing_country
     * @return \App\Entities\Base\User
     */
    public function setBillingCountry($billing_country)
    {
        $this->billing_country = $billing_country;

        return $this;
    }

    /**
     * Get the value of billing_country.
     *
     * @return string
     */
    public function getBillingCountry()
    {
        return $this->billing_country;
    }

    /**
     * Set the value of vat_id.
     *
     * @param string $vat_id
     * @return \App\Entities\Base\User
     */
    public function setVatId($vat_id)
    {
        $this->vat_id = $vat_id;

        return $this;
    }

    /**
     * Get the value of vat_id.
     *
     * @return string
     */
    public function getVatId()
    {
        return $this->vat_id;
    }

    /**
     * Set the value of extra_billing_information.
     *
     * @param string $extra_billing_information
     * @return \App\Entities\Base\User
     */
    public function setExtraBillingInformation($extra_billing_information)
    {
        $this->extra_billing_information = $extra_billing_information;

        return $this;
    }

    /**
     * Get the value of extra_billing_information.
     *
     * @return string
     */
    public function getExtraBillingInformation()
    {
        return $this->extra_billing_information;
    }

    /**
     * Set the value of trial_ends_at.
     *
     * @param \DateTime $trial_ends_at
     * @return \App\Entities\Base\User
     */
    public function setTrialEndsAt($trial_ends_at)
    {
        $this->trial_ends_at = $trial_ends_at;

        return $this;
    }

    /**
     * Get the value of trial_ends_at.
     *
     * @return \DateTime
     */
    public function getTrialEndsAt()
    {
        return $this->trial_ends_at;
    }

    /**
     * Set the value of last_read_announcements_at.
     *
     * @param \DateTime $last_read_announcements_at
     * @return \App\Entities\Base\User
     */
    public function setLastReadAnnouncementsAt($last_read_announcements_at)
    {
        $this->last_read_announcements_at = $last_read_announcements_at;

        return $this;
    }

    /**
     * Get the value of last_read_announcements_at.
     *
     * @return \DateTime
     */
    public function getLastReadAnnouncementsAt()
    {
        return $this->last_read_announcements_at;
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
     * Add Project entity to collection (one to many).
     *
     * @param \App\Entities\Base\Project $project
     * @return \App\Entities\Base\User
     */
    public function addProject(Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    /**
     * Remove Project entity from collection (one to many).
     *
     * @param \App\Entities\Base\Project $project
     * @return \App\Entities\Base\User
     */
    public function removeProject(Project $project)
    {
        $this->projects->removeElement($project);

        return $this;
    }

    /**
     * Get Project entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjects()
    {
        return $this->projects;
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

    public function __sleep()
    {
        return array('id', 'name', 'email', 'password', 'remember_token', 'photo_url', 'uses_two_factor_auth', 'authy_id', 'country_code', 'phone', 'two_factor_reset_code', 'current_team_id', 'stripe_id', 'current_billing_plan', 'card_brand', 'card_last_four', 'card_country', 'billing_address', 'billing_address_line_2', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'vat_id', 'extra_billing_information', 'trial_ends_at', 'last_read_announcements_at', 'created_at', 'updated_at');
    }
}