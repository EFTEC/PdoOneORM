<?php /** @noinspection PhpUnused */

/** @noinspection DuplicatedCode */

namespace eftec;

use Exception;
use RuntimeException;

/**
 * Class pdoonecli
 * It is the CLI interface for PdoOne.<br>
 * <b>How to execute it?</b><br>
 * In the command line, runs the next line:<br>
 * <pre>
 * php vendor/eftec/PdoOne/lib/pdoonecli
 * or
 * vendor/bin/pdoonecli (Linux/macOS) / vendor/bin/pdoonecli.bat (Windows)
 * </pre>
 *
 * @see           https://github.com/EFTEC/PdoOneORM
 * @package       eftec
 * @author        Jorge Castro Castillo
 * @copyright     Copyright Jorge Castro Castillo 2022-2023. Dual license, commercial and LGPL-3.0
 * @version       1.7
 */
class PdoOneORMCli extends PdoOneCli
{
    /**
     * @var array
     */
    protected $tablexclass = [];
    /**
     * @var array conversion by type
     */
    protected $conversion = [];
    protected $folder = [];
    /**
     * @var array
     */
    protected $alias = [];
    /**
     * @var array
     */
    protected $extracolumn = [];
    /**
     * @var array
     */
    protected $removecolumn = [];
    /**
     * @var array
     */
    protected $columnsTable = [];
    /**
     * @var array
     */
    protected $columnsAlias = [];
    protected $tablesmarked = [];
    private $classesSelected;

    public function __construct(bool $run = true)
    {
        parent::__construct(false); // PdoOne Menu and init parameters
        $this->cli->addMenuItem('mainmenu', 'repo', '[{{repo}}] Configure a repository', 'navigate:menuorm');
        $this->cli->addMenu('menuorm', 'ormheader', 'footer');
        $this->cli->addMenuItems('menuorm',
            [
                'folder' => ['[{{repofolder}}] Configure the repository folder and namespace', 'repofolder'],
                'scan' => ['[{{reposcan}}] Scan for changes to the database adding or removing tables and columns.', 'reposcan'],
                'select' => ['Select or de-select the tables to work', 'reposelect'],
                'detail' => ['Configure each table and columns separately', 'repodetail'],
                'type' => ['Configure the conversion of the columns per type', 'repotype'],
                'load' => ['load the configuration', 'repoload'],
                'save' => ['Save the current configuration', 'reposave'],
                'create' => ['Create the PHP repository classes', 'repocreate']]
        );
        // initialize custom fields:
        $this->conversion = $this->convertReset();
        // other initializing fields:
        $listPHPFiles = $this->getFiles('.', '.config.php');
        $this->cli->createOrReplaceParam('filerepo', [], 'longflag')
            ->setRequired(false)
            ->setCurrentAsDefault()
            ->setDescription('select a configuration file', 'Select the configuration file to use', [
                    'Example: <dim>"--filerepo myconfig"</dim>']
                , 'filerepo')
            ->setDefault('')
            ->setInput(false, 'string', $listPHPFiles)
            ->evalParam();
        $this->cli->addVariableCallBack('pdooneorm', function() {
            // every time we change a variable, then we call this code, so the variable "repo" is always updated
            if ($this->cli->getVariable('reposcan') !== '<red>pending</red>' &&
                $this->cli->getVariable('repofolder') !== '<red>pending</red>'
            ) {
                $this->cli->setVariable('repo', '<green>ok</green>', false); // false don't want to recall the callback
            } else {
                $this->cli->setVariable('repo', '<red>pending</red>', false); // false don't want to recall the callback
            }
        });
        $this->cli->setVariable('repofolder', '<red>pending</red>');
        $this->cli->setVariable('reposcan', '<red>pending</red>');
        if ($this->cli->getParameter('filerepo')->missing === false) {
            $this->doReadRepoConfig();
        }
        if ($run) {
            if ($this->cli->getSTDIN() === null) {
                $this->showLogo();
            }
            $this->cli->evalMenu('mainmenu', $this);
        }
    }

    public function menuORMHeader(): void
    {
        $this->cli->upLevel('orm');
        $this->cli->setColor(['byellow'])->showBread();
    }

