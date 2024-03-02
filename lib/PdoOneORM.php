<?php /** @noinspection UnknownInspectionInspection */
/** @noinspection DuplicatedCode */

namespace eftec;

use Exception;
use JsonException;
use RuntimeException;

/**
 * Class PdoOneORM
 * It is an extension of the library PdoOneORM to create and manage ORM <br>
 *
 * @see           https://github.com/EFTEC/PdoOneORM
 * @package       eftec
 * @author        Jorge Castro Castillo
 * @copyright     Copyright Jorge Castro Castillo 2022-2023. Dual license, commercial and LGPL-3.0
 * @version       2.1
 */
class PdoOneORM extends PdoOne
{
    protected string $phpstart = "<?php\n";
    public const VERSION = '2.1';

    /**
     * @param string $tableName
     * @param string $namespace
     * @param null   $customRelation
     * @param null   $classRelations
     * @param array  $specialConversion
     * @param null   $defNoInsert
     * @param null   $defNoUpdate
     * @param null   $baseClass
     * @param array  $extraColumn
     * @param array  $columnRemove
     * @param array  $resultColumns
     * @return string|string[]
     * @throws Exception
     */
    public function generateAbstractModelClass(
        string $tableName,
        string $namespace = '',
               $customRelation = null,
               $classRelations = null,
        array  $specialConversion = [],
               $defNoInsert = null,
               $defNoUpdate = null,
               $baseClass = null,
        array  $extraColumn = [],
        array  $columnRemove = [],
        array  &$resultColumns = []
    )
    {
        $resultColumns = [];
        $this->beginTry();
        $filename = __DIR__ . '/template/template_abstractmodel.php';
        $r = $this->phpstart . $this->openTemplate($filename);
        //$lastns = explode('\\', $namespace);
        //$baseClass = ($baseClass === null) ? end($lastns) : $baseClass;
        $fa = func_get_args();
        foreach ($fa as $f => $k) {
            if (is_array($k)) {
                $fa[$f] = str_replace([' ', "\r\n", "\n"], ['', '', ''], var_export($k, true));
            } else {
                $fa[$f] = "'$k'";
            }
        }
        if ($classRelations === null || !isset($classRelations[$tableName])) {
            $className = self::camelize($tableName);
        } else {
            $className = $classRelations[$tableName];
        }
        $r = str_replace([
            '{version}',
            '{classname}',
            '{exception}',
            '{namespace}'
        ], [
            self::VERSION . ' Date generated ' . date('r'), //{version}
            $className, // {classname}
            ($namespace) ? 'use Exception;' : '',
            ($namespace) ?: ''
        ], $r);
        $pk = '??';
        $pk = $this->service->getPK($tableName, $pk);
        $pkFirst = (is_array($pk) && count($pk) > 0) ? $pk[0] : null;
        $relation = $this->getRelations($tableName, $pkFirst);
        if ($customRelation) {
            foreach ($relation as $k => $rel) {
                if (isset($customRelation[$k])) {
                    // parent.
                    if ($customRelation[$k] === 'PARENT') {
                        $relation[$k]['key'] = 'PARENT';
                    } elseif ($customRelation[$k] === 'MANYTOMANY') {
                        // the table must have 2 primary keys.
                        $pks = null;
                        $pks = $this->service->getPK($rel['reftable'], $pks);
                        /** @noinspection PhpParamsInspection */
                        /** @noinspection PhpArrayIsAlwaysEmptyInspection */
                        /** @noinspection PhpConditionAlreadyCheckedInspection */
                        if ($pks !== false || count($pks) === 2) {
                            $relation[$k]['key'] = 'MANYTOMANY';
                            $refcol2 = (self::$prefixBase . $pks[0] === $relation[$k]['refcol']) ? $pks[1]
                                : $pks[0];
                            try {
                                $defsFK = $this->service->getDefTableFK($relation[$k]['reftable'], false);
                            } catch (Exception $e) {
                                $this->endTry();
                                return 'Error: Unable read table dependencies ' . $e->getMessage();
                            }
                            try {
                                $keys2 = $this->service->getDefTableKeys($defsFK[$refcol2]['reftable'], true,
                                    'PRIMARY KEY');
                            } catch (Exception $e) {
                                $this->endTry();
                                return 'Error: Unable read table dependencies' . $e->getMessage();
                            }
                            $relation[$k]['refcol2'] = self::$prefixBase . $refcol2;
                            if (count($keys2) > 0) {
                                $keys2 = array_keys($keys2);
                                $relation[$k]['col2'] = $keys2[0];
                            } else {
                                $relation[$k]['col2'] = null;
                            }
                            $relation[$k]['table2'] = $defsFK[$refcol2]['reftable'];
                        }
                    }
                    // manytomany
                }
            }
        }
        $gdf = $this->getDefTable($tableName, $specialConversion);
        foreach ($columnRemove as $v) {
            unset($gdf[$v]);
        }
        $fields = [];
        $fieldsb = [];
        foreach ($gdf as $varn => $field) {
            switch ($field['phptype']) { //binary, date, datetime, decimal,int, string,time, timestamp
                case 'binary':
                case 'date':
                case 'datetime':
                case 'decimal':
                case 'float':
                case 'int':
                case 'string':
                case 'time':
                case 'timestamp':
                    $resultColumns[] = [$varn, $field['phptype'], null];
                    $fields[] = "\t/** @var " . $field['phptype'] . " \$$varn  */\n\tpublic \$$varn;";
                    $fieldsb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ?  \$array['$varn'] : null;";
                    break;
            }
        }
        foreach ($extraColumn as $varn => $value) {
            $resultColumns[] = [$varn, 'mixed', null];
            $fields[] = "\t/** @var mixed \$$varn extra column: $value */\n\tpublic \$$varn;";
            $fieldsb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ?  \$array['$varn'] : null;";
        }
        $fieldsArr = implode("\n", $fields);
        $fieldsbArr = implode("\n", $fieldsb);
        $field2s = [];
        $field2sb = [];
        foreach ($relation as $varn => $field) {
            //$varnclean = ltrim($varn, PdoOne::$prefixBase);
            switch ($field['key']) {
                case 'FOREIGN KEY':
                    break;
                case 'MANYTOONE':
                    $class = $classRelations[$field['reftable']];
                    $resultColumns[] = [$varn, $field['key'], $class];
                    $field2s[] = "\t/** @var $class \$$varn manytoone */\n\tpublic \$$varn;";
                    $field2sb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ? \n\t\t\t\$obj->$varn=$class::fromArray(\$array['$varn']) \n\t\t\t: null; // manytoone";
                    $col = ltrim($varn, self::$prefixBase);
                    $rcol = $field['refcol'];
                    $field2sb[] = "\t\t(\$obj->$varn !== null) \n\t\t\tand \$obj->$varn->$rcol=&\$obj->$col; // linked manytoone";
                    break;
                case 'MANYTOMANY':
                    $class = $classRelations[$field['reftable']];
                    $resultColumns[] = [$varn, $field['key'], $class];
                    $field2s[] = "\t/** @var {$class}[] \$$varn manytomany */\n\tpublic \$$varn;";
                    $field2sb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ?  \n\t\t\t\$obj->$varn=$class::fromArrayMultiple(\$array['$varn']) \n\t\t\t: null; // manytomany";
                    break;
                case 'ONETOMANY':
                    $class = $classRelations[$field['reftable']];
                    $resultColumns[] = [$varn, $field['key'], $class];
                    $field2s[] = "\t/** @var {$class}[] \$$varn onetomany */\n\tpublic \$$varn;";
                    $field2sb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ?  \n\t\t\t\$obj->$varn=$class::fromArrayMultiple(\$array['$varn']) \n\t\t\t: null; // onetomany";
                    break;
                case 'ONETOONE':
                    $class = $classRelations[$field['reftable']];
                    $resultColumns[] = [$varn, $field['key'], $class];
                    $field2s[] = "\t/** @var $class \$$varn onetoone */\n\tpublic \$$varn;";
                    $field2sb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ?  \n\t\t\t\$obj->$varn=$class::fromArray(\$array['$varn']) \n\t\t\t: null; // onetoone";
                    $col = $field['col'] ?? $pkFirst;
                    $rcol = $field['refcol'];
                    $field2sb[] = "\t\t(\$obj->$varn !== null) \n\t\t\tand \$obj->$varn->$rcol=&\$obj->$col; // linked onetoone";
                    break;
            }
        }
        $fields2Arr = implode("\n", $field2s);
        $fields2Arrb = implode("\n", $field2sb);
        $r = str_replace(['{fields}', '{fieldsrel}', '{fieldsfa}', '{fieldsrelfa}'],
            [$fieldsArr, $fields2Arr, $fieldsbArr, $fields2Arrb], $r);
        if (@count($this->codeClassConversion) > 0) {
            // we forced the conversion but only if it is not specified explicit
            foreach ($gdf as $k => $colDef) {
                $type = $colDef['type'];
                if (isset($this->codeClassConversion[$type]) && $colDef['conversion'] === null) {
                    $gdf[$k]['conversion'] = $this->codeClassConversion[$type];
                }
            }
        }
        // discard columns
        $identities = $this->getDefIdentities($tableName);
        if ($defNoInsert !== null) {
            $noInsert = array_merge($identities, $defNoInsert);
        } else {
            $noInsert = $identities;
        }
        if ($defNoUpdate !== null) {
            $noUpdate = array_merge($identities, $defNoUpdate);
        } else {
            $noUpdate = $identities;
        }
        try {
            $r = str_replace([
                '{pk}',
                '{def}',
                '{defname}',
                '{defkey}',
                '{defnoinsert}',
                '{defnoupdate}',
                '{deffk}',
                '{deffktype}',
                '{array}',
                '{array_null}'
            ], [
                self::varExport($pk),
                //str_replace(["\n\t\t        ", "\n\t\t    ],"], ['', '],'], PdoOne::varExport($gdf, "\t\t")), // {def}
                self::varExport($gdf, "\t\t"),
                self::varExport(array_keys($gdf), "\t\t"), // {defname}
                self::varExport($this->getDefTableKeys($tableName), "\t\t"), // {defkey}
                self::varExport($noInsert, "\t\t"), // {defnoinsert}
                self::varExport($noUpdate, "\t\t"), // {defnoupdate}
                self::varExport($this->getDefTableFK($tableName), "\t\t\t"), //{deffk}
                self::varExport($relation, "\t\t"), //{deffktype}
                str_replace("\n", "\n\t\t",
                    rtrim($this->generateCodeArray($tableName, null, false, false, true, $classRelations, $relation),
                        "\n")),
                str_replace("\n", "\n\t\t",
                    rtrim($this->generateCodeArray($tableName, null, true, false, true, $classRelations, $relation),
                        "\n"))
            ], $r);
        } catch (Exception $e) {
            $this->endTry();
            return "Unable read definition of tables " . $e->getMessage();
        }
        $this->endTry();
        return $r;
    }

