<?php

namespace App\Entities\Base;

use Carbon\Carbon;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use JsonSerializable;
use stdClass;

abstract class AbstractEntity implements Jsonable, JsonSerializable
{
    /** Excluded field name constants */
    const INITIALIZER_FIELD = '__initializer__';
    const CLONER_FIELD      = '__cloner__';
    const IS_INITIALIZED    = '__isInitialized__';
    const PASSWORD_FIELD    = 'password';

    /** TINYINT deleted field value constants */
    const IS_DELETED  = 1;
    const NOT_DELETED = 0;

    /** Column name constants for database columns that are common to most tables */
    const ID         = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /** @var integer */
    protected $id;

    /** @var DateTime */
    protected $created_at;
    
    /** @var DateTime */
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
     * Sanitise an incoming date string
     *
     * @param $dateString
     * @return Carbon|null
     */
    protected function sanitiseDate($dateString)
    {
        if ($dateString instanceof Carbon || $dateString instanceof DateTime) {
            return $dateString;
        }

        // Check for a valid date format and return a Carbon instance if this is a valid date string
        if (strtotime($dateString) !== false) {
            return new Carbon($dateString);
        }

        // For Nexpose: Try a string truncated to 15 characters and if that is not a valid date, return null
        $dateString = substr($dateString, 0, 15);
        if (strtotime($dateString) === false) {
            return null;
        }

        return new Carbon($dateString);
    }

    /**
     * Generate an array representation of the object
     *
     * @param bool $excludeNulls
     * @return array
     */
    public function toArray(bool $excludeNulls = false)
    {
        if ($excludeNulls === false) {
            return get_object_vars($this);
        }

        return collect(get_object_vars($this))->filter(function ($value) {
            return (isset($value) && !($value instanceof ArrayCollection))
                || ($value instanceof ArrayCollection && !$value->isEmpty());
        })->all();
    }

    /**
     * Alias for the toArray function when using an Entity as a model during parsing
     *
     * @return array
     */
    public function export()
    {
        return $this->toArray();
    }

    /**
     * Set the values of the entity by passing in an array
     *
     * @param array $params
     * @return $this
     */
    public function setFromArray(array $params)
    {
        if (empty($params)) {
            return $this;
        }

        $members = new Collection($params);
        $members->filter(function ($memberValue, $memberName){
            return property_exists($this, $memberName) && isset($memberValue);
        })->each(function ($memberValue, $memberName) {
            $this->$memberName = $memberValue;
            return true;
        });

        return $this;
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

        $members = collect($this->toArray());
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

            if ($memberValue instanceof PersistentCollection || $memberValue instanceof ArrayCollection) {
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

            if ($memberName === 'path') {
                $memberValue = basename($memberValue);
                $memberName  = 'filename';
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
     * Implementation of hashing functionality so the majority of the code is not repeated
     *
     * @param Collection $uniqueColumns
     * @return string
     */
    protected static function generateUniqueHash(Collection $uniqueColumns)
    {
        // Create a SHA1 hash of the property values that constitute a unique key by iterating over the columns, getting
        // the spl_object_hash of any keys that contain objects and then imploding the Collection to a string where the
        // values of the relevant properties are concatenated using a ":" character
        $objectHashes = $uniqueColumns->filter(function ($value) {
            return is_object($value);
        })->map(function ($value) {
            return spl_object_hash($value);
        });

        return sha1(
            $uniqueColumns->filter(function ($value) {
                return isset($value) && !is_object($value);
            })->merge($objectHashes)->implode(":")
        );
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
     * @return bool
     */
    public function hasMinimumRequiredPropertiesSet(): bool
    {
        return true;
    }
}