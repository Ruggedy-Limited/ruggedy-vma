<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\Invoice
 *
 * @ORM\Entity(repositoryClass="App\Repositories\InvoiceRepository")
 * @ORM\Table(name="`invoices`", indexes={@ORM\Index(name="invoices_created_at_index", columns={"`created_at`"}), @ORM\Index(name="invoices_user_id_index", columns={"`user_id`"}), @ORM\Index(name="invoices_team_id_index", columns={"`team_id`"})})
 */
class Invoice extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`user_id`", type="integer", nullable=true)
     */
    protected $user_id;

    /**
     * @ORM\Column(name="`team_id`", type="integer", nullable=true)
     */
    protected $team_id;

    /**
     * @ORM\Column(name="`provider_id`", type="string", length=255)
     */
    protected $provider_id;

    /**
     * @ORM\Column(name="`total`", type="decimal", precision=8, scale=2, nullable=true)
     */
    protected $total;

    /**
     * @ORM\Column(name="`tax`", type="decimal", precision=8, scale=2, nullable=true)
     */
    protected $tax;

    /**
     * @ORM\Column(name="`card_country`", type="string", length=255, nullable=true)
     */
    protected $card_country;

    /**
     * @ORM\Column(name="`billing_state`", type="string", length=255, nullable=true)
     */
    protected $billing_state;

    /**
     * @ORM\Column(name="`billing_zip`", type="string", length=255, nullable=true)
     */
    protected $billing_zip;

    /**
     * @ORM\Column(name="`billing_country`", type="string", length=255, nullable=true)
     */
    protected $billing_country;

    /**
     * @ORM\Column(name="`vat_id`", type="string", length=50, nullable=true)
     */
    protected $vat_id;

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
     * @return \App\Entities\Base\Invoice
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
     * Set the value of user_id.
     *
     * @param integer $user_id
     * @return \App\Entities\Base\Invoice
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get the value of user_id.
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the value of team_id.
     *
     * @param integer $team_id
     * @return \App\Entities\Base\Invoice
     */
    public function setTeamId($team_id)
    {
        $this->team_id = $team_id;

        return $this;
    }

    /**
     * Get the value of team_id.
     *
     * @return integer
     */
    public function getTeamId()
    {
        return $this->team_id;
    }

    /**
     * Set the value of provider_id.
     *
     * @param string $provider_id
     * @return \App\Entities\Base\Invoice
     */
    public function setProviderId($provider_id)
    {
        $this->provider_id = $provider_id;

        return $this;
    }

    /**
     * Get the value of provider_id.
     *
     * @return string
     */
    public function getProviderId()
    {
        return $this->provider_id;
    }

    /**
     * Set the value of total.
     *
     * @param float $total
     * @return \App\Entities\Base\Invoice
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get the value of total.
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set the value of tax.
     *
     * @param float $tax
     * @return \App\Entities\Base\Invoice
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get the value of tax.
     *
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set the value of card_country.
     *
     * @param string $card_country
     * @return \App\Entities\Base\Invoice
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
     * Set the value of billing_state.
     *
     * @param string $billing_state
     * @return \App\Entities\Base\Invoice
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
     * @return \App\Entities\Base\Invoice
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
     * @return \App\Entities\Base\Invoice
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
     * @return \App\Entities\Base\Invoice
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
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\Invoice
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
     * @return \App\Entities\Base\Invoice
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
        return array('id', 'user_id', 'team_id', 'provider_id', 'total', 'tax', 'card_country', 'billing_state', 'billing_zip', 'billing_country', 'vat_id', 'created_at', 'updated_at');
    }
}