<?php

namespace App\Handlers\Commands;

use App\Commands\CreateSystemInformation as CreateSystemInformationCommand;
use App\Entities\Asset;
use App\Entities\SystemInformation;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\AssetNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Exceptions\SuppressedAssetException;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\WorkspaceNotFoundException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use Doctrine\ORM\EntityManager;
use Exception;

class CreateSystemInformation extends CommandHandler
{
    /** @var AssetRepository */
    protected $assetRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateAsset constructor.
     *
     * @param AssetRepository $assetRepository
     * @param EntityManager $em
     */
    public function __construct(AssetRepository $assetRepository, EntityManager $em)
    {
        $this->assetRepository     = $assetRepository;
        $this->em                  = $em;
    }

    /**
     * Process the CreateAsset command.
     *
     * @param CreateSystemInformationCommand $command
     * @return SystemInformation
     * @throws InvalidInputException
     * @throws UserNotFoundException
     * @throws WorkspaceNotFoundException
     * @throws Exception
     */
    public function handle(CreateSystemInformationCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();
        
        // Check that all the required fields were set on the command
        $assetId = $command->getId();
        $details = $command->getDetails();
        
        if (!isset($assetId, $details)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the given Workspace exists
        /** @var Asset $asset */
        $asset = $this->getAssetRepository()->find($assetId);
        if (empty($asset) || $asset->getDeleted()) {
            throw new AssetNotFoundException("No Asset with the given ID was found in the database");
        }

        // If this is a suppressed Asset then don't persist any changes but return the Asset as is
        if ($asset->getSuppressed()) {
            throw new SuppressedAssetException("The given System Information is related to a suppressed Asset");
        }

        // Make sure the authenticated User has permission to add an Asset to this Workspace
        if ($requestingUser->cannot(ComponentPolicy::ACTION_UPDATE, $asset)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to add System Information to the given Asset"
            );
        }

        $systemInformation = new SystemInformation();
        $systemInformation->setFromArray($details);
        $systemInformation->setAsset($asset);

        // Persist the new Asset to the database
        $this->getEm()->persist($systemInformation);

        // Save immediately if we're not in multi-mode
        if (!$command->isMultiMode()) {
            $this->getEm()->flush($systemInformation);
        }

        return $systemInformation;
    }

    /**
     * @return AssetRepository
     */
    public function getAssetRepository()
    {
        return $this->assetRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm()
    {
        return $this->em;
    }
}