    /** @noinspection PhpUnused */
    public function menuRepoFolder(): void
    {
        $this->cli->setParamUsingArray($this->folder);
        $this->cli->createOrReplaceParam('classdirectory')
            ->setCurrentAsDefault()
            ->setDescription('',
                'Select the relative directory where the repository classes will be created',
                ['Example: repo'])
            ->setInput()->add(true);
        $this->cli->createOrReplaceParam('classpostfix')
            ->setDefault('Repo')
            ->setCurrentAsDefault()
            ->setDescription('',
                'Select the postfix of the class',
                ['Example: Repo'])
            ->setInput()->add(true);
        $this->cli->createOrReplaceParam('classnamespace')
            ->setCurrentAsDefault()
            ->setDescription('',
                'Select the repository\'s namespace.',
                ['It must coincide with the definition of Composer\'s autoloader',
                    'Example: ns1\\ns2'])
            ->setInput()->add(true);
        $this->cli->createOrReplaceParam('namespace', 'ns', 'longflag')
            ->setRequired(false)
            ->setDescription('The namespace', 'The namespace used', [
                'Example: <dim>"customers"</dim>'])
            ->setDefault('')
            ->setInput(false)
            ->add(true);
        $this->cli->upLevel('folder');
        $this->cli->setColor(['byellow'])->showBread();
        $this->cli->evalParam('classdirectory', true);
        try {
            $configOk = @mkdir($this->cli->getValue('classdirectory'));
            if (!$configOk) {
                throw new RuntimeException('failed to create folder, maybe the folder already exists');
            }
            $this->cli->showCheck('OK', 'green', 'directory created');
        } catch (Exception $ex) {
            $this->cli->show('<yellow>');
            $this->cli->showCheck('note', 'yellow', 'Unable to create directory ' . $ex->getMessage());
            $this->cli->show('</yellow>');
            // $this->cli->showCheck('WARNING', 'yellow', 'unable to create directory ' . $ex->getMessage());
        }
        $this->cli->evalParam('classpostfix', true);
        // dummy.
        while (true) {
            $this->cli->showCheck('info', 'yellow', 'The target path is ' . getcwd() . '/' . $this->cli->getValue('classdirectory'));
            $this->cli->getParameter('classnamespace')->setDefault($this->cli->getValue('classnamespace'))->evalParam(true);
            $nameclass = '\\' . $this->cli->getValue('classnamespace') . '\\DummyClass';
            $filename = $this->cli->getValue('classdirectory') . '/DummyClass.php';
            $content = "<?php\nnamespace " . $this->cli->getValue('classnamespace') . ";\nclass DummyClass {}";
            try {
                $r = @file_put_contents($filename, $content);
                if ($r === false) {
                    throw new RuntimeException('Unable to write file ' . $filename);
                }
            } catch (Exception $ex) {
                $this->cli->showCheck('warning', 'yellow', 'Unable to create test class, ' . $ex->getMessage());
            }
            $ce = class_exists($nameclass);
            if ($ce) {
                $this->cli->showCheck('ok', 'green', 'Namespace tested correctly');
                @unlink($filename);
                break;
            }
            $this->cli->showCheck('warning', 'yellow', "Unable to test that dummy class $nameclass exists");
            $tmp = $this->cli->createParam('yn', [], 'none')
                ->setDescription('', 'Do you want to retry?')
                ->setInput(true, 'optionshort', ['yes', 'no'])
                ->evalParam(true, true);
            if ($tmp === 'no') {
                break;
            }
        } // test namespace
        $this->cli->downLevel();
        $this->folder = $this->cli->getValueAsArray(['classdirectory', 'classpostfix', 'classnamespace']);
        if (isset($this->folder['classdirectory'])) {
            $this->cli->setVariable('repofolder', '<green>ok</green>');
        }
    }

    public function menuRepoLoad(): void
    {
        $this->doReadRepoConfig(true);
    }

