<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\Team
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="`teams`", indexes={@ORM\Index(name="teams_owner_id_index", columns={"`owner_id`"})})
 */
class Team extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`owner_id`", type="integer")
     */
    protected $owner_id;

    /**
     * @ORM\Column(name="`name`", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="`photo_url`", type="text", nullable=true)
     */
    protected $photo_url;

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
     * @ORM\Column(name="`created_at`", type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @ORM\Column(name="`updated_at`", type="datetime", nullable=true)
     */
    protected $updated_at;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \App\Entities\Base\Team
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
     * Set the value of owner_id.
     *
     * @param integer $owner_id
     * @return \App\Entities\Base\Team
     */
    public function setOwnerId($owner_id)
    {
        $this->owner_id = $owner_id;

        return $this;
    }

    /**
     * Get the value of owner_id.
     *
     * @return integer
     */
    public function getOwnerId()
    {
        return $this->owner_id;
    }

    /**
     * Set the value of name.
     *
     * @param string $name
     * @return \App\Entities\Base\Team
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
     * Set the value of photo_url.
     *
     * @param string $photo_url
     * @return \App\Entities\Base\Team
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
     * Set the value of stripe_id.
     *
     * @param string $stripe_id
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Team
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
     * @return \App\Entities\Base\Team
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

    public function __sleep()
    {
        return array('id', 'owner_id', 'name', 'photo_url', 'stripe_id', 'current_billing_plan', 'card_brand', 'card_last_four', 'card_country', 'billing_address', 'billing_address_line_2', 'billing_city', 'billing_state', 'billing_zip', 'billing_country', 'vat_id', 'extra_billing_information', 'trial_ends_at', 'created_at', 'updated_at');
    }
}