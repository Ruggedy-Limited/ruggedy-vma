<?php

namespace App\Handlers\Commands;

use App\Commands\Search as SearchCommand;
use App\Contracts\Searchable;
use App\Entities\Base\AbstractEntity;
use App\Entities\Vulnerability;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\UnsupportedSearchTypeException;
use App\Policies\ComponentPolicy;
use App\Repositories\AbstractSearchableRepository;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Collection;

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
     * @return \Illuminate\Support\Collection
     * @throws ActionNotPermittedException
     * @throws InvalidInputException
     * @throws UnsupportedSearchTypeException
     */
    public function handle(SearchCommand $command)
    {
        $requestingUser = $this->authenticate();

        $searchTerm = $command->getSearchTerm();
        // Check that we have everything we need to process the command
        if (!isset($searchTerm)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Iterate the different search types and reduce to a single Collection keyed by the entity's display name, each
        // with a Collection of matching entities sorted in descending order based on how many times a match is found.
        return AbstractSearchableRepository::getValidSearchTypes()
            ->reduce(function ($searchResults, $entityClass) use ($searchTerm, $requestingUser) {
                 /** @var Collection $searchResults */
                 // Make sure we're using a searchable repository
                 $repository = $this->em->getRepository($entityClass);
                 /** @var Searchable $repository */
                 if (!($repository instanceof Searchable)) {
                     return $searchResults;
                 }

                 // Intitial results filtered by permissions where relevant
                 $results = collect($repository->search($searchTerm))->filter(function ($entity) use ($requestingUser) {
                     if ($entity instanceof Vulnerability) {
                         return true;
                     }

                     return $requestingUser->can(ComponentPolicy::ACTION_VIEW, $entity);
                 });

                 // No allowed, matching results? Return the Collection that has been carried over from the previous
                 // iteration as is.
                 if ($results->isEmpty()) {
                     return $searchResults;
                 }

                 // Sort the results in descending order by score. Score is calculated by determining how many times the
                 // search query was found in the searchable fields
                 $results = $results->sortByDesc(function ($entity) use ($repository, $searchTerm) {
                     /** @var AbstractEntity $entity */
                     return $entity->getSearchScore($repository->getSearchableFields(), $searchTerm);
                 }, SORT_NUMERIC);

                 // Add the results at a key which is the entity's display name
                 /** @var AbstractEntity $entity */
                 $entity = $results->first();
                 return $searchResults->put($entity->getDisplayName(true), $results);
            }, new Collection());
    }
}