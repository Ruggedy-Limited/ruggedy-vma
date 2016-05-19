<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\TeamSubscription
 *
 * @ORM\Entity(repositoryClass="App\Repositories\TeamSubscriptionRepository")
 * @ORM\Table(name="`team_subscriptions`")
 */
class TeamSubscription extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`team_id`", type="integer")
     */
    protected $team_id;

    /**
     * @ORM\Column(name="`name`", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(name="`stripe_id`", type="string", length=255)
     */
    protected $stripe_id;

    /**
     * @ORM\Column(name="`stripe_plan`", type="string", length=255)
     */
    protected $stripe_plan;

    /**
     * @ORM\Column(name="`quantity`", type="integer")
     */
    protected $quantity;

    /**
     * @ORM\Column(name="`trial_ends_at`", type="datetime", nullable=true)
     */
    protected $trial_ends_at;

    /**
     * @ORM\Column(name="`ends_at`", type="datetime", nullable=true)
     */
    protected $ends_at;

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
     * @return \App\Entities\Base\TeamSubscription
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
     * Set the value of team_id.
     *
     * @param integer $team_id
     * @return \App\Entities\Base\TeamSubscription
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
     * Set the value of name.
     *
     * @param string $name
     * @return \App\Entities\Base\TeamSubscription
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
     * Set the value of stripe_id.
     *
     * @param string $stripe_id
     * @return \App\Entities\Base\TeamSubscription
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
     * Set the value of stripe_plan.
     *
     * @param string $stripe_plan
     * @return \App\Entities\Base\TeamSubscription
     */
    public function setStripePlan($stripe_plan)
    {
        $this->stripe_plan = $stripe_plan;

        return $this;
    }

    /**
     * Get the value of stripe_plan.
     *
     * @return string
     */
    public function getStripePlan()
    {
        return $this->stripe_plan;
    }

    /**
     * Set the value of quantity.
     *
     * @param integer $quantity
     * @return \App\Entities\Base\TeamSubscription
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get the value of quantity.
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set the value of trial_ends_at.
     *
     * @param \DateTime $trial_ends_at
     * @return \App\Entities\Base\TeamSubscription
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
     * Set the value of ends_at.
     *
     * @param \DateTime $ends_at
     * @return \App\Entities\Base\TeamSubscription
     */
    public function setEndsAt($ends_at)
    {
        $this->ends_at = $ends_at;

        return $this;
    }

    /**
     * Get the value of ends_at.
     *
     * @return \DateTime
     */
    public function getEndsAt()
    {
        return $this->ends_at;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\TeamSubscription
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
     * @return \App\Entities\Base\TeamSubscription
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
        return array('id', 'team_id', 'name', 'stripe_id', 'stripe_plan', 'quantity', 'trial_ends_at', 'ends_at', 'created_at', 'updated_at');
    }
}