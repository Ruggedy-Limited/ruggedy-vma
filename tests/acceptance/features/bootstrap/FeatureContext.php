<?php

namespace Tests\Acceptance\Features\Bootstrap;

use App\Team;
use App\User;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Illuminate\Support\Facades\DB;


/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    /** @var  string */
    protected $apiKey;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @beforeScenario
     */
    public function prepareScenario()
    {
        DB::beginTransaction();
    }

    /**
     * @afterScenario
     */
    public function cleanupScenario()
    {
        DB::rollBack();
    }

    /**
     * @Given the following existing users:
     */
    public function theFollowingExistingUsers(TableNode $table)
    {
        foreach ($table as $row) {
            $user = new User($row);
            $user->saveOrFail();
        }
    }

    /**
     * @Given the following existing teams:
     */
    public function theFollowingExistingTeams(TableNode $table)
    {
        foreach ($table as $row) {
            $team = new Team($row);
            $team->saveOrFail();
        }
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
}
