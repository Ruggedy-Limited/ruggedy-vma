<?php

namespace App\Handlers\Commands;

use App\Commands\CreateSoftwareInformation as CreateSoftwareInformationCommand;
use App\Entities\Asset;
use App\Entities\SoftwareInformation;
use App\Exceptions\ActionNotPermittedException;
use App\Exceptions\AssetNotFoundException;
use App\Exceptions\InvalidInputException;
use App\Policies\ComponentPolicy;
use App\Repositories\AssetRepository;
use App\Repositories\SoftwareInformationRepository;
use Doctrine\ORM\EntityManager;

class CreateSoftwareInformation extends CommandHandler
{
    /** @var SoftwareInformationRepository */
    protected $softwareInformationRepository;

    /** @var AssetRepository */
    protected $assetRepository;

    /** @var EntityManager */
    protected $em;

    /**
     * CreateSoftwareInformation constructor.
     * @param SoftwareInformationRepository $softwareInformationRepository
     * @param AssetRepository $assetRepository
     * @param EntityManager $em
     */
    public function __construct(
        SoftwareInformationRepository $softwareInformationRepository, AssetRepository $assetRepository,
        EntityManager $em
    )
    {
        $this->softwareInformationRepository = $softwareInformationRepository;
        $this->assetRepository               = $assetRepository;
        $this->em                            = $em;
    }

    /**
     * Process the CreateSoftwareInformation command.
     *
     * @param CreateSoftwareInformationCommand $command
     * @return SoftwareInformation|null|object
     * @throws ActionNotPermittedException
     * @throws AssetNotFoundException
     * @throws InvalidInputException
     */
    public function handle(CreateSoftwareInformationCommand $command)
    {
        // Get the authenticated User
        $requestingUser = $this->authenticate();

        // Check that all the required fields were set on the command
        $assetId = $command->getId();
        $details = $command->getEntity();

        if (!isset($assetId, $details)) {
            throw new InvalidInputException("One or more required members are not set on the command");
        }

        // Make sure the given Vulnerability exists
        /** @var Asset $asset */
        $asset = $this->assetRepository->find($assetId);
        if (empty($asset)) {
            throw new AssetNotFoundException("No Asset with the given ID was found in the database");
        }

        // Make sure the authenticated User has permission to add/edit information related to the given Asset
        if ($requestingUser->cannot(ComponentPolicy::ACTION_UPDATE, $asset)) {
            throw new ActionNotPermittedException(
                "The authenticated User does not have permission to"
                . " add Software Information to the given Asset"
            );
        }

        // Check if this Software Information already exists for this Asset and if so, exit early
        $softwareInformation = $this->softwareInformationRepository->findOneBy($details);
        if (!empty($softwareInformation) && $softwareInformation instanceof SoftwareInformation) {
            return $softwareInformation;
        }

        $softwareInformation = new SoftwareInformation();
        $softwareInformation->setFromArray($details);

        $asset->addSoftwareInformation($softwareInformation);

        // Persist the new Asset to the database
        $this->em->persist($asset);

        // Save immediately if we're not in multi-mode
        if (!$command->isMultiMode()) {
            $this->em->flush($asset);
        }

        return $softwareInformation;
    }

    /**
     * @return SoftwareInformationRepository
     */
    public function getSoftwareInformationRepository(): SoftwareInformationRepository
    {
        return $this->softwareInformationRepository;
    }

    /**
     * @return AssetRepository
     */
    public function getAssetRepository(): AssetRepository
    {
        return $this->assetRepository;
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->em;
    }
}