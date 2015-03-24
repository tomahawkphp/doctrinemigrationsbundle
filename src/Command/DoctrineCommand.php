<?php

namespace Tomahawk\Bundle\DoctrineMigrationsBundle\Command;

use Tomahawk\DI\ContainerInterface;
use Tomahawk\DI\ContainerAwareInterface;
use Tomahawk\Bundle\DoctrineBundle\Command\DoctrineCommand as BaseCommand;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Configuration\AbstractFileConfiguration;

/**
 * @author Tom Ellis
 *
 * Based on the original by Fabien Potencier
 *
 * Base class for Doctrine console commands to extend from.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class DoctrineCommand extends BaseCommand
{
    public static function configureMigrations(ContainerInterface $container, Configuration $configuration)
    {
        $config  = $container->get('config');

        if (!$configuration->getMigrationsDirectory()) {
            $dir = $config->get('doctrine.migrations_directories');

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $configuration->setMigrationsDirectory($dir);
        }
        else {
            $dir = $configuration->getMigrationsDirectory();

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $configuration->setMigrationsDirectory($dir);
        }
        if (!$configuration->getMigrationsNamespace()) {
            $configuration->setMigrationsNamespace($config->get('doctrine.migration_namespace'));
        }
        if (!$configuration->getName()) {
            $configuration->setName($config->get('doctrine.migration_name'));
        }

        // Migrations is not register from configuration loader
        if (!($configuration instanceof AbstractFileConfiguration)) {
            $configuration->registerMigrationsFromDirectory($configuration->getMigrationsDirectory());
        }

        self::injectContainerToMigrations($container, $configuration->getMigrations());
    }

    /**
     * Injects the container to migrations aware of it
     */
    private static function injectContainerToMigrations(ContainerInterface $container, array $versions)
    {
        foreach ($versions as $version) {
            $migration = $version->getMigration();
            if ($migration instanceof ContainerAwareInterface) {
                $migration->setContainer($container);
            }
        }
    }
}
