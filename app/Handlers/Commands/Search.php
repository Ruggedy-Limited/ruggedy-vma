<?php

namespace App\Handlers\Commands;

use App\Commands\Search as SearchCommand;
use App\Contracts\Searchable;
use App\Entities\Vulnerability;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UnsupportedSearchTypeException;
use App\Policies\ComponentPolicy;
use App\Repositories\AbstractSearchableRepository;
use Doctrine\ORM\EntityManager;

class Search extends CommandHandler
{
    /** @var EntityManager */
    protected $em;

    /**
     * Search constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Process the Search command
     *
     * @param SearchCommand $command
     * @return \Doctrine\Common\Collections\Collection
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws UnsupportedSearchTypeException
     */
    public function handle(SearchCommand $command)
    {
        $requestingUser = $this->authenticate();

        $searchTerm = $command->getSearchTerm();
        $searchType = $command->getSearchType();
        // Check that we have everything we need to process the command
        if (!isset($searchTerm, $searchType)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure we have a valid search type
        if (!AbstractSearchableRepository::isValidSearchType($searchType)) {
            throw new UnsupportedSearchTypeException("The given search type is not supported");
        }

        $entityClassForSearch = AbstractSearchableRepository::getValidSearchTypes()->get($searchType);
        $repository           = $this->em->getRepository($entityClassForSearch);
        if (!($repository instanceof Searchable)) {
            throw new UnsupportedSearchTypeException(
                "The given entity does not have a repository implementing the Searchable Contract"
            );
        }

        return $repository->search($searchTerm)->filter(function ($entity) use ($requestingUser) {
            if ($entity instanceof Vulnerability) {
                return true;
            }
            return $requestingUser->can(ComponentPolicy::ACTION_VIEW, $entity);
        });
    }
}