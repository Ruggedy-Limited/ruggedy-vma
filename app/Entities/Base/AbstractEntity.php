<?php

namespace App\Entities\Base;

use App\Contracts\HasGetId;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;
use stdClass;


abstract class AbstractEntity implements Jsonable, JsonSerializable, HasGetId
{
    /** Excluded field name constants */
    const INITIALIZER_FIELD = '__initializer__';
    const CLONER_FIELD      = '__cloner__';
    const IS_INITIALIZED    = '__isInitialized__';
    const PASSWORD_FIELD    = 'password';

    /** TINYINT deleted field value constants */
    const IS_DELETED  = 1;
    const NOT_DELETED = 0;

    /** @var integer */
    protected $id;

    /**
     * @var DateTime
     */
    protected $created_at;
    /**
     * @var DateTime
     */
    protected $updated_at;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @ORM\PreFlush
     */
    public function timestamp()
    {
        if (empty($this->created_at)) {
            $this->created_at = new DateTime();
        }

        $this->updated_at = new DateTime();
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
            $format .= " H:i:s";
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
    public function jsonSerialize()
    {
        return $this->toStdClass();
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
    public function toStdClass($onlyTheseAttributes = [])
    {
        $objectForJson = new stdClass();

        $members = new Collection($this->toArray());
        $members->filter(function($memberValue, $memberName) use ($onlyTheseAttributes)
        {
            // Explicitly excluded items
            $excludedSearchResult = $this->getExcludedFields()->search($memberName);
            if ($excludedSearchResult !== false) {
                return false;
            }

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
            if ($memberValue instanceof DateTime || $memberValue instanceof Carbon) {
                $memberValue = $memberValue->format(env('APP_DATE_FORMAT'));
            }

            if ($memberValue instanceof PersistentCollection) {
                $collectionForEncoding = [];
                /** @var AbstractEntity $entity */
                foreach ($memberValue->toArray() as $entity) {
                    $collectionForEncoding[] = $entity->toStdClass(['id', 'name', 'created_at', 'updated_at']);
                }

                $memberValue = $collectionForEncoding;
            }

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

    /**
     * @return Collection
     */
    protected function getExcludedFields()
    {
        return new Collection([
            self::INITIALIZER_FIELD,
            self::CLONER_FIELD,
            self::IS_INITIALIZED,
            self::PASSWORD_FIELD
        ]);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}