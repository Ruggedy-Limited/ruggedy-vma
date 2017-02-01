<?php

namespace App\Http\Controllers\Api;

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
     * @param $workspaceId
     * @return ResponseFactory|JsonResponse
     */
    public function searchJiraTickets()
    {

    }

    /**
     * Create a Jira ticket
     *
     * @POST("/jira/create/{fileId}/{vulnerabilityId}", as="jira.create", where={"fileId":"[0-9]+", "vulnerabilityId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return ResponseFactory|JsonResponse
     */
    public function createJiraTicket()
    {

    }

    /**
     * Edit a Jira ticket
     *
     * @POST("/jira/edit/{jiraId}", as="jira.edit", where={"jiraId":"[0-9]+"})
     *
     * @param $workspaceId
     * @return ResponseFactory|JsonResponse
     */
    public function editJiraTicket()
    {

    }

    /**
     * @inheritdoc
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [];
    }

}