    /**
     * @param string $tableName
     * @param string $namespace
     * @param null   $customRelation
     * @param null   $classRelations
     * @param array  $specialConversion
     * @param null   $defNoInsert
     * @param null   $defNoUpdate
     * @param null   $baseClass
     *
     * @return string|string[]
     * @throws Exception
     */
    public function generateModelClass(
        string $tableName,
        string $namespace = '',
               $customRelation = null,
               $classRelations = null,
        array  $specialConversion = [],
               $defNoInsert = null,
               $defNoUpdate = null,
               $baseClass = null
    )
    {
        $this->beginTry();
        $filename = __DIR__ . '/template/template_model.php';
        $r = $this->phpstart . $this->openTemplate($filename);
        //$lastns = explode('\\', $namespace);
        //$baseClass = ($baseClass === null) ? end($lastns) : $baseClass;
        $fa = func_get_args();
        foreach ($fa as $f => $k) {
            if (is_array($k)) {
                $fa[$f] = str_replace([' ', "\r\n", "\n"], ['', '', ''], var_export($k, true));
            } else {
                $fa[$f] = "'$k'";
            }
        }
        if ($classRelations === null || !isset($classRelations[$tableName])) {
            $className = self::camelize($tableName);
        } else {
            $className = $classRelations[$tableName];
        }
        $r = str_replace([
            '{version}',
            '{classname}',
            '{exception}',
            '{namespace}'
        ], [
            self::VERSION . ' Date generated ' . date('r'), //{version}
            $className, // {classname}
            ($namespace) ? 'use Exception;' : '',
            ($namespace) ?: ''
        ], $r);
        $pk = '??';
        $pk = $this->service->getPK($tableName, $pk);
        $pkFirst = (is_array($pk) && count($pk) > 0) ? $pk[0] : null;
        try {
            $relation = $this->getDefTableFK($tableName, false, true);
        } catch (Exception $e) {
            return 'Error: Unable read fk of table ' . $e->getMessage();
        }
        try {
            $deps = $this->tableDependency(true);
        } catch (Exception $e) {
            $this->endTry();
            return 'Error: Unable read table dependencies ' . $e->getMessage();
        } //  ["city"]=> {["city_id"]=> "address"}
        $after = $deps[1][$tableName] ?? null;
        if ($after === null) {
            $after = $deps[1][strtolower($tableName)] ?? null;
        }
        $before = $deps[2][$tableName] ?? null;
        if ($before === null) {
            $before = $deps[2][strtolower($tableName)] ?? null;
        }
        if (is_array($after) && is_array($before)) {
            foreach ($before as $key => $rows) { // $value is [relcol,table]
                foreach ($rows as $value) {
                    $relation[self::$prefixBase . $value[1]] = [
                        'key' => 'ONETOMANY',
                        'col' => $key,
                        'reftable' => $value[1],
                        'refcol' => $value[0]
                    ];
                }
            }
        }
        // converts relations to ONETOONE
        foreach ($relation as $k => $rel) {
            if ($rel['key'] === 'ONETOMANY') {
                $pkref = null;
                $pkref = $this->service->getPK($rel['reftable'], $pkref);
                if (self::$prefixBase . $pkref[0] === $rel['refcol'] && count($pkref) === 1) {
                    $relation[$k]['key'] = 'ONETOONE';
                    $relation[$k]['col'] = 'xxx3';
                    $relation[$k]['refcol'] = ltrim($relation[$k]['refcol'], self::$prefixBase);
                }
            }
            if ($rel['key'] === 'MANYTOONE') {
                $pkref = null;
                $pkref = $this->service->getPK($rel['reftable'], $pkref);
                if ($pkref[0] === $rel['refcol'] && count($pkref) === 1
                    && (strcasecmp($k, self::$prefixBase . $pkFirst) === 0)
                ) {
                    // if they are linked by the pks and the pks are only 1.
                    $relation[$k]['key'] = 'ONETOONE';
                    $relation[$k]['col'] = 'xxx4';
                    $relation[$k]['refcol'] = ltrim($relation[$k]['refcol'], self::$prefixBase);
                }
            }
        }
        if ($customRelation) {
            foreach ($relation as $k => $rel) {
                if (isset($customRelation[$k])) {
                    // parent.
                    if ($customRelation[$k] === 'PARENT') {
                        $relation[$k]['key'] = 'PARENT';
                    } elseif ($customRelation[$k] === 'MANYTOMANY') {
                        // the table must have 2 primary keys.
                        $pks = $this->service->getPK($rel['reftable']);
                        /** @noinspection PhpParamsInspection */
                        /** @noinspection PhpArrayIsAlwaysEmptyInspection */
                        /** @noinspection PhpConditionAlreadyCheckedInspection */
                        if ($pks !== false || count($pks) === 2) {
                            $relation[$k]['key'] = 'MANYTOMANY';
                            $refcol2 = (self::$prefixBase . $pks[0] === $relation[$k]['refcol']) ? $pks[1]
                                : $pks[0];
                            try {
                                $defsFK = $this->service->getDefTableFK($relation[$k]['reftable'], false);
                            } catch (Exception $e) {
                                $this->endTry();
                                return 'Error: Unable read table dependencies ' . $e->getMessage();
                            }
                            try {
                                $keys2 = $this->service->getDefTableKeys($defsFK[$refcol2]['reftable'], true,
                                    'PRIMARY KEY');
                            } catch (Exception $e) {
                                $this->endTry();
                                return 'Error: Unable read table dependencies' . $e->getMessage();
                            }
                            $relation[$k]['refcol2'] = self::$prefixBase . $refcol2;
                            if (count($keys2) > 0) {
                                $keys2 = array_keys($keys2);
                                $relation[$k]['col2'] = $keys2[0];
                            } else {
                                $relation[$k]['col2'] = null;
                            }
                            $relation[$k]['table2'] = $defsFK[$refcol2]['reftable'];
                        }
                    }
                    // manytomany
                }
            }
        }
        $gdf = $this->getDefTable($tableName, $specialConversion);
        $fields = [];
        $fieldsb = [];
        foreach ($gdf as $varn => $field) {
            switch ($field['phptype']) { //binary, date, datetime, decimal,int, string,time, timestamp
                case 'binary':
                case 'date':
                case 'datetime':
                case 'decimal':
                case 'float':
                case 'int':
                case 'string':
                case 'time':
                case 'timestamp':
                    $fields[] = "\t/** @var " . $field['phptype'] . " \$$varn  */\n\tpublic \$$varn;";
                    $fieldsb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ?  \$array['$varn'] : null;";
                    break;
            }
        }
        $fieldsArr = implode("\n", $fields);
        $fieldsbArr = implode("\n", $fieldsb);
        $field2s = [];
        $field2sb = [];
        foreach ($relation as $varn => $field) {
            //$varnclean = ltrim($varn, PdoOne::$prefixBase);
            switch ($field['key']) {
                case 'FOREIGN KEY':
                    break;
                case 'MANYTOONE':
                    $class = $classRelations[$field['reftable']];
                    $field2s[] = "\t/** @var $class \$$varn manytoone */
    public \$$varn;";
                    $field2sb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ? 
            \$obj->$varn=$class::fromArray(\$array['$varn']) 
            : null; // manytoone";
                    break;
                case 'MANYTOMANY':
                    $class = $classRelations[$field['reftable']];
                    $field2s[] = "\t/** @var {$class}[] \$$varn manytomany */
    public \$$varn;";
                    $field2sb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ?  
            \$obj->$varn=$class::fromArrayMultiple(\$array['$varn']) 
            : null; // manytomany";
                    break;
                case 'ONETOMANY':
                    $class = $classRelations[$field['reftable']];
                    $field2s[] = "\t/** @var {$class}[] \$$varn onetomany */
    public \$$varn;";
                    $field2sb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ?  
            \$obj->$varn=$class::fromArrayMultiple(\$array['$varn']) 
            : null; // onetomany";
                    break;
                case 'ONETOONE':
                    $class = $classRelations[$field['reftable']];
                    $field2s[] = "\t/** @var $class \$$varn onetoone */
    public \$$varn;";
                    $field2sb[] = "\t\t\$obj->$varn=isset(\$array['$varn']) ?  
            \$obj->$varn=$class::fromArray(\$array['$varn']) 
            : null; // onetoone";
                    break;
            }
        }
        $fields2Arr = implode("\n", $field2s);
        $fields2Arrb = implode("\n", $field2sb);
        $r = str_replace(['{fields}', '{fieldsrel}', '{fieldsfa}', '{fieldsrelfa}'],
            [$fieldsArr, $fields2Arr, $fieldsbArr, $fields2Arrb], $r);
        if (@count($this->codeClassConversion) > 0) {
            // we forced the conversion but only if it is not specified explicit
            foreach ($gdf as $k => $colDef) {
                $type = $colDef['type'];
                if (isset($this->codeClassConversion[$type]) && $colDef['conversion'] === null) {
                    $gdf[$k]['conversion'] = $this->codeClassConversion[$type];
                }
            }
        }
        // discard columns
        $identities = $this->getDefIdentities($tableName);
        if ($defNoInsert !== null) {
            $noInsert = array_merge($identities, $defNoInsert);
        } else {
            $noInsert = $identities;
        }
        if ($defNoUpdate !== null) {
            $noUpdate = array_merge($identities, $defNoUpdate);
        } else {
            $noUpdate = $identities;
        }
        try {
            $r = str_replace([
                '{pk}',
                '{def}',
                '{defname}',
                //'{defnamealias}',
                '{defkey}',
                '{defnoinsert}',
                '{defnoupdate}',
                '{deffk}',
                '{deffktype}',
                '{array}',
                '{array_null}'
            ], [
                self::varExport($pk),
                //str_replace(["\n\t\t        ", "\n\t\t    ],"], ['', '],'], PdoOne::varExport($gdf, "\t\t")), // {def}
                self::varExport($gdf, "\t\t"),
                self::varExport(array_keys($gdf), "\t\t"), // {defname}
                //self::varExport(array_keys($gdf), "\t\t"), // {defnamealias}
                self::varExport($this->getDefTableKeys($tableName), "\t\t"), // {defkey}
                self::varExport($noInsert, "\t\t"), // {defnoinsert}
                self::varExport($noUpdate, "\t\t"), // {defnoupdate}
                self::varExport($this->getDefTableFK($tableName), "\t\t\t"), //{deffk}
                self::varExport($relation, "\t\t"), //{deffktype}
                str_replace("\n", "\n\t\t",
                    rtrim($this->generateCodeArray($tableName, null, false, false, true, $classRelations, $relation),
                        "\n")),
                str_replace("\n", "\n\t\t",
                    rtrim($this->generateCodeArray($tableName, null, true, false, true, $classRelations, $relation),
                        "\n"))
            ], $r);
        } catch (Exception $e) {
            $this->endTry();
            return "Unable read definition of tables " . $e->getMessage();
        }
        $this->endTry();
        return $r;
    }

