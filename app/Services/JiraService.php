<?php

namespace App\Services;

use App\Entities\File;
use App\Entities\Vulnerability;

class JiraService
{
    /**
     * JiraService constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $searchPhrase
     * @return mixed
     */
    public function searchJiraTickets(string $searchPhrase)
    {

    }

    /**
     * @param File $file
     * @param Vulnerability $vulnerability
     * @return mixed
     */
    public function createJiraTicket(File $file, Vulnerability $vulnerability)
    {

    }

    /**
     * @param int $jiraId
     * @return mixed
     */
    public function editJiraTicket(int $jiraId)
    {

    }
}