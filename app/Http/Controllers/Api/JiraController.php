<?php

namespace App\Http\Controllers\Api;

use App\Commands\CreateJiraTicket;
use App\Entities\JiraIssue;
use App\Transformers\JiraIssueTransformer;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

/**
 * @Controller(prefix="api")
 * @Middleware("auth:api")
 */
class JiraController extends AbstractController
{
    /**
     * Search Jira tickets
     *
     * @POST("/jira/search/{searchPhrase}", as="jira.search", where={"searchPhrase":"[0-9A-Za-z\s\-_]+"})
     *
     * @return ResponseFactory|JsonResponse
     */
    public function searchJiraTickets()
    {
        //TODO: Implement the search controller
    }

    /**
     * Create a Jira ticket
     *
     * @POST("/jira/create/{fileId}/{vulnerabilityId}", as="jira.create", where={"fileId":"[0-9]+", "vulnerabilityId":"[0-9]+"})
     *
     * @param $fileId
     * @param $vulnerabilityId
     * @return ResponseFactory|JsonResponse
     */
    public function createJiraTicket($fileId, $vulnerabilityId)
    {
        $jiraIssue = new JiraIssue();
        $jiraIssue
            ->setRequestType(JiraIssue::REQUEST_TYPE_CREATE)
            ->setRequestStatus(JiraIssue::REQUEST_STATUS_PENDING)
            ->setHost($this->request->get('jira-hostname'))
            ->setPort($this->request->get('jira-port'))
            ->setIssueType($this->request->get('jira-issue-type', 'Bug'))
            ->setProjectKey($this->request->get('jira-project-key'));

        $command = new CreateJiraTicket(
            $fileId,
            $vulnerabilityId,
            $this->request->get('jira-username'),
            $this->request->get('jira-password'),
            $jiraIssue
        );

        return $this->sendCommandToBusHelper($command, new JiraIssueTransformer());
    }

    /**
     * Edit a Jira ticket
     *
     * @POST("/jira/edit/{jiraId}", as="jira.edit", where={"jiraId":"[0-9]+"})
     *
     * @return ResponseFactory|JsonResponse
     */
    public function editJiraTicket()
    {
        //TODO: Implement the update controller
    }

    /**
     * @inheritdoc
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            'jira-username' => 'bail|filled',
            'jira-password' => 'bail|filled',
            'jira-hostname' => 'bail|filled|url',
            'jira-port'     => 'bail|filled|int',
        ];
    }

}