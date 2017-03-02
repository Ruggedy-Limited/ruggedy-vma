<?php

namespace App\Handlers\Commands;

use App\Commands\SyncJiraStatuses as SyncJiraStatusesCommand;
use App\Repositories\JiraIssueRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Illuminate\Filesystem\Filesystem;

class SyncJiraStatuses extends CommandHandler
{
    /** @var JiraIssueRepository */
    protected $jiraIssueRepository;

    /** @var EntityManager */
    protected $em;

    /** @var Filesystem */
    protected $fileSystem;

    /**
     * SyncJiraStatuses constructor.
     *
     * @param JiraIssueRepository $jiraIssueRepository
     * @param EntityManager $em
     * @param Filesystem $filesystem
     */
    public function __construct(JiraIssueRepository $jiraIssueRepository, EntityManager $em, Filesystem $filesystem)
    {
        $this->jiraIssueRepository = $jiraIssueRepository;
        $this->em                  = $em;
        $this->fileSystem          = $filesystem;
    }

    /**
     * Process the SyncJiraStatuses command.
     *
     * @param SyncJiraStatusesCommand $command
     */
    public function handle(SyncJiraStatusesCommand $command)
    {
        $lastUpdate = new Carbon(
            $this->fileSystem->lastModified(storage_path('framework') . DIRECTORY_SEPARATOR . 'sync.txt')
        );

        $this->fileSystem->put(storage_path('framework') . DIRECTORY_SEPARATOR . 'sync.txt', time(), true);

        $issues = $this->jiraIssueRepository->findStatusesToSync($lastUpdate);
        if (empty($issues) || $issues->isEmpty()) {
            return;
        }

        $issues->chunk(10)->each(function ($chunk) {
            //TODO: Fetch the updated issues statuses for all the issues in this chunk
        });
    }
}