<?php

namespace Task;

use Quartz\External\sfInflector;

/**
 * Description of QuartzFixturesTask
 *
 * @author paul
 */
class QuartzFixturesTask extends \Ongoo\Core\Task
{

    protected $app = null;
    protected $refs = array();

    protected function configure()
    {
        parent::configure();
        $this->setName('quartz:fixtures')
                ->setDescription('Load database entities using Quartz')
                ->addArgument('dir_or_file', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Dir or File to load')
                ->addOption('drop', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Drop current data in the database')
                ->addOption('update', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Use finder key to update entity or insert')
                ->addOption('dry-run', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Dry run')
                ->addOption('silence', null, \Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Discard any output')
        ;
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $silence = $input->getOption('silence');
        try
        {
            $filename = $input->getArgument('dir_or_file');
            if (is_dir($filename))
            {
                $this->importDirectory($filename, $input->getOption('drop'), $input->getOption('update'), $input->getOption('dry-run'), $silence ? null : $output,  $input->getOption('env') == 'test');
            } else if (file_exists($filename) && preg_match("#\.php\$#", $filename))
            {
                $this->import($filename, $input->getOption('drop'), $input->getOption('update'), $input->getOption('dry-run'), $silence ? null : $output);
            } else
            {
                if( ! $silence )
                {
                    $output->writeln("<error>The filename $filename must be a php file or a directory.</error>");
                }
            }
        } catch (\Exception $e)
        {
            $this->app['logger']->error($e);
        }
    }

    public function importDirectory($directory, $drop, $update, $dry_run = true, \Symfony\Component\Console\Output\OutputInterface $output = null, $isTest = false)
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $iterator = $finder->files();

        if (!$isTest)
        {
            $iterator->name('*.php');
        }

        $iterator->sortByName()
                ->in($directory);

        foreach ($iterator as $file)
        {
            $this->import($file, $drop, $update, $dry_run, $output);
        }
    }

    public function import($filename, $drop, $update, $dry_run = true, \Symfony\Component\Console\Output\OutputInterface $output = null)
    {
        $app = $this->app;
        $data = include($filename);
        try
        {
            $finder = isset($data['finder']) ? $data['finder'] : array();
            unset($data['finder']);

            foreach ($data as $class => $elements)
            {
                $table = $this->app['orm']->getTable($class);
                $table->beginTransaction();

                if ($drop)
                {
                    if( $output )
                    {
                        $output->write("Cleaning & ");
                    }
                    $table->delete();
                }

                if( $output )
                {
                    $output->write("<info>" . $class . "</info>:");
                }
                $nbUpdate = 0;
                $nbInsert = 0;
                $nbElements = 0;
                foreach ($elements as $refName => $conf)
                {
                    $nbElements++;
                    $obj = new $class();
                    if (!$drop && $update)
                    {
                        $obj = $this->find($finder, $class, $conf) ? : $obj;
                    }

                    foreach ($conf as $k => $v)
                    {
                        $setter = $obj->getSetter($k);
                        if( is_object($v) )
                        {
                            $obj->$setter($v);
                        }
                        else if (!is_array($v) && preg_match('/##(REF|ref)(@(?P<reference>.*?))?##(?P<options>.*?)$/', $v, $m))
                        {
                            $obj->$setter($this->getReferedObject(isset($m['reference']) ? $m['reference'] : rand(1,99999), $m['options']));
                        } else
                        {
                            $obj->$setter($v);
                        }
                    }
                    if ($obj->isNew())
                    {
                        $nbInsert++;
                    } else if ($obj->hasChanged())
                    {
                        $nbUpdate++;
                    }
                    $obj->save();

                    $this->refs[$refName] = $obj;
                }
                if( $output )
                {
                    $output->write(" <info>$nbElements</info>row(s) <info>$nbInsert</info>insert, <info>$nbUpdate</info>update");
                }

                if ($dry_run)
                {
                    $table->rollback();
                    if( $output )
                    {
                        $output->writeln(" <comment>not commited</comment>");
                    }
                } else
                {
                    $table->commit();
                    if( $output )
                    {
                        $output->writeln(" <info>commited</info>");
                    }
                }
            }
        } catch (\Exception $e)
        {
            $this->app['logger']->error($e);
        }
    }

    protected function find($finder, $class, $array)
    {
        if (!isset($finder[$class]))
        {
            return false;
        }

        $criteria = array();
        foreach ($finder[$class] as $field)
        {
            if (!isset($array[$field]))
            {
                $array[$field] = null;
            //} else if (preg_match('/##(REF|ref)(@(?P<reference>.*?))?##(?P<options>.*?##find:one,field:.*?)$/i', $array[$field], $m))
            //{
            //    $array[$field] = $this->getReferedObject(isset($m['reference']) ? $m['reference'] : rand(1,99999), $m['options']);
            } else if (preg_match('/##(REF|ref)(@(?P<reference>.*?))?##(?P<options>.*?)$/i', $array[$field], $m))
            {
                $array[$field] = $this->getReferedObject(isset($m['reference']) ? $m['reference'] : rand(1,99999), $m['options']);
            }
            $criteria[$field] = $array[$field];
        }

        return $this->app['orm']->getTable($class)->findOne($criteria);
    }

    protected function getReferedObject($reference, $options)
    {
        $boundary = '==boundary==' . md5(time() . json_encode($options)) . '==boundary==';
        $options = str_replace('\\#', "$boundary%diez%$boundary", $options);
        $options = str_replace('\\@', "$boundary%at%$boundary", $options);

        $regexModel = '(?P<model>\\\\.*?\\\\Models\\\\.*?)';
        $regexCriteria = '(?P<criteria>.*?)';
        $regexFind = 'find:(?P<find>.*?)(,field:(?P<field>.*?))?(,order:(?P<order>.*?),(limit:[0-9]+)?)?';


        if (preg_match("@^$regexModel##$regexCriteria##$regexFind$@i", $options, $extract))
        {
            $this->app['logger']->debug($extract);
            $model = $extract['model'];
            $criteria = json_decode(str_replace("$boundary%diez%$boundary", '#', $extract['criteria']), true);
            $find = isset($extract['find']) ? $extract['find'] : 'one';
            $orderby = isset($extract['order']) ? json_decode($extract['order'], true) : array();
            $limit = isset($extract['limit']) ? $extract['limit'] : 1;

            $refObj = $this->app['orm']->getTable($model)->find($criteria, $orderby, $limit);
            if ($find == 'one')
            {
                if ($refObj)
                {
                    $field = $extract['field'];
                    $refObj = array_shift($refObj);
                    $getter = $refObj->getGetter($field);
                    $this->refs[$reference] = $refObj;

                    return $refObj->$getter();
                }
                return null;
            } else
            {
                return $refObj;
            }
        } else
        {
            $refObj = isset($this->refs[$reference]) ? $this->refs[$reference] : null;

            if ($options && $refObj)
            {
                $getter = $refObj->getGetter($options);
                return $refObj->$getter();
            }
            return $refObj;
        }
    }

}

?>