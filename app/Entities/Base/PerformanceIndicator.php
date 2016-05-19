<?php

namespace App\Entities\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\Base\PerformanceIndicator
 *
 * @ORM\Entity(repositoryClass="App\Repositories\PerformanceIndicatorRepository")
 * @ORM\Table(name="`performance_indicators`", indexes={@ORM\Index(name="performance_indicators_created_at_index", columns={"`created_at`"})})
 */
class PerformanceIndicator extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="`id`", type="integer", options={"unsigned":true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="`monthly_recurring_revenue`", type="decimal", precision=8, scale=2)
     */
    protected $monthly_recurring_revenue;

    /**
     * @ORM\Column(name="`yearly_recurring_revenue`", type="decimal", precision=8, scale=2)
     */
    protected $yearly_recurring_revenue;

    /**
     * @ORM\Column(name="`daily_volume`", type="decimal", precision=8, scale=2)
     */
    protected $daily_volume;

    /**
     * @ORM\Column(name="`new_users`", type="integer")
     */
    protected $new_users;

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
     * @return \App\Entities\Base\PerformanceIndicator
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
     * Set the value of monthly_recurring_revenue.
     *
     * @param float $monthly_recurring_revenue
     * @return \App\Entities\Base\PerformanceIndicator
     */
    public function setMonthlyRecurringRevenue($monthly_recurring_revenue)
    {
        $this->monthly_recurring_revenue = $monthly_recurring_revenue;

        return $this;
    }

    /**
     * Get the value of monthly_recurring_revenue.
     *
     * @return float
     */
    public function getMonthlyRecurringRevenue()
    {
        return $this->monthly_recurring_revenue;
    }

    /**
     * Set the value of yearly_recurring_revenue.
     *
     * @param float $yearly_recurring_revenue
     * @return \App\Entities\Base\PerformanceIndicator
     */
    public function setYearlyRecurringRevenue($yearly_recurring_revenue)
    {
        $this->yearly_recurring_revenue = $yearly_recurring_revenue;

        return $this;
    }

    /**
     * Get the value of yearly_recurring_revenue.
     *
     * @return float
     */
    public function getYearlyRecurringRevenue()
    {
        return $this->yearly_recurring_revenue;
    }

    /**
     * Set the value of daily_volume.
     *
     * @param float $daily_volume
     * @return \App\Entities\Base\PerformanceIndicator
     */
    public function setDailyVolume($daily_volume)
    {
        $this->daily_volume = $daily_volume;

        return $this;
    }

    /**
     * Get the value of daily_volume.
     *
     * @return float
     */
    public function getDailyVolume()
    {
        return $this->daily_volume;
    }

    /**
     * Set the value of new_users.
     *
     * @param integer $new_users
     * @return \App\Entities\Base\PerformanceIndicator
     */
    public function setNewUsers($new_users)
    {
        $this->new_users = $new_users;

        return $this;
    }

    /**
     * Get the value of new_users.
     *
     * @return integer
     */
    public function getNewUsers()
    {
        return $this->new_users;
    }

    /**
     * Set the value of created_at.
     *
     * @param \DateTime $created_at
     * @return \App\Entities\Base\PerformanceIndicator
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
     * @return \App\Entities\Base\PerformanceIndicator
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
        return array('id', 'monthly_recurring_revenue', 'yearly_recurring_revenue', 'daily_volume', 'new_users', 'created_at', 'updated_at');
    }
}