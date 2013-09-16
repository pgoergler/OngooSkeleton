<?php

namespace Tasks;

/**
 * Description of QuartzModelTask
 *
 * @author paul
 */
class QuartzModelTask extends \Ongoo\Core\Task
{

    protected function configure()
    {
        parent::configure();
        $this->setName('quartz:model')
                ->setDescription('Create model files')
                ->addArgument('model_name', \Symfony\Component\Console\Input\InputArgument::REQUIRED, 'Model class name')
        ;
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $modelName = $input->getArgument('model_name');
        $regex = '#^\\\\(?P<namespace>.*)\\\\(?P<model>.*?)$#';

        if( !preg_match($regex, $modelName, $m) )
        {
            $output->writeln("<info>Model class name should match \\\<Namespace>\\\<ModelClassname> got $modelName</info>");
            return;
        }

        $namespace = $m['namespace'];
        $model = $m['model'];

        $replace = array(
                '%namespace%' => $namespace,
                '%namespace|lower%' => strtolower($namespace),
                '%model%' => $model,
                '%model|lower%' => strtolower($model)
            );

        $path = __ROOT_DIR . '/' . str_replace("\\", DIRECTORY_SEPARATOR, $namespace);
        if( !is_dir($path) )
        {
            mkdir($path, 0755);
        }
        $modelFile = $path . '/' . $model . '.php';

        $content = strtr($this->getModelTemplate(), $replace);
        file_put_contents($modelFile, $content);
        $output->writeln("<info>\\${namespace}\\${model}</info> created in $modelFile.");


        $path .= '/Table';
        if( !is_dir($path) )
        {
            mkdir($path, 0755);
        }

        $tableFile = $path . '/' . $model . 'Table.php';
        $content = strtr($this->getTableTemplate(), $replace);
        file_put_contents($tableFile, $content);
        $output->writeln("<info>\\${namespace}\\Table\\${model}Table</info> created in $tableFile.");

        $path .= '/Base';
        if( !is_dir($path) )
        {
            mkdir($path, 0755);
        }
        $baseFile = $path . '/Base' . $model . 'Table.php';
        $content = strtr($this->getTableBaseTemplate(), $replace);
        file_put_contents($baseFile, $content);
        $output->writeln("<info>\\${namespace}\\Table\\Base\\Base${model}Table</info> created in $baseFile.");


    }

    public function getModelTemplate()
    {
        return <<<TEMPLATE
<?php

namespace %namespace%;

/**
 * Description of %model%
 *
 * @author paul
 */
class %model% extends \Quartz\Object\Entity
{
    //put your code here
}
TEMPLATE;
    }

    public function getTableTemplate()
    {
        return <<<TEMPLATE
<?php

namespace %namespace%\Table;

/**
 * Description of %model%Table
 *
 * @author paul
 */
class %model%Table extends Base\Base%model%Table
{
    //put your code here
}
TEMPLATE;
    }

    public function getTableBaseTemplate()
    {
        return <<<TEMPLATE
<?php

namespace %namespace%\Table\Base;

/**
 * Description of Base%model%Table
 *
 * @author paul
 */
class Base%model%Table extends \Quartz\Object\Table
{
    public function __construct(\Quartz\Connection\Connection \$conn = null)
    {
        if (is_null(\$conn))
        {
            \$conn = \Quartz\Quartz::getInstance()->getConnection('default');
            \$this->setConnection(\$conn);
            \$this->setDatabaseName(\Quartz\Quartz::getInstance()->getDatabaseName('default'));
        }

        parent::__construct(\$conn);

        \$this->setName('%namespace|lower%.%model|lower%s');

        \$this->addColumn('id', 'sequence', null, true, array('primary' => true));
    }
}
TEMPLATE;
    }
}

?>