    /**
     * It generates a class<br>
     * **Example:**
     * ```php
     * $class = $this->generateCodeClass('tablename', 'namespace\namespace2'
     *          ,['_idchild2FK'=>'PARENT' // relation
     *          ,'_tablaparentxcategory'=>'MANYTOMANY' // relation
     *          ,'col'=>'datetime3' // conversion
     *          ,'col2'=>'conversion(%s)' // custom conversion (identified by %s)
     *          ,'col3'=>] // custom conversion (identified by %s)
     *          ,'Repo');
     * $class = $this->generateCodeClass(['ClassName'=>'tablename'], 'namespace\namespace2'
     *          ,['/idchild2FK'=>'PARENT','/tablaparentxcategory'=>'MANYTOMANY']
     *          ,'Repo');
     * ```
     *
     * @param string|array  $tableName            The name of the table and the class.
     *                                            If the value is an array, then the key is the name of the table and
     *                                            the value is the name of the class
     * @param string        $namespace            The Namespace of the generated class
     * @param array|null    $columnRelations      An associative array to specific custom relations, such as PARENT<br>
     *                                            The key is the name of the columns and the value is the type of
     *                                            relation<br>
     * @param string[]|null $classRelations       The postfix of the class. Usually it is Repo or Dao.
     *
     * @param array         $specialConversion    An associative array to specify a custom conversion<br>
     *                                            The key is the name of the columns and the value is the type of
     *                                            relation<br>
     * @param string[]|null $defNoInsert          An array with the name of the columns to not insert. The identity
     *                                            is added automatically to this list
     * @param string[]|null $defNoUpdate          An array with the name of the columns to not update. The identity
     *                                            is added automatically to this list
     * @param string|null   $baseClass            The name of the base class. If no name then it uses the last namespace
     * @param string        $modelfullClass       (default:'') The full class of the model (with the namespace). If
     *                                            empty, then it doesn't use a model
     * @param array         $extraCols            An associative array with extra columns where they key is the name of
     *                                            the column and the value are the value to return (it is evaluated in
     *                                            the query). It is used by toList() and first(), it's also added to
     *                                            the model.
     *
     * @param array         $columnRemove
     * @param array         $aliasesAllTables     The aliases of every column of all table.<br>
     *                                            Example: ['col'=>'alias','col2'=>'alias2']
     * @return string|string[]
     * @throws Exception
     */
    public function generateAbstractRepo(
        $tableName,
        string $namespace = '',
        ?array $columnRelations = null,
        ?array $classRelations = null,
        array $specialConversion = [],
        ?array $defNoInsert = null,
        ?array $defNoUpdate = null,
        ?string $baseClass = null,
        string $modelfullClass = '',
        array $extraCols = [],
        array $columnRemove = [],
        array $aliasesAllTables = []
    )
    {
        /** @var array $aliases aliases of the current table */
        $aliases = $aliasesAllTables[$tableName] ?? [];
        $this->beginTry();
        $filename = __DIR__ . '/template/template_abstractrepo.php';
        $r = $this->phpstart . $this->openTemplate($filename);
        $lastns = explode('\\', $namespace);
        if ($modelfullClass) {
            $arr = explode('\\', $modelfullClass);
            $modelClass = end($arr);
            $modelUse = true;
        } else {
            $modelClass = false;
            $modelUse = false;
        }
        if ($baseClass === null) {
            $tmp3 = end($lastns);
            $baseClass = $tmp3 === false ? '' : $tmp3;
        }
        $fa = func_get_args();
        foreach ($fa as $f => $k) {
            if (is_array($k)) {
                $fa[$f] = str_replace([' ', "\r\n", "\n"], ['', '', ''], var_export($k, true));
            } else {
                $fa[$f] = "'$k'";
            }
        }
        if ($classRelations === null || !isset($classRelations[$tableName])) {
            $className = self::camelize($tableName);
        } else {
            $className = $classRelations[$tableName];
        }
        $extraColArray = '';
        foreach ($extraCols as $k => $v) {
            $extraColArray .= $v . ' as ' . $this->addQuote($k) . ',';
        }
        $extraColArray = rtrim($extraColArray, ',');
        $r = str_replace([
            '{version}',
            '{classname}',
            '{exception}',
            '{baseclass}',
            '{args}',
            '{table}',
            '{namespace}',
            '{modelnamespace}',
            '{classmodellist}',
            '{classmodelfirst}',
            '{extracol}'
        ], [
            self::VERSION . ' Date generated ' . date('r'), //{version}
            $className, // {classname}
            ($namespace) ? 'use Exception;' : '',
            $baseClass, // {baseclass}
            implode(",", $fa),
            $tableName, // {table}
            ($namespace) ?: '', //{namespace}
            $modelUse ? "use $modelfullClass;" : '', // {modelnamespace}
            $modelUse ? "$modelClass::fromArrayMultiple( self::_toList(\$filter, \$filterValue));"
                : 'false; // no model set',  // {classmodellist}
            $modelUse ? "$modelClass::fromArray(self::_first(\$pk));" : 'false; // no model set' // {classmodelfirst}
            ,
            $extraColArray // {extracol}
        ], $r);
        $pk = $this->service->getPK($tableName, '??');
        $pkFirst = (is_array($pk) && count($pk) > 0) ? $pk[0] : null;
        [$relation, $linked] = $this->generateGetRelations($tableName, $columnRelations, $pkFirst, $aliasesAllTables);
        if (!is_array($relation)) {
            $this->endTry();
            return 'Error: Unable read fk of table ' . $relation;
        }
        $convertOutput = '';
        $convertInput = '';
        $getDefTable = $this->getDefTable($tableName, $specialConversion);
        foreach ($columnRemove as $v) {
            unset($getDefTable[$v]);
        }
        // we forced the conversion but only if it is not specified explicit
        $allColumns = array_merge($getDefTable, $extraCols); // $extraColArray does not have type
        foreach ($allColumns as $kcol => $colDef) {
            $type = $colDef['type'] ?? null;
            $conversion = null;
            if (!isset($aliases[$kcol])) {
                $aliases[$kcol] = $kcol;
            }
            $getDefTable[$kcol]['alias'] = $aliases[$kcol] ?? $kcol;
            $alias = $getDefTable[$kcol]['alias'];
            if (isset($columnRelations[$kcol])) {
                $conversion = $columnRelations[$kcol];
                if ($type !== null) {
                    $getDefTable[$kcol]['conversion'] = $conversion;
                } else {
                    $type = 'new column';
                }
            } elseif ($type !== null && isset($this->codeClassConversion[$type])
                && $getDefTable[$kcol]['conversion'] === null
            ) {
                $conversion = $this->codeClassConversion[$type];
                $getDefTable[$kcol]['conversion'] = $conversion;
            }
            if ($conversion !== null) {
                if (is_array($conversion)) {
                    [$input, $output] = $conversion;
                } else {
                    $input = $conversion;
                    $output = $input;
                }
                switch ($input) {
                    case 'encrypt':
                        $tmp2 = "isset(%s) and %s=self::getPdoOne()->encrypt(%s);";
                        break;
                    case 'decrypt':
                        $tmp2 = "isset(%s) and %s=self::getPdoOne()->decrypt(%s);";
                        break;
                    case 'datetime3':
                        $tmp2 = "isset(%s) and %s=PdoOne::dateConvert(%s, 'human', 'sql');";
                        break;
                    case 'datetime4':
                        $tmp2 = '';
                        //$tmp2 = "isset(%s) and %s=PdoOne::dateConvert(%s, 'sql', 'sql');";
                        break;
                    case 'datetime2':
                        $tmp2 = "isset(%s) and %s=PdoOne::dateConvert(%s, 'iso', 'sql');";
                        break;
                    case 'datetime':
                        $tmp2 = "isset(%s) and %s=PdoOne::dateConvert(%s, 'class', 'sql');";
                        break;
                    case 'timestamp':
                        $tmp2 = "isset(%s) and %s=PdoOne::dateConvert(%s, 'timestamp', 'sql')";
                        break;
                    case 'bool':
                        $tmp2 = "isset(%s) and %s=(%s) ? 1 : 0;";
                        break;
                    case 'int':
                        $tmp2 = "isset(%s) and %s=(int)%s;";
                        break;
                    case 'string':
                        $tmp2 = "isset(%s) and %s=(string)%s;";
                        break;
                    case 'float':
                    case 'decimal':
                        $tmp2 = "isset(%s) and %s=(float)%s;";
                        break;
                    default:
                        if (strpos($input, '%s') !== false) {
                            $tmp2 = "%s=isset(%s) ? " . $input . " : null;";
                        } else {
                            $tmp2 = '// type ' . $input . ' not defined';
                        }
                }
                switch ($output) {
                    case 'encrypt':
                        $tmp = "%s=isset(%s) ? self::getPdoOne()->encrypt(%s) : null;";
                        break;
                    case 'decrypt':
                        $tmp = "%s=isset(%s) ? self::getPdoOne()->decrypt(%s) : null;";
                        break;
                    case 'datetime3':
                        $tmp = "%s=isset(%s) ? PdoOne::dateConvert(%s, 'sql', 'human') : null;";
                        break;
                    case 'datetime4':
                        // sql->sql no conversion
                        $tmp = '';
                        break;
                    case 'varchar':
                        // sql->sql no conversion
                        $tmp = "%s=isset(%s) ? (string)%s : null;";
                        break;
                    case 'datetime2':
                        $tmp = "%s=isset(%s) ? PdoOne::dateConvert(%s, 'sql', 'iso') : null;";
                        break;
                    case 'datetime':
                        $tmp = "%s=isset(%s) ? PdoOne::dateConvert(%s, 'sql', 'class') : null;";
                        break;
                    case 'timestamp':
                        $tmp = "%s=isset(%s) ? PdoOne::dateConvert(%s, 'sql', 'timestamp') : null;";
                        break;
                    case 'bool':
                        $tmp = "%s=isset(%s) ? (%s) ? true : false : null;";
                        break;
                    case 'int':
                        $tmp = "%s=isset(%s) ? (int)%s : null;";
                        break;
                    case 'float':
                    case 'decimal':
                        $tmp = "%s=isset(%s) ? (float)%s : null;";
                        break;
                    case null:
                    case 'nothing':
                    case 'null':
                        $tmp = "!isset(%s) and %s=null; // no conversion";
                        break;
                    default:
                        if (strpos($output, '%s') !== false) {
                            $tmp = "%s=isset(%s) ? " . $output . " : null;";
                        } else {
                            $tmp = '// type ' . $output . ' not defined';
                        }
                }
                if ($tmp !== '') {
                    $convertOutput .= "\t\t" . str_replace('%s', "\$row['$alias']", $tmp) . "\n";
                    $convertInput .= "\t\t" . str_replace('%s', "\$row['$alias']", $tmp2) . "\n";
                }
            } else {
                $tmp = "!isset(%s) and %s=null; // $type";
                $convertOutput .= "\t\t" . str_replace('%s', "\$row['$alias']", $tmp) . "\n";
            }
        }
        //$convertOutput.=$linked;
        $convertOutput = rtrim($convertOutput, "\n");
        $convertInput = rtrim($convertInput, "\n");
        // discard columns
        //$identities=$this->getDefTableKeys($tableName,);
        $identities = $this->getDefIdentities($tableName);
        if (count($identities) > 0) {
            $identity = $identities[0];
        } else {
            $identity = null;
        }
        if ($defNoInsert !== null) {
            $noInsert = array_merge($identities, $defNoInsert);
        } else {
            $noInsert = $identities;
        }
        if ($defNoUpdate !== null) {
            $noUpdate = array_merge($identities, $defNoUpdate);
        } else {
            $noUpdate = $identities;
        }
        /*$copy = $noInsert;
        $noInsert = [];
        foreach ($copy as $v) {
            if (isset($aliases[$v])) {
                $noInsert[] = $aliases[$v];
            } else {
                $noInsert[] = $v;
            }
        }*/
        /*$copy = $noUpdate;
        $noUpdate = [];
        foreach ($copy as $v) {
            if (isset($aliases[$v])) {
                $noUpdate[] = $aliases[$v];
            } else {
                $noUpdate[] = $v;
            }
        }*/
        if ($pk) {
            // we never update the primary key.
            $noUpdate += $pk; // it adds and replaces duplicates, indexes are ignored.
        }
        $relation2 = [];
        foreach ($relation as $arr) {
            if ($arr['key'] !== 'FOREIGN KEY' && $arr['key'] !== 'PARENT' && $arr['key'] !== 'NONE') {
                @$relation2[$arr['key']][] = '/' . $arr['alias'];
            }
            //if($arr['key']==='MANYTOONE') {
            //    $relation2[]=$col;
            // }
        }
        $listAlias = [];
        foreach ($getDefTable as $k => $v) {
            $listAlias[$k] = $v['alias'];
        }
        try {
            $r = str_replace([
                '{pk}',
                '{identity}',
                '{def}',
                '{convertoutput}',
                '{convertinput}',
                '{defname}',
                '{defnamealias}',
                '{defnamealiasinv}',
                '{defkey}',
                '{defnoinsert}',
                '{defnoupdate}',
                '{deffk}',
                '{deffktype}',
                '{deffktype2}',
                '{array}',
                '{factory}',
                '{factoryrecursive}',
                '{linked}'
            ], [
                self::varExport($pk),
                self::varExport($identity), // {identity}
                //str_replace(["\n\t\t        ", "\n\t\t    ],"], ['', '],'], self::varExport($gdf, "\t\t")), // {def}
                self::varExport($getDefTable, "\t\t"), // {def}
                $convertOutput, // {convertoutput}
                $convertInput, // {convertinput}
                self::varExport(array_keys($getDefTable), "\t\t"), // {defname}
                self::varExport($listAlias, "\t\t"), // {defnamealias}
                self::varExport(array_flip($listAlias), "\t\t"), // {defnamealiasinv}
                self::varExport($this->getDefTableKeys($tableName), "\t\t"), // {defkey}
                self::varExport($noInsert, "\t\t"), // {defnoinsert}
                self::varExport($noUpdate, "\t\t"), // {defnoupdate}
                self::varExport($this->getDefTableFK($tableName), "\t\t\t"), //{deffk}
                self::varExport($relation, "\t\t"), //{deffktype}
                self::varExport($relation2, "\t\t"), //{deffktype2}
                self::varExport($this->generateCodeArrayConst(
                    $getDefTable, $classRelations ?? [], $relation, 'function'), "\t\t"), // {array}
                self::varExport($this->generateCodeArrayConst(
                    $getDefTable, $classRelations ?? [], $relation, 'constant'), "\t\t"), // {factory}
                str_replace(["\n", "\t", "    "], "", self::varExport($this->generateCodeArrayRecursive(
                    $getDefTable, $classRelations ?? [], $relation, 'function'))),// {factoryrecursive}
                $linked // {linked}
            ], $r);
        } catch (Exception $e) {
            $this->endTry();
            return "Unable read definition of tables " . $e->getMessage();
        }
        $this->endTry();
        return $r;
    }

