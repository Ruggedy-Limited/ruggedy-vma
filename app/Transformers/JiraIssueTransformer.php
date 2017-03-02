<?php

namespace App\Transformers;

use App\Entities\JiraIssue;
use League\Fractal\TransformerAbstract;

class JiraIssueTransformer extends TransformerAbstract
{
    /**
     * Tranform a a JiraIssue entity for API responses
     *
     * @param JiraIssue $jiraIssue
     * @return array
     */
    public function transform(JiraIssue $jiraIssue)
    {
        return [
            'id'               => $jiraIssue->getId(),
            'issueId'          => $jiraIssue->getIssueId(),
            'issueKey'         => $jiraIssue->getIssueKey(),
            'issueType'        => $jiraIssue->getIssueType(),
            'issueStatus'      => $jiraIssue->getIssueStatus(),
            'issueSummary'     => $jiraIssue->getSummary(),
            'issueDescription' => $jiraIssue->getDescription(),
            'projectKey'       => $jiraIssue->getProjectKey(),
            'jiraHost'         => $jiraIssue->getHost(),
            'jiraPort'         => $jiraIssue->getPort(),
            'apiRequestStatus' => $jiraIssue->getRequestStatus(),
            'apiRequestType'   => $jiraIssue->getRequestType(),
            'failureReason'    => $jiraIssue->getFailureReason(),
            'createdDate'      => $jiraIssue->getCreatedAt()->format(env('APP_DATE_FORMAT')),
            'modifiedDate'     => $jiraIssue->getUpdatedAt()->format(env('APP_DATE_FORMAT')),
        ];
    }
}