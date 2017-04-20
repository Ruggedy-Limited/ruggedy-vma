<?php

namespace App\Http\Controllers;

use App\Commands\CreateJiraTicket;
use App\Entities\JiraIssue;
use App\Http\Responses\AjaxResponse;
use App\Http\Responses\ErrorResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class JiraController extends AbstractController
{
    /**
     * Send an API call to the Jira REST API to create a new issue from this vulnerability
     *
     * @POST("/jira-issue/create/{vulnerabilityId}", as="jira.create", where={"vulnerabilityId":"[0-9]+"})
     *
     * @param $vulnerabilityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendToJira($vulnerabilityId)
    {
        // Only allow ajax requests to access this endpoint
        if (!$this->request->ajax()) {
            throw new MethodNotAllowedHttpException([], "That route cannot be accessed in that way.");
        }

        // Start creating a new Jira Issue and populate it with data from the request
        $jiraIssue = new JiraIssue();
        $jiraIssue->setHost($this->request->get('host'))
                  ->setPort($this->request->get('port'))
                  ->setProjectKey($this->request->get('project-id'))
                  ->setIssueType('Bug')
                  ->setRequestType(JiraIssue::REQUEST_TYPE_CREATE)
                  ->setRequestStatus(JiraIssue::REQUEST_STATUS_FAILED);

        $ajaxResponse = new AjaxResponse();

        // Do validation but catch the ValidationExceptions to handle them here ourselves
        // because this is a JSON response to an ajax request
        try {
            $this->validate($this->request, $this->getValidationRules(), $this->getValidationMessages());
        } catch (ValidationException $e) {
            $ajaxResponse->setMessage(
                view('partials.custom-message', ['bsClass' => 'danger', 'message' => $e->getMessage()])->render()
            );

            return response()->json($ajaxResponse);
        }

        // Create and send the command to create a new Jira ticket
        $command = new CreateJiraTicket(
            $vulnerabilityId,
            $this->request->get('username'),
            $this->request->get('password'),
            $jiraIssue
        );

        /** @var JiraIssue $jiraIssueResponse */
        $jiraIssueResponse = $this->sendCommandToBusHelper($command);

        // Handle the response and make sure the issue was created successfully, or notify the user what the error is
        // where possible

        // Command errors
        if ($jiraIssueResponse instanceof ErrorResponse) {
            $ajaxResponse->setMessage(view('partials.custom-message', [
                'bsClass' => 'danger',
                'message' => $jiraIssueResponse->getMessage()
            ])->render());

            return response()->json($ajaxResponse);
        }

        // Jira API errors
        if ($jiraIssueResponse->getRequestStatus() !== JiraIssue::REQUEST_STATUS_SUCCESS) {
            $ajaxResponse->setMessage(view('partials.custom-message', [
                'bsClass' => 'danger',
                'message' => $jiraIssueResponse->getFailureReason()
                    ?? 'Something went wrong and we were not able to create an new Jira Issue for you, '
                    . 'but please try again.',
            ])->render());

            return response()->json($ajaxResponse);
        }

        // Success
        $ajaxResponse->setMessage(view('partials.custom-message', [
            'bsClass' => 'success',
            'message' => 'Jira Issue <a href="' . $jiraIssueResponse->getIssueUrl() . '" target="_blank">'
                . '<span class="jira-issue-key">' . $jiraIssueResponse->getIssueKey() . '</span>'
                .'</a> created successfully.'
        ]));
        $ajaxResponse->setError(false);

        return response()->json($ajaxResponse);
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            JiraIssue::HOST => 'bail|filled|url',
            JiraIssue::PORT => 'bail|filled|int',
            'username'      => 'bail|filled',
            'password'      => 'bail|filled',
        ];
    }

    /**
     * @inheritdoc
     *
     * @return array
     */
    protected function getValidationMessages(): array
    {
        return [
            JiraIssue::HOST => 'You must provide your Jira host URL.',
            JiraIssue::PORT => 'You must provide your Jira host port.',
            'username'      => 'You must provide your Jira username.',
            'password'      => 'You must provide your Jira password.',
        ];
    }
}