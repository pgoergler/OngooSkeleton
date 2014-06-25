<?php

namespace Tasks;

/**
 * Description of QuartzTableTask
 *
 * @author paul
 */
class QuartzTableTask extends \Ongoo\Core\Task
{
    protected function configure()
    {
        parent::configure();
        $this->setName('quartz:table')
                ->setDescription('Create/drop table using Quartz')
                ->addArgument('classname_or_dir', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Model classname or directory of classes')
                ->addOption('create', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Create the table')
                ->addOption('drop', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Drop the table')
                ->addOption('cascade', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Drop the table in cascade')
                ->addOption('silence', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Discard any output')
        ;
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $silence = $input->getOption('silence');
        if( $silence )
        {
            $this->output = null;
        }
        try
        {
            $classname = $input->getArgument('classname_or_dir');

            if (is_dir($classname))
            {
                $this->processDirectory($classname, $input->getOption('create'), $input->getOption('drop'), $input->getOption('cascade'));
            } else
            {
                $this->processClassname($classname, $input->getOption('create'), $input->getOption('drop'), $input->getOption('cascade'));
            }
        } catch (\Exception $e)
        {
            $output->writeln("<error>" . $e->getMessage() . "</error>");
            $this->app['logger']->error($e);
        }
    }

    public function processDirectory($dir, $create = true, $drop = true, $cascade = true)
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $iterator = $finder->files()->name('*.php'); //->notName('*Table.php');
        // $iterator->depth('== 0');
        $iterator->sortByName()->in($dir);

        $created = array();
        foreach ($iterator as $file)
        {
            $classes = \get_declared_php_classes($file);
            foreach ($classes as $class)
            {
                $clazz = new \ReflectionClass($class);
                if ($clazz->IsInstantiable() && $clazz->isSubclassOf('\Quartz\Object\Table'))
                {
                    //$table = $this->app['orm']->getTable($class);
                    $table = new $class();
                    if( !in_array($table->getName(), $created) )
                    {
                        $this->createDrop($table, $create, $drop, $cascade);
                        $created[] = $table->getName();
                    }
                }
            }
        }
    }

    public function processClassname($classname, $create = true, $drop = true, $cascade = true)
    {
        $table = $this->app['orm']->getTable($classname);
        $this->createDrop($table, $create, $drop, $cascade);
    }

    protected function createDrop(\Quartz\Object\Table $table, $create, $drop, $cascade = false)
    {
        if ($drop)
        {
            $table->drop($cascade);
            if ($this->output)
            {
                $this->output->writeln("<info>" . $table->getName() . " dropped</info>");
            }
        }

        if ($create)
        {
            $table->create();
            if ($this->output)
            {
                $this->output->writeln("<info>" . $table->getName() . " created</info>");
            }
        }
    }

}