<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * App\Entities\JiraIssue
 *
 * @ORM\Entity(repositoryClass="App\Repositories\JiraIssueRepository")
 * @ORM\HasLifecycleCallbacks
 */
class JiraIssue extends Base\JiraIssue
{
    /** JIRA issue status constants */
    const ISSUE_STATUS_OPEN        = 'open';
    const ISSUE_STATUS_IN_PROGRESS = 'in progress';
    const ISSUE_STATUS_RESOLVED    = 'resolved';
    const ISSUE_STATUS_CLOSED      = 'closed';
    const ISSUE_STATUS_REOPENED    = 'reopened';
    
    /** API Request status constants */
    const REQUEST_STATUS_PENDING     = 'pending';
    const REQUEST_STATUS_IN_PROGRESS = 'in progress';
    const REQUEST_STATUS_FAILED      = 'failed';
    const REQUEST_STATUS_SUCCESS     = 'success';

    /** JIRA API request types */
    const REQUEST_TYPE_SEARCH = 'search';
    const REQUEST_TYPE_CREATE = 'create';
    const REQUEST_TYPE_UPDATE = 'update';
}