<?php

namespace Tomahawk\Bundle\DoctrineMigrationsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Tomahawk\Bundle\DoctrineBundle\Command\Proxy\CommandHelper;
use Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand;

/**
 * Command for generating new blank migration classes
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
class DoctrineGenerateCommand extends GenerateCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('doctrine:migrations:generate')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command.')
        ;
    }
    public function execute(InputInterface $input, OutputInterface $output)
    {
        CommandHelper::setApplicationEntityManager($this->getApplication(), $input->getOption('em'));
        $configuration = $this->getMigrationConfiguration($input, $output);
        DoctrineCommand::configureMigrations($this->getApplication()->getKernel()->getContainer(), $configuration);
        parent::execute($input, $output);
    }
}