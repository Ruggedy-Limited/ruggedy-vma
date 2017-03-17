<?php

namespace App\Handlers\Commands;

use App\Commands\CreateOpenPort as CreateOpenPortCommand;
use App\Entities\Asset;
use App\Entities\OpenPort;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\AssetNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\SuppressedAssetException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use App\Repositories\OpenPortRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class CreateOpenPort extends CommandHandler
{
    /** @var AssetRepository */
    protected $assetRepository;

    /** @var OpenPortRepository */
    protected $openPortRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateAsset constructor.
     *
     * @param AssetRepository $assetRepository
     * @param OpenPortRepository $openPortRepository
     * @param EntityManager $em
     */
    public function __construct(AssetRepository $assetRepository, OpenPortRepository $openPortRepository, EntityManager $em)
    {
        $this->assetRepository     = $assetRepository;
        $this->openPortRepository  = $openPortRepository;
        $this->em                  = $em;
    }

    /**
     * Process the CreateOpenPort command.
     *
     * @param CreateOpenPortCommand $command
     * @return OpenPort
     * @throws InvalidInputException
     * @throws UserNotFoundException
     * @throws WorkspaceNotFoundException
     * @throws Exception
     */
    public function handle(CreateOpenPortCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();
        
        // Check that all the required fields were set on the command
        $assetId = $command->getId();
        /** @var OpenPort $entity */
        $entity = $command->getEntity();
        
        if (!isset($assetId, $entity)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the given Asset exists
        /** @var Asset $asset */
        $asset = $this->assetRepository->find($assetId);
        if (empty($asset) || $asset->getDeleted()) {
            throw new AssetNotFoundException("No Asset with the given ID was found in the database");
        }

        // If this is a suppressed Asset then don't persist any changes but throw a SuppressedAssetException
        if ($asset->getSuppressed()) {
            throw new SuppressedAssetException("The given System Information is related to a suppressed Asset");
        }

        // Make sure the authenticated User has permission to add/update information related to the given Asset
        if ($requestingUser->cannot(ComponentPolicy::ACTION_UPDATE, $asset)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to add System Information to the given Asset"
            );
        }

        // See if a record of this open port already exists for this Asset and if so, exit early
        $entity->setAsset($asset)
            ->setAssetId($assetId);

        $openPort = $this->openPortRepository->findOneBy($entity->getUniqueKeyColumns()->all());
        if (!empty($openPort) && $openPort instanceof OpenPort) {
            $entity = $openPort->setFromArray($entity->toArray(true));
        }

        // Persist the new OpenPort to the database
        $this->em->persist($entity);

        // Save immediately if we're not in multi-mode
        if (!$command->isMultiMode()) {
            $this->em->flush($entity);
        }

        return $entity;
    }

    /**
     * @return AssetRepository
     */
    public function getAssetRepository()
    {
        return $this->assetRepository;
    }

    /**
     * @return OpenPortRepository
     */
    public function getOpenPortRepository(): OpenPortRepository
    {
        return $this->openPortRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }
}