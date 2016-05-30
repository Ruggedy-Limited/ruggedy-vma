<?php
namespace App\Providers;

use Doctrine\ORM\EntityManagerInterface;
use DoctrineExtensions\Query\Mysql\TimestampDiff;
use LaravelDoctrine\ORM\DoctrineServiceProvider as LaravelDoctrineServiceProvider;
use LaravelDoctrine\ORM\Configuration\Cache\CacheManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Gedmo\DoctrineExtensions;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Gedmo\Timestampable\TimestampableListener;
use Doctrine\DBAL\Event\Listeners\MysqlSessionInit;


class DoctrineServiceProvider extends LaravelDoctrineServiceProvider
{
    /**
     * Adds additional functionality - like Gedmo helpers
     */
    public function register()
    {
        parent::register();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app[EntityManagerInterface::class];
        $platform = $entityManager->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        $this->registerCustomFunctions($entityManager);
        $cache = $this->app[CacheManager::class]->driver();

        // standard annotation reader
        $annotationReader = new AnnotationReader();
        $cachedAnnotationReader = new CachedReader(
            $annotationReader, // use reader
            $cache // and a cache driver
        );

        // create a driver chain for metadata reading
        $driverChain = new MappingDriverChain();
        // load superclass metadata mapping only, into driver chain
        // also registers Gedmo annotations.NOTE: you can personalize it
        DoctrineExtensions::registerAbstractMappingIntoDriverChainORM(
            $driverChain, // our metadata driver chain, to hook into
            $cachedAnnotationReader // our cached annotation reader
        );

        // now we want to register our application entities,
        // for that we need another metadata driver used for Entity namespace
        $annotationDriver = new AnnotationDriver(
            $cachedAnnotationReader, // our cached annotation reader
            array(__DIR__.'/app/Entities') // paths to look in
        );

        // NOTE: driver for application Entity can be different, Yaml, Xml or whatever
        // register annotation driver for our application Entity namespace
        $driverChain->addDriver($annotationDriver, 'Entity');

        $evm = $entityManager->getEventManager();

        $timestampableListener = new TimestampableListener();
        $timestampableListener->setAnnotationReader($cachedAnnotationReader);

        $evm->addEventSubscriber($timestampableListener);
        $evm->addEventSubscriber(new MysqlSessionInit());

        $this->registerEntityRepositories();
    }

    /**
     * Add custom entity repositories to DIC so they can be injected
     */
    protected function registerEntityRepositories()
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $this->app[EntityManagerInterface::class];

        /** @var \Doctrine\ORM\Mapping\ClassMetadata[] $metadata */
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        if (empty($metadata)) {
            return;
        }

        foreach ($metadata as $classMetadata) {
            // only add custom entity repositories to DIC
            $repositoryClass = $classMetadata->customRepositoryClassName;
            $entityClass     = $classMetadata->rootEntityName;
            if (empty($repositoryClass)) {
                continue;
            }

            if (strpos($entityClass, "Base\\") !== false) {
                $entityClass = str_replace("Base\\", "", $entityClass);
            }

            $this->app->bind($repositoryClass, function () use ($entityManager, $entityClass) {
                return $entityManager->getRepository($entityClass);
            });
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function registerCustomFunctions(EntityManagerInterface $entityManager)
    {
        $entityManager->getConfiguration()
            ->addCustomDatetimeFunction('TIMESTAMPDIFF', TimestampDiff::class);
    }
}