    public function menuRepoSave($input = false): void
    {
        if ($input) {
            $this->cli->getParameter('filerepo')->setInput()->evalParam(true);
        } else {
            $this->cli->getParameter('filerepo')->evalParam();
        }
        $error = $this->cli->saveData($this->cli->getParameter('filerepo')->value, [
            'conversion' => $this->conversion,
            'alias' => $this->alias,
            'columnsAlias' => $this->columnsAlias,
            'columnsTable' => $this->columnsTable,
            'extracolumn' => $this->extracolumn,
            'tablexclass' => $this->tablexclass,
            'removecolumn' => $this->removecolumn,
            'tablesmarked' => $this->tablesmarked,
            'folder' => $this->folder]);
        if (!$error) {
            $this->cli->showCheck('ok', 'green', 'Configuration saved');
        } else {
            $this->cli->showCheck('error', 'red', 'Unable to save configuration, ' . $error);
        }
    }

    public function menuRepoScan(): void
    {
        $pdo = $this->runCliConnection();
        if ($pdo === null) {
            $this->cli->showCheck('CRITICAL', 'red', 'No connection');
            return;
        }
        $this->cli->show('<yellow>Please wait, reading tables... </yellow>');
        try {
            $tables = $pdo->objectList('table', true);
        } catch (Exception $e) {
            $this->cli->showCheck('CRITICAL', 'red', 'Unable to read tables');
            $this->cli->setVariable('reposcan', '<green>error</green>');
            return;
        }
        $tablesmarked = $tables;
        if (count($this->tablexclass) === 0) {
            // no values, scanning...
            $this->databaseScan($tablesmarked, $pdo);
        }
        if (count($this->columnsTable) > 0) {
            $this->cli->setVariable('reposcan', '<green>ok</green>');
        }
    }

    public function menuRepoSelect(): void
    {
        $pdo = $this->runCliConnection();
        if ($pdo === null) {
            $this->cli->showCheck('CRITICAL', 'red', 'No connection');
            return;
        }
        $this->cli->show('<yellow>Please wait, reading tables... </yellow>');
        try {
            $allTables = $pdo->objectList('table', true);
        } catch (Exception $e) {
            $this->cli->showCheck('CRITICAL', 'red', 'Unable to read tables');
            die(1);
        }
        if ($this->tablesmarked === []) {
            $this->tablesmarked = $allTables;
        }
        $this->cli->upLevel('select');
        $this->cli->setColor(['byellow'])->showBread();
        $this->cli->setParam('tablesmarked', $this->tablesmarked, false, true);
        $this->tablesmarked = $this->cli->createParam('tablesmarked')
            ->setDefault($this->tablesmarked ?? [])
            ->setDescription('', 'Select or de-select a table to process')
            ->setInput(true, 'multiple2', $allTables)
            ->evalParam(true, true);
        $this->cli->downLevel();
    }

    public function menuRepoDetail(): void
    {
        $this->cli->upLevel('detail');
        while (true) {
            $this->cli->setColor(['byellow'])->showBread();
            $this->classesSelected = $this->cli->createOrReplaceParam('classselected', [], 'none')
                ->setDescription('', 'Select a table to configure')
                ->setAllowEmpty()
                ->setInput(true, 'option3', $this->tablexclass)
                ->evalParam(true);
            //$tmp = $this->cli->getValue('tables');
            //$classselected = $this->cli->evalParam('classselected', true);
            if ($this->classesSelected->value === '') {
                $this->cli->downLevel();
                break; // return to command
            }
            //$oldnameclass = $classselected->value;
            $ktable = $this->classesSelected->valueKey;
            $this->cli->upLevel($ktable, '(table)');
            while (true) { // tablecommand
                $this->cli->setColor(['byellow'])->showBread();
                $tablecommand = $this->cli->createOrReplaceParam('tablecommand')
                    ->setDescription('', 'Select the command for the table')
                    ->setAllowEmpty()
                    ->setInput(true, 'option', [
                        'rename' => 'rename the class from the table',
                        'conversion' => 'A conversion of type per column',
                        'alias' => 'rename a column',
                        'extracolumn' => 'configure extra columns that could be read',
                        'remove' => 'remove a column'
                    ])->evalParam(true);
                switch ($tablecommand->valueKey) {
                    case $this->cli->emptyValue:
                        //$this->cli->downLevel();
                        $this->cli->downLevel();
                        break 2; // while tablecommand
                    case 'rename':
                        $this->menuRepoDetailRename();
                        $this->cli->downLevel();
                        break;
                    case 'remove':
                        $this->menuRepoDetailRemove();
                        break;
                    case 'extracolumn':
                        $this->menuRepoDetailExtraColumn();
                        $this->cli->downLevel();
                        break;
                    case 'conversion':
                        $this->menuRepoDetailConversion();
                        $this->cli->downLevel();
                        break;
                    case 'alias':
                        $this->menuRepoDetailAlias();
                        $this->cli->downLevel();
                        break;
                }
            } // end while tablecommand
        } // end while table
    }

