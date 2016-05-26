<?php

namespace App\Entities\Base;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;
use stdClass;


abstract class AbstractEntity implements Jsonable, JsonSerializable
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

    /**
     * Generate an array representation of the object
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * Set the values of the entity by passing in an array
     *
     * @param array $params
     * @return bool
     */
    public function setFromArray(array $params)
    {
        if (empty($params)) {
            return false;
        }

        $members = new Collection($params);
        $members->each(function($memberValue, $memberName)
        {
            if (!property_exists($this, $memberName)) {
                return;
            }

            $this->$memberName = $memberValue;
        });

        return true;
    }

    /**
     * Implementation for the Jsonable interface
     *
     * @param int $options
     * @param array $onlyTheseAttributes
     * @return string
     */
    public function toJson($options = 0, $onlyTheseAttributes = [])
    {
        $objectForJson = $this->toStdClass($onlyTheseAttributes);
        return json_encode($objectForJson, $options);
    }

    /**
     * Implementation for the JsonSerializable interface
     *
     * @return mixed
     */
    function jsonSerialize()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Create a stdClass object of $this so that all the members are public and can be converted to a string
     *
     * @param array $onlyTheseAttributes
     * @return stdClass
     */
    protected function toStdClass($onlyTheseAttributes = [])
    {
        $objectForJson = new stdClass();

        $members = new Collection($this->toArray());
        $members->filter(function($memberValue, $memberName) use ($onlyTheseAttributes)
        {
            // If the $onlyTheseAttributes filter is not set, return all the members
            if (empty($onlyTheseAttributes)) {
                return true;
            }

            // Return only members present in the $onlyTheseAttributes array
            return in_array($memberName, $onlyTheseAttributes);

        })->each(function($memberValue, $memberName) use ($objectForJson)
        {
            // If this member is an object but does not have a getId() method, exit early because we cant get a scalar
            // value for the JSON representation
            if (is_object($memberValue) && !method_exists($memberValue, 'getId')) {
                return;
            }

            // This member is an entity so extract the ID
            if (is_object($memberValue)) {
                $memberValue = $memberValue->getId();
                // Append '_id' if the member was an entity and we've extracted the ID
                $memberName .= '_id';
            }

            // Exclude members that aren't set
            if (!isset($memberValue)) {
                return;
            }

            $objectForJson->$memberName = $memberValue;
        });

        return $objectForJson;
    }
}