    /**
     * It builds (generates source code) of the base, repo and repoext classes of the current schema.<br>
     * **Example:**
     * ```php
     * // with model
     * $this->generateAllClasses([
     *          'products'=>['ProductRepo','ProductModel']
     *          ,'types'=>['TypeRepo','TypeModel']
     *          ],
     *          'SakilaBase'
     *          ,['eftec\repo','eftec\model']
     *          ,['c:/temp','c:/tempmodel']
     *          ,false,
     *          [
     *              'products'=>['_col'=>'PARENT' // relations
     *              ,'_col2'=>'MANYTOMANY' // relations
     *              ,'col1'=>'encrypt' // encrypt (input and output)
     *              ,'col2'=>['encrypt','decrypt'] // encrypt input and decrypt output
     *              ,'col3'=>['encrypt',null] // encrypt input and none output
     *          ]]);
     * // without model
     * $this->generateAllClasses([
     *          'products'=>'ProductRepo'
     *          ,'types'=>'TypeRepo'
     *          ]
     *          ,'SakilaBase'
     *          ,'eftec\repo'
     *          ,'c:/temp'
     *          ,false,
     *          ['products'=>['_col'=>'PARENT','_col2'=>'MANYTOMANY'],
     *          ['products'=>['extracol'=>'now()']]
     *          ]);
     * ```
     *
     * @param array        $relations       Where the key is the name of the table, and the value is an array with
     *                                      the name of the repository class and the name of the model class <br>
     *                                      If the value is not an array, then it doesn't build a model class<br>
     *                                      <b>Example:</b> ['products'=>'ProductRepo','types'=>'TypeRepo']<br>
     *                                      <b>Example:</b> ['products'=>['ProductRepo','ProductModel'] ]<br>
     * @param string       $baseClass       The name of the base class.
     * @param array|string $namespaces      (default:'') The name of the namespace. Example 'eftec\repo'<br>
     *                                      If we want to use a model class, then we need to set the namespace of the
     *                                      repository class and the namespace of the model class<br>
     *                                      ['c:/temp','c:/tempmodel'].
     * @param array|string $folders         (default:'') The name of the folder where the classes will be store.<br>
     *                                      If we want to use a model class, then we need to set the folder of the
     *                                      repository class and the folder of the model class<br>
     *                                      ['eftec\repo','eftec\model'].
     *                                      Example: 'c:/folder'
     * @param bool         $force           (default:false), if true then it will overwrite the repo files (if any).
     * @param array        $columnRelations (default:[]) An associative array with custom relations or
     *                                      conversion per table.<br>
     *                                      If we want to indicate a relation PARENT/MANYTOMANY, then we must use
     *                                      this array.<br>
     *                                      Example:['products'=>['_col'=>'PARENT','_col2'=>'MANYTOMANY']<br>
     *                                      If the column is not relational, then it is the column used to determine the
     *                                      conversion.<br>
     *                                      Example:['products'=>['col'=>'int']] // convert int input/output<br>
     *                                      Example:['products'=>['col'=>['encrypt','decrypt']] // encrypt input and
     *                                      decrypt output<br>
     *                                      <b>Conversion allowed</b> (see generateCodeClassConversions)
     * @param array        $extraColumns    An associative array with extra columns per table. It has the same form
     *                                      as $columnRelations. The columns are returned when we use toList() and
     *                                      first() and they are added to the model (if any) but they are not used in
     *                                      insert,update or delete<br>
     * @param array        $columnRemoves   An associative array to skip in the generation with the key as the name of
     *                                      the table and value an array with columns to be removed.<br>
     *                                      Example:['products'=>['colnotread']]
     *
     * @param array        $aliases         An associative array with the alias of every column.<br>
     *                                      If this array is empty, then it keeps the current value
     * @return array It returns an array with all the errors or warnings (if any).
     * @throws JsonException
     * @see PdoOne::generateCodeClassConversions
     */
    public function generateAllClasses(
        array  $relations,
        string $baseClass,
               $namespaces = '',
               $folders = '',
        bool   $force = false,
        array  $columnRelations = [],
        array  $extraColumns = [],
        array  $columnRemoves = [],
        array  $aliases = []
    ): array
    {


        $internalCache = $this->useInternalCache;
        $this->setUseInternalCache();
        if (is_array($folders)) {
            [$folder, $folderModel] = $folders;
        } else {
            $folder = $folders;
            $folderModel = $folders;
        }
        if (is_array($namespaces)) {
            [$namespace, $namespaceModel] = $namespaces;
        } else {
            if (is_null($namespaces) || is_null($folder)) {
                throw new RuntimeException('namespace or folder is not set');
            }
            $namespace = $namespaces;
            $namespaceModel = $namespaces;
        }
        $firstKeyRelation = array_keys($relations)[0];
        $firstRelation = $relations[$firstKeyRelation]; // the first value of the relation arrays.
        if (is_array($firstRelation)) {
            $useModel = true;
            $relationsRepo = [];
            $relationsModel = [];
            foreach ($relations as $k => $v) {
                $relationsRepo[$k] = $v[0];
                $relationsModel[$k] = $v[1];
            }
        } else {
            $useModel = false;
            $relationsRepo = [];
            $relationsModel = [];
            foreach ($relations as $k => $v) {
                $relationsRepo[$k] = $v;
                $relationsModel[$k] = $v . 'Model';
            }
        }
        // BASE CLASS *******************************
        $folder = rtrim($folder, '/') . '/';
        $folderModel = rtrim($folderModel, '/') . '/';
        $logs = [];
        try {
            $classCode = $this->generateBaseClass($baseClass, $namespace, $relationsRepo, $useModel);
            $result = self::saveFile($folder . $baseClass . '.php', $classCode);
        } catch (Exception $exception) {
            $result = false;
        }
        if ($result === false) {
            $logs[] = "Unable to save Base Class file '$folder$baseClass.php'";
        }
        // CODE CLASSES, MODELS *******************************
        foreach ($relationsRepo as $tableName => $className) {
            if ($useModel) {
                $modelname = $namespaceModel . '\\' . $relationsModel[$tableName];
            } else {
                $modelname = '';
            }
            try {
                $custom = $columnRelations[$tableName] ?? [];
                $extraCols = $extraColumns[$tableName] ?? [];
                $columnRem = $columnRemoves[$tableName] ?? [];
                $classCode1 = $this->generateAbstractRepo($tableName, $namespace, $custom, $relationsRepo, [], null, null,
                    $baseClass, $modelname, $extraCols, $columnRem, $aliases ?? []);
                $result = self::saveFile($folder . "Abstract$className.php", $classCode1);
            } catch (Exception $e) {
                $result = false;
            }
            if ($result === false) {
                $logs[] = "Unable to save Repo Abstract Class file '{$folder}Abstract$className.php' "
                    . json_encode(error_get_last(), JSON_THROW_ON_ERROR);
            }
            // creating model
            $resultcolumns = [];
            try {
                // we need to generate it to obtain resultcolumns
                $classModel1 = $this->generateAbstractModelClass($tableName, $namespaceModel, $custom,
                    $relationsModel, [], null, null, $baseClass, $extraCols, $columnRem, $resultcolumns);
            } catch (Exception $e) {
                $result = false;
                $classModel1 = 'error ' . $e->getMessage();
            }
            if ($result === false) {
                $logs[] = "Error: Unable to save Abstract Model Class file '{$folder}Abstract"
                    . $relationsModel[$tableName] . ".php' " . json_encode(error_get_last(), JSON_THROW_ON_ERROR);
            }
            if ($useModel) {
                try {
                    //$custom = (isset($customRelation[$tableName])) ? $customRelation[$tableName] : [];
                    $result = self::saveFile($folderModel . 'Abstract' . $relationsModel[$tableName] . '.php',
                        $classModel1);
                } catch (Exception $e) {
                    $result = false;
                }
                if ($result === false) {
                    $logs[] = "Error: Unable to save Abstract Model Class file '{$folder}Abstract"
                        . $relationsModel[$tableName] . ".php' " . json_encode(error_get_last(), JSON_THROW_ON_ERROR);
                }
                try {
                    $filename = $folderModel . $relationsModel[$tableName] . '.php';
                    $classModel1 = $this->generateModelClass($tableName, $namespaceModel, $custom, $relationsModel, [],
                        null, null, $baseClass);
                    if ($force || @!file_exists($filename)) {
                        $result = self::saveFile($filename, $classModel1);
                    } else {
                        $logs[] = "Warning: Unable to save Model Class file '$filename', file already exist, skipped";
                    }
                } catch (Exception $e) {
                    $result = false;
                }
                if ($result === false) {
                    $logs[] = "Error: Unable to save Model Class file '$filename' " . json_encode(error_get_last(), JSON_THROW_ON_ERROR);
                }
            }
            try {
                $filename = $folder . $className . '.php';
                $classCode2 = $this->generateCodeClassRepo($tableName,
                    $namespace,
                    $relationsRepo,
                    $modelname,
                    $resultcolumns,
                    $aliases
                );
                if ($force || @!file_exists($filename)) {
                    // if the file exists then, we don't want to replace this class
                    $result = self::saveFile($filename, $classCode2);
                } else {
                    $logs[] = "Warning: Unable to save Repo Class file '$folder$className.php', file already exist, skipped";
                }
            } catch (Exception $e) {
                $result = false;
            }
            if ($result === false) {
                $logs[] = "Error: Unable to save Repo Class file '$folder$className.php' " . json_encode(error_get_last(), JSON_THROW_ON_ERROR);
            }
        }
        $this->setUseInternalCache($internalCache);
        return $logs;
    }

