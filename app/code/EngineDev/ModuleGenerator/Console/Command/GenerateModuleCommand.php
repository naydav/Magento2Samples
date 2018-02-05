<?php
declare(strict_types=1);

namespace EngineDev\ModuleGenerator\Console\Command;

use EngineDev\ModuleGenerator\Model\ModuleGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @api
 * @author naydav <valeriy.nayda@gmail.com>
 */
class GenerateModuleCommand extends Command
{
    /**
     * Argument name 'module'
     */
    const ARGUMENT_NAME_MODULE = 'module';

    /**
     * @var ModuleGenerator
     */
    private $moduleGenerator;

    /**
     * @param ModuleGenerator $moduleGenerator
     * @param string|null $name
     */
    public function __construct(ModuleGenerator $moduleGenerator, string $name = null)
    {
        parent::__construct($name);
        $this->moduleGenerator = $moduleGenerator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('engine-dev:module-generator:generate')
            ->setDescription('Generate module structure')
            ->addArgument(self::ARGUMENT_NAME_MODULE, InputArgument::OPTIONAL, 'Module for generation');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start: ' . $this->getName());
        $module = $input->getArgument(self::ARGUMENT_NAME_MODULE);
        try {
            $this->moduleGenerator->generate($module);
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }
        $output->writeln('Finish');
    }
}
