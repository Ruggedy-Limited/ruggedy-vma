<?php

namespace App\Commands;

use App\Entities\JiraIssue;

class CreateJiraTicket extends Command
{
    /** @var int */
    protected $vulnerabilityId;

    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /** @var JiraIssue */
    protected $jiraIssue;

    /**
     * CreateJiraTicket constructor.
     *
     * @param int $fileId
     * @param int $vulnerabilityId
     * @param string $username
     * @param string $password
     * @param JiraIssue $jiraIssue
     */
    public function __construct(
        int $vulnerabilityId, string $username, string $password, JiraIssue $jiraIssue
    )
    {
        $this->vulnerabilityId = $vulnerabilityId;
        $this->username        = $username;
        $this->password        = $password;
        $this->jiraIssue       = $jiraIssue;
    }

    /**
     * @return int
     */
    public function getVulnerabilityId(): int
    {
        return $this->vulnerabilityId;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return JiraIssue
     */
    public function getJiraIssue(): JiraIssue
    {
        return $this->jiraIssue;
    }
}