    public function menuRepoDetailRename(): void
    {
        $this->cli->upLevel('rename');
        $this->cli->setColor(['byellow'])->showBread();
        $newclassname = $this->cli->createOrReplaceParam('newclassname')
            ->setDescription('', 'Select the name of the class')
            ->setDefault($this->classesSelected->value)
            ->setInput(true, 'string', [])
            ->evalParam(true);
        //$k=array_search($classselected->value,$classes,true);
        //$classes[$k]=$newclassname->value;
        $this->tablexclass[$this->classesSelected->valueKey] = $newclassname->value;
    }

    public function menuRepoDetailRemove(): void
    {
        $this->databaseConfigureRemove($this->classesSelected->valueKey);
    }

    public function menuRepoDetailExtraColumn(): void
    {
        $ktable = $this->classesSelected->valueKey;
        $this->cli->upLevel('extracolumn');
        while (true) {
            $this->cli->setColor(['byellow'])->showBread();
            $this->cli->showValuesColumn($this->extracolumn[$ktable], 'option2');
            $ecc = $this->cli->createOrReplaceParam('extracolumncommand')
                ->setAllowEmpty()
                ->setInput(true, 'optionshort', ['add', 'remove'])
                ->setDescription('', 'Select an operation')
                ->evalParam(true);
            switch ($ecc->value) {
                case '':
                    break 2;
                case 'add':
                    $tmp = $this->cli->createOrReplaceParam('extracolumn_name')
                        //->setAllowEmpty()
                        ->setInput()
                        ->setDescription('', 'Select a name for the new column')
                        ->evalParam(true);
                    $tmp2 = $this->cli->createOrReplaceParam('extracolumn_sql')
                        //->setAllowEmpty()
                        ->setInput()
                        ->setDescription('', 'Select a sql for the new column')
                        ->evalParam(true);
                    $this->extracolumn[$ktable][$tmp->value] = $tmp2->value;
                    break;
                case 'remove':
                    $tmp = $this->cli->createOrReplaceParam('extracolumn_delete')
                        ->setAllowEmpty()
                        ->setInput(true, 'option2', $this->extracolumn[$ktable])
                        ->setDescription('', 'Select a columne to delete')
                        ->evalParam(true);
                    if ($tmp->valueKey !== $this->cli->emptyValue) {
                        unset($this->extracolumn[$ktable][$tmp->valueKey]);
                    }
                    break;
            }
        }
    }