    public function run(
        string $database,
        string $server,
        string $user,
        string $pwd,
        string $db,
        string $input,
        string $output,
        string $namespace
    )
    {
        $r = parent::run($database, $server, $user, $pwd, $db, $input, $output, $namespace);
        switch ($output) {
            case 'createcode':
                return $this->generateCodeCreate($input);
            case 'classcode':
                return $this->generateAbstractRepo($input, $namespace);
        }
        return $r;
    }

    public function generateBaseClass($baseClassName, $namespace, $classes, $modelUse = false)
    {
        $filename = __DIR__ . '/template/template_base.php';
        $r = $this->phpstart . $this->openTemplate($filename);
        /*foreach($classes as $id=>$entity) {
            foreach($entity as $k=>$class) {
                $classes[$id][$k] = $namespace . '\\' . $class;
            }
        }
        */
        $namespace = trim($namespace, '\\');
        return str_replace([
            '{type}',
            '{class}',
            '{exception}',
            '{namespace}',
            '{namespace2}',
            '{relations}',
            '{modeluse}',
            '{version}',
            '{compiled}'
        ], [
            $this->databaseType,
            $baseClassName,
            ($namespace) ? 'use Exception;' : '', // {exception}
            ($namespace) ?: '', // {namespace}
            ($namespace) ? "$namespace\\\\" : '', // {namespace2}
            $this::varExport($classes),
            $modelUse ? 'true' : 'false', // {modeluse}
            self::VERSION . ' Date generated ' . date('r'), // {version}
            _BasePdoOneRepo::BINARYVERSION, // {compiled}
        ], $r);
    }

