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
use App\Repositories\JiraIssueRepository;
use App\Repositories\VulnerabilityRepository;
use App\Services\JiraService;
use Doctrine\ORM\EntityManager;

class CreateJiraTicket extends CommandHandler
{
    /** @var FileRepository */
    protected $fileRepository;

    /** @var VulnerabilityRepository */
    protected $vulnerabilityRepository;

    /** @var JiraIssueRepository */
    protected $jiraIssueRepository;

    /** @var EntityManager */
    protected $em;

    /** @var JiraService */
    protected $service;

    /**
     * CreateJiraTicket constructor.
     *
     * @param FileRepository $fileRepository
     * @param VulnerabilityRepository $vulnerabilityRepository
     * @param JiraIssueRepository $jiraIssueRepository
     * @param EntityManager $em
     * @param JiraService $service
     */
    public function __construct(
        FileRepository $fileRepository, VulnerabilityRepository $vulnerabilityRepository,
        JiraIssueRepository $jiraIssueRepository, EntityManager $em, JiraService $service
    )
    {
        $this->fileRepository          = $fileRepository;
        $this->vulnerabilityRepository = $vulnerabilityRepository;
        $this->jiraIssueRepository     = $jiraIssueRepository;
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
        $fileId          = $command->getFileId();
        $vulnerabilityId = $command->getVulnerabilityId();
        $username        = $command->getUsername();
        $password        = $command->getPassword();
        $jiraIssue       = $command->getJiraIssue();
        $hostname        = $jiraIssue->getHost();
        $port            = $jiraIssue->getPort();
        // Check that we have everything we need to continue
        if (!isset($fileId, $vulnerabilityId, $username, $password, $jiraIssue)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure we have a project key, host and port
        if (empty($jiraIssue->getProjectKey())) {
            throw new InvalidInputException("A JIRA Project key is required to create a JIRA issue");
        }

        // Make sure a File with the given ID exists
        /** @var File $file */
        $file = $this->fileRepository->find($fileId);
        if (empty($file)) {
            throw new FileNotFoundException("There is no existing file with the given ID");
        }

        // Make sure a Vulnerability with the given ID exists
        /** @var Vulnerability $vulnerability */
        $vulnerability = $this->vulnerabilityRepository->find($vulnerabilityId);
        if (empty($vulnerability)) {
            throw new VulnerabilityNotFoundException("There is no existing Vulnerability with the given ID");
        }

        $summary     = $this->service->getIssueSummaryText($vulnerability, $file, $jiraIssue);
        $description = $this->service->getIssueDescriptionText($vulnerability, $file, $jiraIssue);

        $jiraIssue
            ->setFile($file)
            ->setVulnerability($vulnerability)
            ->setSummary($summary)
            ->setDescription($description)
            ->setRequestStatus(JiraIssue::REQUEST_STATUS_IN_PROGRESS)
            ->setRetries(
                empty($jiraIssue->getRetries()) ? 0 : $jiraIssue->getRetries() + 1
            );

        // If not explicit issue status has been set, set a default of 'open' for new issues
        if (empty($jiraIssue->getIssueStatus())) {
            $jiraIssue->setIssueStatus(JiraIssue::ISSUE_STATUS_OPEN);
        }

        // Save changes before API call
        $this->em->persist($jiraIssue);
        $this->em->flush($jiraIssue);
        $this->em->refresh($jiraIssue);

        if (empty($this->service->initialise($username, $password, $hostname, $port))) {
            throw new InvalidInputException("A username, password, hostname and port are required");
        }

        $jira = $this->service->createJiraIssue($jiraIssue);
        if ($jira->isErrorResponse()) {
            // Set the failure reason and status and exit early
            $jiraIssue
                ->setFailureReason($jira->getErrorCollection())
                ->setRequestStatus(JiraIssue::REQUEST_STATUS_FAILED);

            return $jiraIssue;
        }

        // Update issue details
        $jiraIssue
            ->setRequestStatus(JiraIssue::REQUEST_STATUS_SUCCESS)
            ->setIssueId($jira->getResponseField('id'))
            ->setIssueKey($jira->getResponseField('key'));

        // Persist success status issue ID and issue key
        $this->em->persist($jiraIssue);
        $this->em->flush($jiraIssue);

        return $jiraIssue;
    }
}