    public function menuRepoDetailConversion(): void
    {
        $ktable = $this->classesSelected->valueKey;
        $this->cli->upLevel('conversion');
        while (true) {
            $this->cli->setColor(['byellow'])->showBread();
            $tablecolumn = $this->cli->createOrReplaceParam('tablescolumns')
                ->setDescription('', 'Select a column (or empty to end)')
                ->setAllowEmpty()
                ->setInput(true, 'option3', $this->columnsTable[$ktable])
                ->evalParam(true);
            if ($tablecolumn->value === '') {
                // exit
                break;
            }
            $this->cli->upLevel($tablecolumn->valueKey, ' (column)');
            $this->cli->setColor(['byellow'])->showBread();
            if ($tablecolumn->valueKey[0] === '_') {
                $this->cli->createOrReplaceParam('tablescolumnsvalue')
                    ->setDescription('', 'Select a relation')
                    ->setAllowEmpty()
                    ->setRequired(false)
                    ->setDefault($tablecolumn->value)
                    ->setPattern('<cyan>[{key}]</cyan> {value}')
                    ->setInput(true, 'option', [
                        'PARENT' => 'The field is related (similar to MANYTONE) but it is not loaded recursively',
                        'MANYTOMANY' => 'Many to many relation',
                        'ONETOMANY' => 'One to many relation',
                        'MANYTOONE' => 'Many to one relation',
                        'ONETOONE' => 'One to one'
                    ])->add(true);
            } else {
                $this->cli->createOrReplaceParam('tablescolumnsvalue')
                    ->setDescription('', 'Select a conversion')
                    ->setDefault($tablecolumn->value)
                    ->setAllowEmpty()
                    ->setInput(true, 'option', [
                        'string' => 'the value is converted to string',
                        'encrypt' => 'encrypt the value',
                        'decrypt' => 'decrypt the value',
                        'datetime3' => 'date/time is convert from human readable to SQL format',
                        'datetime4' => 'date/time is not converted',
                        'datetime2' => 'date/time is converted from ISO to SQL format',
                        'datetime' => 'date/time is converted from a DateTime PHP class to SQL format',
                        'timestamp' => 'date/time is converted from timestamp to SQL format',
                        'bool' => 'the value will be converted into a boolean (0=false,other=true)',
                        'int' => 'the value will be converted into a int',
                        'float' => 'the value will be converted into a float',
                        'decimal' => 'the value will be converted into a float',
                        'null' => 'pending.',
                        'nothing' => "it does nothing"])->add(true);
            }
            $tablecolumnsvalue = $this->cli->evalParam('tablescolumnsvalue', true);
            if ($tablecolumnsvalue->valueKey !== $this->cli->emptyValue) {
                $this->columnsTable[$ktable][$tablecolumn->valueKey] = $tablecolumnsvalue->valueKey;
            }
            $this->cli->downLevel();
        }
    }

    public function menuRepoDetailAlias(): void
    {
        $ktable = $this->classesSelected->valueKey;
        $this->cli->upLevel('alias');
        while (true) {
            $this->cli->setColor(['byellow'])->showBread();
            $tablecolumn = $this->cli->createOrReplaceParam('tablescolumns')
                ->setDescription('', 'Select a column (or empty to end)')
                ->setAllowEmpty()
                ->setInput(true, 'option3', $this->columnsAlias[$ktable])
                ->evalParam(true);
            //$tablecolumn = $this->cli->evalParam('tablescolumns', true);
            if ($tablecolumn->value === '') {
                // exit
                break;
            }
            $this->cli->upLevel($tablecolumn->valueKey, ' (column)');
            $this->cli->setColor(['byellow'])->showBread();
            $tablescolumnalias = $this->cli->createOrReplaceParam('tablescolumnsalias')
                ->setDescription('', 'Select the new alias of the column. Use: PROPERCASE to set propercase')
                ->setAllowEmpty()
                ->setInput(true, 'string', [])
                ->setDefault($tablecolumn->value)
                ->evalParam(true);
            $this->columnsAlias[$ktable][$tablecolumn->valueKey] = $tablescolumnalias->value;
            $this->cli->downLevel();
        }
    }