    public function generateCodeClassRepo(
        $tableName,
        $namespace = '',
        $classRelations = [],
        $modelfullClass = '',
        $resultColumns = [],
        $aliases = []
    )
    {
        $this->beginTry();
        //
        $filename = __DIR__ . '/template/template_classrepo.php';
        $r = $this->phpstart . $this->openTemplate($filename);
        $fa = func_get_args();
        foreach ($fa as $f => $k) {
            if (is_array($k)) {
                $fa[$f] = str_replace([' ', "\r\n", "\n"], ['', '', ''], var_export($k, true));
            } else {
                $fa[$f] = "'$k'";
            }
        }
        if ($modelfullClass) {
            $arr = explode('\\', $modelfullClass);
            $modelClass = end($arr);
            $modelUse = true;
        } else {
            $modelClass = false;
            $modelUse = false;
        }
        $helpcolumns = '';
        $related = '';
        foreach ($resultColumns as $v) {
            if ($v[2]) {
                $related .= " * @see $v[2]\n";
                $c = '(' . $v[2] . ')';
            } else {
                $c = '';
            }
            $alias = $aliases[$tableName][$v[0]] ?? $v[0];
            $helpcolumns .= " * <li><b>$alias</b>: $v[1] (alias of column $v[0]) $c</li>\n";
        }
        $this->endTry();
        return str_replace([
            '{version}',
            '{classname}',
            '{exception}',
            '{args}',
            '{table}',
            '{namespace}',
            '{modelnamespace}',
            '{modelclass}',
            '{modeluse}',
            '{helpcolumns}',
            '{related}'
        ], [
            self::VERSION . ' Date generated ' . date('r'), // {version}
            $classRelations[$tableName], // {class}
            ($namespace) ? 'use Exception;' : '',
            "'" . implode("','", $fa) . "'", // {args}
            $tableName, //{table}
            ($namespace) ?: '', // {namespace}
            $modelfullClass ? "use $modelfullClass;" : '', // {modelnamespace}
            $modelClass ? "const MODEL= $modelClass::class;" : '', // {modelclass}
            $modelUse ? 'true' : 'false', // {modeluse},
            rtrim($helpcolumns), // {helpcolumns}
            rtrim($related)
        ], $r);
    }

    /**
     * @param string $tableName
     *
     * @return string
     * @throws Exception
     */
    public function generateCodeCreate(string $tableName): string
    {
        $this->beginTry();
        $code = "\$pdo->createTable('" . $tableName . "',\n";
        $arr = $this->getDefTable($tableName);
        $arrKey = $this->getDefTableKeys($tableName);
        $arrFK = self::varExport($this->getDefTableFK($tableName));
        $keys = self::varExport($arrKey);
        $code .= "\t" . self::varExport($arr);
        $code .= ",$keys);\n";
        $code .= "\$pdo->createFk('" . $tableName . "',\n";
        $code .= "$arrFK);\n";
        $this->endTry();
        return $code;
    }
}
