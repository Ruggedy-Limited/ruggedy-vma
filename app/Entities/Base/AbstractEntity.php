<?php

namespace App\Entities\Base;

use DateTime;


abstract class AbstractEntity
{
    /**
     * @var \DateTime
     */
    protected $createdAt;
    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @ORM\PreFlush
     */
    public function timestamp()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new DateTime();
        }

        $this->updatedAt = new DateTime();
    }

    /**
     * Format the date to Y-m-d H:i:s
     *
     * @param $date
     * @param bool $hours
     * @return bool
     */
    public function formatDate($date, $hours = false)
    {
        if (empty($date) || !$date instanceof DateTime) {
            return false;
        }

        $format = 'Y-m-d';
        if ($hours) {
            $format = "$format H:i:s";
        }

        return $date->format($format);
    }
}