    public function menuRepoType(): void
    {
        $this->cli->upLevel('Configure x type');
        while (true) {
            $this->cli->setColor(['byellow'])->showBread();
            $convertionselected = $this->cli->createOrReplaceParam('convertionselected')
                ->setDescription('', 'Select a type of data to convert')
                ->setAllowEmpty()
                ->setInput(true, 'option3', $this->conversion)
                ->evalParam(true);
            //$this->cli->getParameter('convertionselected')
            //    ->setInput(true, 'option3', $this->conversion);
            //= $this->cli->evalParam('convertionselected', true);
            if ($convertionselected->valueKey === $this->cli->emptyValue) {
                break;
            }
            $this->cli->upLevel($convertionselected->valueKey, ' (type)');
            $this->cli->setColor(['byellow'])->showBread();
            $convertionnewvalue = $this->cli->createParam('convertionnewvalue')
                ->setDescription('', 'Select the conversion')
                ->setAllowEmpty()
                ->setInput(true, 'option', [
                    'encrypt' => 'encrypt and decrypt the value',
                    'decrypt' => 'encrypt and decrypt the value',
                    'datetime3' => 'convert an human readable date to SQL',
                    'datetime4' => 'no conversion, it keeps the format of SQL',
                    'datetime2' => 'convert between ISO standard and SQL',
                    'datetime' => 'convert between PHP Datetime object and SQL',
                    'timestamp' => 'convert between a timestamp number and sql',
                    'bool' => 'the value will be converted into a boolean (0,"" or null=false,other=true)',
                    'int' => 'the value will be cast into a int',
                    'float' => 'the value will be cast into a float',
                    'decimal' => 'the value will be cast into a float',
                    'null' => 'the value will be null',
                    'nothing' => "it does nothing"])->evalParam(true);
            // $convertionnewvalue = $this->cli->getParameter('convertionnewvalue')
            //->setDefault($convertionselected->value ?? '')
            //->evalParam(true);
            $this->conversion[$convertionselected->valueKey] = $convertionnewvalue->valueKey;
            $this->cli->downLevel();
        }
        $this->cli->downLevel();
    }

    public function menuRepoCreate(): void
    {
        $pdo = $this->runCliConnection();
        if ($pdo === null) {
            $this->cli->showCheck('CRITICAL', 'red', 'No connection');
            return;
        }
        if ($this->cli->getValue('classdirectory') && $this->cli->getValue('classnamespace')) {
            $this->cli->createOrReplaceParam('overridegenerate', ['og'], 'longflag')
                ->setRelated(['generate'])
                ->setDefault('no')
                ->setDescription('Override the generate values', 'Do you want to override previous repository classes (abstract classes are always override)?'
                    , ['Values available <cyan><option/></cyan>'], 'bool')
                ->setInput(true, 'optionshort', ['yes', 'no'])->evalParam(true);
            //$this->cli->evalParam('overridegenerate', true);
            $pdo->generateCodeClassConversions($this->conversion);
            $tmpTableXClass = [];
            foreach ($this->tablexclass as $k => $v) {
                $tmpTableXClass[$k] = $v . $this->cli->getValue('classpostfix');
            }
            /** @noinspection PhpPossiblePolymorphicInvocationInspection */
            $pdo->generateAllClasses($tmpTableXClass, ucfirst($this->cli->getValue('database')),
                $this->cli->getValue('classnamespace'),
                $this->cli->getValue('classdirectory'),
                $this->cli->getValue('overridegenerate') === 'yes',
                $this->columnsTable,
                $this->extracolumn,
                $this->removecolumn,
                $this->columnsAlias
            );
            //$this->RunCliGenerationSaveConfig();
            $this->cli->showLine('<green>Done</green>');
        } else {
            $this->cli->showCheck('ERROR', 'red', [
                'you must set the directory and namespace',
                'Use the option <bold><cyan>[folder]</cyan></bold> to set the directory and namespace'], 'stderr');
        }
    }

