<?php

namespace Tests\Acceptance\Features\Bootstrap;

use App\Exceptions\FeatureBackgroundSetupFailedException;
use App\Exceptions\InvalidConfigurationException;
use App\Project;
use App\Team;
use App\User;
use App\Workspace;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Doctrine\ORM\EntityManager;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    /** @var  string */
    protected $apiKey;

    protected $tablesToTruncate = [
        'users',
        'teams',
        'team_users',
        'workspaces',
        'components',
        'component_permissions',
        'assets',
        'files',
        'scanner_apps',
        'vulnerabilities',
        'vulnerability_reference_codes',
        'open_ports'
    ];

    /**
     * Initializes context.
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * Truncate all relevant tables before each scenario
     *
     * @BeforeScenario
     */
    public function truncateTables()
    {
        // Disable foreign key checks otherwise the TRUNCATE will fail with an integrity constraint violation
        Schema::disableForeignKeyConstraints();

        foreach ($this->getTablesToTruncate() as $table) {
            DB::table($table)->truncate();
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * @Given /^the following existing ([^"]*):$/
     *
     * @param $objectType
     * @param TableNode $table
     * @return bool
     * @throws FeatureBackgroundSetupFailedException
     * @throws InvalidConfigurationException
     */
    public function theFollowingExistingThings($objectType, TableNode $table)
    {
        $modelClassPath = $this->getEloquentModelClassHelper($objectType);

        /** @var Model $model */
        $model = new $modelClassPath();

        // If we have a valid Eloquent Model
        if ($model instanceof Model) {
            // Disable foreign key checks so that this doesn't fail for the wrong reasons
            Schema::disableForeignKeyConstraints();

            foreach ($table as $row) {
                // Create ans save each row using Eloquent ORM
                $row   = $this->sanitiseRowHelper($row);
                $model = $modelClassPath::forceCreate($row);
                $model->saveOrFail();
            }

            // Re-enable foreign key checks
            Schema::enableForeignKeyConstraints();

            return true;
        }

        return false;
    }

    /**
     * @Given /^the following ([A-Za-z]*) in ([A-Za-z]*) (.*):$/
     * 
     * @param $manyObject
     * @param $oneObject
     * @param $pivotId
     * @param TableNode $table
     */
    public function andTheFollowingRelations($manyObject, $oneObject, $pivotId, TableNode $table)
    {
        // Get the fully qualified class paths for the one and many relation Eloquent Models
        $oneClassPath  = $this->getEloquentModelClassHelper($oneObject);
        $manyMethod    = $this->getEloquentManyRelationsMethodHelper($manyObject);

        // Get the relevant owning Model from the database
        $oneModel = $oneClassPath::find($pivotId);
        $attachments = [];

        // Disable foreign key checks so that this doesn't fail for the wrong reasons
        Schema::disableForeignKeyConstraints();

        foreach ($table as $row) {
            $row = $this->sanitiseRowHelper($row);
            $id = $row['id'];
            unset($row['id']);
            $attachments[$id] = $row;
        }

        // Try to save the relational data to the database
        try {
            $oneModel->$manyMethod()->attach($attachments);
            // Re-enable foreign key checks
            Schema::enableForeignKeyConstraints();
        } catch (QueryException $e) {
            // Just catch the exception in the case of integrity constraint violations
            // Re-enable foreign key checks
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Iterate over the columns in a row and convert 'true' or 'false' string values to proper bools
     *
     * @param array $row
     * @return array
     */
    protected function sanitiseRowHelper(array $row)
    {
        if (empty($row)) {
            return $row;
        }

        foreach ($row as $index => $value) {
            if (!is_scalar($value)) {
                continue;
            }

            $row[$index] = $this->convertBoolHelper($value);
            if (is_int($row[$index])) {
                $row[$index] = $this->convertIntHelper($value);
            }
        }

        return $row;
    }

    /**
     * Helper method to convert 'true' or 'false' passed as strings from the feature file to proper boolean values
     *
     * @param $value
     * @return bool
     */
    protected function convertBoolHelper($value)
    {
        if ($value === 'true') {
            return true;
        }

        if ($value === 'false') {
            return false;
        }

        if ($value === 'NULL') {
            return null;
        }

        return $value;
    }

    /**
     * Helper method to convert integer values passed as strings from the feature file to proper integer values
     *
     * @param $value
     * @return int
     */
    protected function convertIntHelper($value)
    {
        if (preg_match("/^[1-9][\d]*$/", $value) && !is_bool($value)) {
            return intval($value);
        }

        return $value;
    }

    /**
     * Get the full class path for an Eloquent model
     *
     * @param string $objectType
     * @return string
     * @throws FeatureBackgroundSetupFailedException
     * @throws InvalidConfigurationException
     */
    protected function getEloquentModelClassHelper(string $objectType): string
    {
        $rootNamespace = env('APP_TEST_MODEL_NAMESPACE');
        if (empty($rootNamespace)) {
            throw new InvalidConfigurationException("Please add APP_MODEL_NAMESPACE to your .env file");
        }

        if (!empty($objectType) && substr($objectType, strlen($objectType) - 1, 1) === "s") {
            $objectType = substr($objectType, 0, strlen($objectType) - 1);
        }

        $modelClassPath = $rootNamespace . "\\" . $objectType;

        if (!class_exists($modelClassPath)) {
            throw new FeatureBackgroundSetupFailedException("The '$modelClassPath' model does not exist.");
        }

        return $modelClassPath;
    }

    /**
     * Get the Eloquent Model method to get the relations for a one-to-many relationship
     *
     * @param string $objectType
     * @return string
     */
    protected function getEloquentManyRelationsMethodHelper(string $objectType): string
    {
        if (!empty($objectType) && substr($objectType, strlen($objectType) - 1, 1) === "s") {
            return strtolower($objectType);
        }

        return strtolower($objectType) . "s";
    }

    /**
     * @Given a valid API key :apiKey
     */
    public function aValidApiKey($apiKey)
    {
        $this->setApiKey($apiKey);
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return array
     */
    public function getTablesToTruncate()
    {
        return $this->tablesToTruncate;
    }
}
