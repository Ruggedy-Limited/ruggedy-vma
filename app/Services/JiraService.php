<?php

namespace App\Services;

use App\Entities\Asset;
use App\Entities\Exploit;
use App\Entities\File;
use App\Entities\JiraIssue;
use App\Entities\Vulnerability;
use App\Entities\VulnerabilityReferenceCode;
use Univerze\Jira\Jira;

class JiraService
{
    /** @var Jira */
    private $jira;

    /**
     * JiraService constructor.
     *
     * @param Jira $jira
     */
    public function __construct(Jira $jira)
    {
        $this->jira = $jira;
    }

    /**
     * Initialise the service
     *
     * @param string $username
     * @param string $password
     * @param string $hostname
     * @param int $port
     * @return bool
     */
    public function initialise(string $username, string $password, string $hostname, int $port): bool
    {
        return $this->jira->initialise($username, $password, $hostname, $port);
    }

    /**
     * @param string $searchPhrase
     * @return mixed
     */
    public function searchJiraIssues(string $searchPhrase)
    {
        //TODO: Implement the search request
    }

    /**
     * Create a Jira issue via the Jira REST API
     *
     * @param JiraIssue $jiraIssue
     * @return mixed
     */
    public function createJiraIssue(JiraIssue $jiraIssue)
    {
        // Send the Issue to JIRA
        return $this->jira->create([
            'project'     => [
                'key' => $jiraIssue->getProjectKey()
            ],
            'summary'     => $jiraIssue->getSummary(),
            'description' => $jiraIssue->getDescription(),
            'issuetype'   => [
                'name' => $jiraIssue->getIssueType()
            ]
        ]);
    }

    /**
     * @param int $jiraId
     * @return mixed
     */
    public function editJiraIssue(int $jiraId)
    {
        //TODO: Implement Jira issue editing request
    }

    /**
     * Generate issue summary from User input or Vulnerability name
     *
     * @param Vulnerability $vulnerability
     * @param File $file
     * @param JiraIssue $jiraIssue
     * @return string
     */
    public function getIssueSummaryText(Vulnerability $vulnerability, File $file, JiraIssue $jiraIssue): string
    {
        return $summary = $jiraIssue->getSummary() ?? $vulnerability->getName() . " found by "
            . ucwords($file->getWorkspaceApp()->getScannerApp()->getName());
    }

    /**
     * Get the complete issue description for Jira
     *
     * @param JiraIssue $jiraIssue
     * @param Vulnerability $vulnerability
     * @param File $file
     * @return string
     */
    public function getIssueDescriptionText(Vulnerability $vulnerability, File $file, JiraIssue $jiraIssue): string
    {
        return $jiraIssue->getDescription() . PHP_EOL . PHP_EOL
            . "*Vulnerability Name:*" . PHP_EOL . $vulnerability->getName() . PHP_EOL . PHP_EOL
            . "*Vulnerability Description:*" . PHP_EOL . strip_tags($vulnerability->getDescription()) . PHP_EOL . PHP_EOL
            . "*Vulnerability Severity:*" . PHP_EOL . $vulnerability->getSeverityText() . PHP_EOL . PHP_EOL
            . "*Vulnerability References:*" . PHP_EOL
                . $this->getVulnerabilityReferencesText($vulnerability) . PHP_EOL . PHP_EOL
            . "*CVSS Score:*" . PHP_EOL . $vulnerability->getCvssScore() . PHP_EOL . PHP_EOL
            . "*Resolution:*" . PHP_EOL . strip_tags($vulnerability->getSolution()) . PHP_EOL . PHP_EOL
            . "*Vulnerable Assets:*" . PHP_EOL . $this->getVulnerableAssetsText($vulnerability) . PHP_EOL . PHP_EOL
            . "*Exploits:*" . PHP_EOL . $this->getExploitsText($vulnerability) . PHP_EOL . PHP_EOL
            . "*Found in File:*" . PHP_EOL . basename($file->getPath()) . PHP_EOL . PHP_EOL
            . "*Scanner App:*" . PHP_EOL . ucwords($file->getWorkspaceApp()->getScannerApp()->getName())
            . PHP_EOL . PHP_EOL;
    }

    /**
     * Generate a Vulnerable Assets string
     *
     * @param Vulnerability $vulnerability
     * @return string
     */
    protected function getVulnerableAssetsText(Vulnerability $vulnerability): string
    {
        $vulnerableAssets = collect($vulnerability->getAssets()->toArray())
            ->map(function ($asset) {
                /** @var Asset $asset */
                return $asset->getName();
            })
            ->unique()
            ->implode(PHP_EOL);

        if (empty($vulnerableAssets)) {
            $vulnerableAssets = "No vulnerable Assets found for now.";
        }

        return $vulnerableAssets;
    }

    /**
     * Get a list of exploits from the Jira issue description
     *
     * @param Vulnerability $vulnerability
     * @return string
     */
    protected function getExploitsText(Vulnerability $vulnerability): string
    {
        $exploits = collect($vulnerability->getExploits()->toArray())
            ->map(function ($exploit) {
                /** @var Exploit $exploit */
                $skillLevel = '';
                if (!empty($exploit->getSkillLevel())) {
                    $skillLevel = "({$exploit->getSkillLevel()})";
                }

                return $exploit->getTitle() . $skillLevel . PHP_EOL
                    . $exploit->getUrlReference();
            })
            ->unique()
            ->implode(PHP_EOL);

        if (empty($exploits)) {
            $exploits = "Currently no exploits in our database.";
        }

        return $exploits;
    }

    /**
     * Get a list of Vulnerability Reference Codes for the Jira issue description
     *
     * @param Vulnerability $vulnerability
     * @return string
     */
    protected function getVulnerabilityReferencesText(Vulnerability $vulnerability): string
    {
        $vulnerabilityReferences = collect($vulnerability->getVulnerabilityReferenceCodes()->toArray())
            ->map(function ($vulnerabilityReference) {
                /** @var VulnerabilityReferenceCode $vulnerabilityReference */
                return " - " . $vulnerabilityReference->getValue();
            })
            ->unique()
            ->implode(PHP_EOL);

        if (empty($vulnerabilityReferences)) {
            $vulnerabilityReferences = "No references found.";
        }

        return $vulnerabilityReferences;
    }

    /**
     * @return Jira
     */
    public function getJira(): Jira
    {
        return $this->jira;
    }
}