    /** @noinspection DisconnectedForeachInstructionInspection */
    protected function databaseScan($tablesmarked, $pdo): void
    {
        $tablexclass = [];
        $columnsTable = [];
        $conversion = [];
        $extracolumn = [];
        //$this->removecolumn = [];
        $def2 = [];
        $pk = [];
        $this->cli->show('<yellow>Please wait, reading structure of tables... </yellow>');
        $this->cli->showWaitCursor();
        foreach ($tablesmarked as $table) {
            $this->cli->showWaitCursor(false);
            $class = PdoOne::tableCase($table);
            //$classes[] = $class;
            $tablexclass[$table] = $class;
            $extracolumn[$table] = [];
            $columns = $pdo->columnTable($table);
            foreach ($columns as $v) {
                $conversion[$v['coltype']] = null;
                $columnsTable[$table][$v['colname']] = null;
            }
            $pk[$table] = $pdo->getPK($table);
            if ($pk[$table] === false) {
                $def2[$table] = $pdo->getRelations($table, null);
            } else {
                $def2[$table] = $pdo->getRelations($table, $pk[$table][0]);
            }
            foreach ($def2[$table] as $k => $v) {
                if (isset($v['key']) && $v['key'] !== 'FOREIGN KEY') {
                    $columnsTable[$table][$k] = $v['key'];
                }
            }
        }
        // The next lines are used for testing:
        //unset($tablexclass['actor']);
        //$tablexclass['newtable']='newtablerepo';
        //unset($columnsTable['city']['city']);
        //$columnsTable['city']['xxxx'] = 'new';
        // end testing
        $this->cli->showLine();
        ksort($conversion);
        // merge new with old
        // *** TABLEXCLASS
        if (count($this->tablexclass) !== 0) {
            foreach ($this->tablexclass as $table => $v) {
                if (!isset($tablexclass[$table])) {
                    $this->cli->showCheck('<bold>deleted</bold>', 'red', "table <bold>$table</bold> deleted");
                    unset($this->tablexclass[$table], $this->columnsTable[$table], $this->extracolumn[$table]);
                }
            }
            foreach ($tablexclass as $table => $v) {
                if (!isset($this->tablexclass[$table])) {
                    $this->cli->showCheck(' added ', 'green', "table <bold>$table</bold> added");
                    $class = PdoOne::tableCase($table);
                    $this->tablexclass[$table] = $class;
                    $this->extracolumn[$table] = [];
                }
            }
        } else {
            $this->tablexclass = $tablexclass;
        }
        // *** COLUMNSTABLE
        $this->columnsTable = $this->updateMultiArray($columnsTable, $this->columnsTable, 'Columns Table');
        if (count($this->columnsTable) === 0) {
            $this->columnsTable = $columnsTable;
        }
        $alias = $columnsTable;
        foreach ($columnsTable as $table => $columns) {
            foreach ($columns as $column => $v) {
                if ($column[0] !== '_') {
                    $alias[$table][$column] = $column;
                } else {
                    unset($alias[$table][$column]);
                }
            }
        }
        // add onetomany and onetoone alias
        foreach ($columnsTable as $ktable => $columns) {
            $pk = '??';
            $pk = $pdo->service->getPK($ktable, $pk);
            $pkFirst = (is_array($pk) && count($pk) > 0) ? $pk[0] : null;
            /** @noinspection PhpUnusedLocalVariableInspection */
            [$relation, $linked] = $pdo->generateGetRelations($ktable, $this->columnsTable, $pkFirst, $alias);
            foreach ($relation as $colDB => $defs) {
                if (!isset($alias[$ktable][$colDB])) {
                    $alias[$ktable][$colDB] = $defs['alias'];
                }
            }
        }
        //$oldAlias = $this->columnsAlias;
        //$this->columnsAlias = [];
        // **** COLUMNSALIAS
        $this->columnsAlias = $this->updateMultiArray($alias, $this->columnsAlias, 'Columns Alias');
        if (count($this->extracolumn) === 0) {
            $this->extracolumn = $extracolumn;
        }
    }

