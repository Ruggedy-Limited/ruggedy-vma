<?php

namespace App\Handlers\Commands;

use App\Commands\CreateJiraTicket as CreateJiraTicketCommand;
use App\Entities\File;
use App\Entities\JiraIssue;
use App\Entities\Vulnerability;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\JiraApiException;
use App\Exceptions\VulnerabilityNotFoundException;
use App\Repositories\FileRepository;
use App\Repositories\VulnerabilityRepository;
use App\Services\JiraService;
use Doctrine\ORM\EntityManager;

class CreateJiraTicket extends CommandHandler
{
    /** @var VulnerabilityRepository */
    protected $vulnerabilityRepository;

    /** @var EntityManager */
    protected $em;

    /** @var JiraService */
    protected $service;

    /**
     * CreateJiraTicket constructor.
     *
     * @param VulnerabilityRepository $vulnerabilityRepository
     * @param EntityManager $em
     * @param JiraService $service
     */
    public function __construct(
        VulnerabilityRepository $vulnerabilityRepository, EntityManager $em, JiraService $service
    )
    {
        $this->vulnerabilityRepository = $vulnerabilityRepository;
        $this->service                 = $service;
        $this->em                      = $em;
    }

    /**
     * Process the CreateJiraTicketCommand
     *
     * @param CreateJiraTicketCommand $command
     * @return JiraIssue
     * @throws FileNotFoundException
     * @throws InvalidInputException
     * @throws JiraApiException
     * @throws VulnerabilityNotFoundException
     */
    public function handle(CreateJiraTicketCommand $command): JiraIssue
    {
        /** @var JiraIssue $jiraIssue */
        $vulnerabilityId = $command->getVulnerabilityId();
        $username        = $command->getUsername();
        $password        = $command->getPassword();
        $jiraIssue       = $command->getJiraIssue();
        $hostname        = $jiraIssue->getHost();
        $port            = $jiraIssue->getPort();
        // Check that we have everything we need to continue
        if (!isset($vulnerabilityId, $jiraIssue)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Check the username, password, host and port values
        if (empty($this->service->initialise($username, $password, $hostname, $port))) {
            throw new InvalidInputException("A username, password, hostname and port are required");
        }

        // Make sure we have a project key
        if (empty($jiraIssue->getProjectKey())) {
            throw new InvalidInputException("A JIRA Project key is required to create a JIRA issue");
        }

        // Make sure a Vulnerability with the given ID exists
        /** @var Vulnerability $vulnerability */
        $vulnerability = $this->vulnerabilityRepository->find($vulnerabilityId);
        if (empty($vulnerability)) {
            throw new VulnerabilityNotFoundException("There is no existing Vulnerability with the given ID");
        }

        // Get the formatted issue summary and description
        $summary     = $this->service->getIssueSummaryText($vulnerability, $jiraIssue);
        $description = $this->service->getIssueDescriptionText($vulnerability, $jiraIssue);

        // Calculate the number of retries on this request
        $retries = 0;
        if (!empty($jiraIssue->getRetries())) {
            $retries = $jiraIssue->getRetries() + 1;
        }

        // Update the JirsIssue entity
        $jiraIssue
            ->setFile($vulnerability->getFile())
            ->setVulnerability($vulnerability)
            ->setSummary($summary)
            ->setDescription($description)
            ->setRequestStatus(JiraIssue::REQUEST_STATUS_IN_PROGRESS)
            ->setRetries($retries);

        // If not explicit issue status has been set, set a default of 'open' for new issues
        if (empty($jiraIssue->getIssueStatus())) {
            $jiraIssue->setIssueStatus(JiraIssue::ISSUE_STATUS_OPEN);
        }

        // Save changes before API call so that we have the state of the request just
        // before any failure resulting in an exception that would prevent this
        $this->em->persist($jiraIssue);
        $this->em->flush($jiraIssue);
        $this->em->refresh($jiraIssue);

        // Call the API to create the issue
        $this->service->createJiraIssue($jiraIssue);

        // Handle API call failures
        if ($this->service->getJira()->isErrorResponse()) {
            // Set the failure reason and status and exit early
            $jiraIssue
                ->setFailureReason($this->service->getJira()->getErrorCollection())
                ->setRequestStatus(JiraIssue::REQUEST_STATUS_FAILED);

            return $jiraIssue;
        }

        // Update issue details
        $jiraIssue
            ->setRequestStatus(JiraIssue::REQUEST_STATUS_SUCCESS)
            ->setIssueId($this->service->getJira()->getResponseField('id'))
            ->setIssueKey($this->service->getJira()->getResponseField('key'));

        // Persist success status issue ID and issue key
        $this->em->persist($jiraIssue);
        $this->em->flush($jiraIssue);

        // Return the create issue request in it's current state
        return $jiraIssue;
    }
}