    protected function databaseConfigureRemove($ktable): void
    {
        $this->cli->upLevel('remove');
        $this->removecolumn[$ktable] = $this->removecolumn[$ktable] ?? [];
        while (true) {
            $this->cli->setColor(['byellow'])->showBread();
            if (isset($this->removecolumn[$ktable])) {
                $this->cli->showValuesColumn($this->removecolumn[$ktable], 'option3');
            }
            $ecc = $this->cli->createOrReplaceParam('extracolumncommand')
                ->setAllowEmpty()
                ->setInput(true, 'optionshort', ['add', 'remove'])
                ->setDescription('', 'Do you want to add or remove a column from the remove-list')
                ->evalParam(true);
            switch ($ecc->value) {
                case '':
                    break 2;
                case 'add':
                    $tmp = $this->cli->createParam('extracolumn_name')
                        //->setAllowEmpty()
                        ->setInput(true, 'option3', array_keys($this->columnsTable[$ktable]))
                        ->setDescription('', 'Select a name of the column to remove')
                        ->evalParam(true);
                    $this->removecolumn[$ktable][] = $tmp->value;
                    break;
                case 'remove':
                    $tmp = $this->cli->createParam('extracolumn_delete')
                        ->setAllowEmpty()
                        ->setInput(true, 'option2', $this->removecolumn[$ktable])
                        ->setDescription('', 'Select a columne to delete')
                        ->evalParam(true);
                    if ($tmp->valueKey !== $this->cli->emptyValue) {
                        unset($this->removecolumn[$ktable][$tmp->valueKey - 1]);
                    }
                    // renumerate
                    $this->removecolumn[$ktable] = array_values($this->removecolumn[$ktable]);
                    break;
            }
        }
        $this->cli->downLevel();
    }

    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    public function createPdoInstance()
    {
        try {
            $pdo = new PdoOneORM(
                $this->cli->getValue('databaseType'),
                $this->cli->getValue('server'),
                $this->cli->getValue('user'),
                $this->cli->getValue('pwd'),
                $this->cli->getValue('database'));
            $pdo->logLevel = 1;
            $pdo->connect();
        } catch (Exception $ex) {
            /** @noinspection PhpUndefinedVariableInspection */
            $this->cli->showCheck('ERROR', 'red', ['Unable to connect to database', $pdo->lastError(), $pdo->errorText]);
            return null;
        }
        $pdo->logLevel = 2;
        return $pdo;
    }

    protected function doReadRepoConfig($input = false): void
    {
        if ($input) {
            $this->cli->getParameter('filerepo')->setInput()->evalParam(true);
        } else {
            $this->cli->getParameter('filerepo')->evalParam();
        }
        $readComplete = $this->cli->readData($this->cli->getValue('filerepo'));
        if ($readComplete[0] === true) {
            $r = $readComplete[1];
            $this->conversion = $r['conversion'] ?? [];
            $this->alias = $r['alias'] ?? [];
            $this->columnsAlias = $r['columnsAlias'] ?? [];
            $this->columnsTable = $r['columnsTable'] ?? [];
            $this->extracolumn = $r['extracolumn'] ?? [];
            $this->tablexclass = $r['tablexclass'] ?? [];
            $this->removecolumn = $r['removecolumn'] ?? [];
            $this->tablesmarked = $r['tablesmarked'] ?? [];
            $this->folder = $r['folder'] ?? [];
            if (count($this->columnsTable) > 0) {
                $this->cli->setVariable('reposcan', '<green>ok</green>');
            }
            if (isset($this->folder['classdirectory'])) {
                $this->cli->setVariable('repofolder', '<green>ok</green>');
            }
            $this->cli->setParamUsingArray($this->folder);
        } else {
            $this->cli->showCheck('error', 'red', 'Unable to read configuration, ' .
                $this->cli->getValue('filerepo'));
        }
    }

    protected function showLogo(): void
    {
        $vorm = PdoOneORM::VERSION;
        $v = PdoOne::VERSION;
        $vc = self::VERSION;
        $this->cli->show("
 ____     _        ___              ___  ____  __  __ 
|  _ \ __| | ___  / _ \ _ __   ___ / _ \|  _ \|  \/  |
| |_) / _` |/ _ \| | | | '_ \ / _ \ | | | |_) | |\/| |
|  __/ (_| | (_) | |_| | | | |  __/ |_| |  _ <| |  | |
|_|   \__,_|\___/ \___/|_| |_|\___|\___/|_| \_\_|  |_|                                                      
PdoOneORM:$vorm  PdoOne:$v  Cli $vc  

<yellow>Syntax:php pdooneorm <command> <flags></yellow>

");
        $this->cli->showParamSyntax2();
    }

    public function convertReset(): array
    {
        return ["bigint" => null, "blob" => null, "char" => null, "date" => null, "datetime" => null,
            "decimal" => null, "double" => null, "enum" => null, "float" => null, "geometry" => null,
            "int" => null, "json" => null, "longblob" => null, "mediumint" => null, "mediumtext" => null,
            "set" => null, "smallint" => null, "text" => null, "time" => null, "timestamp" => null,
            "tinyint" => null, "varbinary" => null, "varchar" => null, "year" => null];
    }
}
