<?php
/** @noinspection PhpUnusedParameterInspection
* @noinspection PhpClassConstantAccessedViaChildClassInspection
* @noinspection PhpClasspublic constantAccessedViaChildClassInspection
* @noinspection NullCoalescingOperatorCanBeUsedInspection
* @noinspection PhpPureAttributeCanBeAddedInspection
* @noinspection PhpArrayShapeAttributeCanBeAddedInspection
* @noinspection AccessModifierPresentedInspection
* @noinspection UnknownInspectionInspection
* @noinspection PhpIncompatibleReturnTypeInspection
* @noinspection ReturnTypeCanBeDeclaredInspection
* @noinspection DuplicatedCode
* @noinspection PhpUnused
* @noinspection PhpUndefinedMethodInspection
* @noinspection PhpUnusedLocalVariableInspection
* @noinspection PhpUnusedAliasInspection
* @noinspection NullPointerExceptionInspection
* @noinspection SenselessProxyMethodInspection
* @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection
*/
namespace eftec\examples\example2\repo;
use eftec\PdoOne;
use eftec\PdoOneQuery;

use Exception;
// [EDIT:use] you can edit this part
// Here you can add your custom use
// [/EDIT] end of edit

/**
 * Class AbstractActorRepo. Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * Generated by PdoOne Version 1.00 Date generated Sat, 11 Mar 2023 18:09:05 -0300.<br>
 * <b>DO NOT EDIT THE CODE OUTSIDE EDIT BLOCKS</b>. This code is generated<br>
 * If you want to make some changes, then add the changes to the Repository class.<br>
 * <pre>
 * $code=$pdoOne->generateCodeClass('actor','eftec\examples\example2\repo',array('actor_id'=>NULL,'first_name'=>NULL,'last_name'=>NULL,'last_update'=>NULL,'_film_actor'=>'ONETOMANY',),array('actor'=>'ActorRepo','address'=>'AddresRepo','category'=>'CategoryRepo','city'=>'CityRepo','country'=>'CountryRepo','customer'=>'CustomerRepo','film'=>'FilmRepo','film_actor'=>'FilmActorRepo','film_category'=>'FilmCategoryRepo','film_text'=>'FilmTextRepo','inventory'=>'InventoryRepo','language'=>'LanguageRepo','payment'=>'PaymentRepo','rental'=>'RentalRepo','staff'=>'StaffRepo','store'=>'StoreRepo',),array(),'','','Sakila','',array(),array(),array('actor'=>array('actor_id'=>'actor_id','first_name'=>'first_name','last_name'=>'last_name','last_update'=>'last_update','_film_actor'=>'_film_actor',),'address'=>array('address_id'=>'address_id','address'=>'address','address2'=>'address2','district'=>'district','city_id'=>'city_id','postal_code'=>'postal_code','phone'=>'phone','last_update'=>'last_update','_city_id'=>'_city_id','_customer'=>'_customer','_staff'=>'_staff','_store'=>'_store',),'category'=>array('category_id'=>'category_id','name'=>'name','last_update'=>'last_update','_film_category'=>'_film_category',),'city'=>array('city_id'=>'city_id','city'=>'city','country_id'=>'country_id','last_update'=>'last_update','_country_id'=>'_country_id','_address'=>'_address',),'country'=>array('country_id'=>'country_id','country'=>'country','last_update'=>'last_update','_city'=>'_city',),'customer'=>array('customer_id'=>'customer_id','store_id'=>'store_id','first_name'=>'first_name','last_name'=>'last_name','email'=>'email','address_id'=>'address_id','active'=>'active','create_date'=>'create_date','last_update'=>'last_update','_address_id'=>'_address_id','_store_id'=>'_store_id','_payment'=>'_payment','_rental'=>'_rental',),'film'=>array('film_id'=>'film_id','title'=>'title','description'=>'description','release_year'=>'release_year','language_id'=>'language_id','original_language_id'=>'original_language_id','rental_duration'=>'rental_duration','rental_rate'=>'rental_rate','length'=>'length','replacement_cost'=>'replacement_cost','rating'=>'rating','special_features'=>'special_features','last_update'=>'last_update','_language_id'=>'_language_id','_original_language_id'=>'_original_language_id','_film_actor'=>'_film_actor','_inventory'=>'_inventory',),'film_actor'=>array('actor_id'=>'actor_id','film_id'=>'film_id','last_update'=>'last_update','_actor_id'=>'_actor_id','_film_id'=>'_film_id',),'film_category'=>array('film_id'=>'film_id','category_id'=>'category_id','last_update'=>'last_update','_category_id'=>'_category_id',),'film_text'=>array('film_id'=>'film_id','title'=>'title','description'=>'description',),'inventory'=>array('inventory_id'=>'inventory_id','film_id'=>'film_id','store_id'=>'store_id','last_update'=>'last_update','_film_id'=>'_film_id','_store_id'=>'_store_id','_rental'=>'_rental',),'language'=>array('language_id'=>'language_id','name'=>'name','last_update'=>'last_update','_film'=>'_film',),'payment'=>array('payment_id'=>'payment_id','customer_id'=>'customer_id','staff_id'=>'staff_id','rental_id'=>'rental_id','amount'=>'amount','payment_date'=>'payment_date','last_update'=>'last_update','_customer_id'=>'_customer_id','_rental_id'=>'_rental_id','_staff_id'=>'_staff_id',),'rental'=>array('rental_id'=>'rental_id','rental_date'=>'rental_date','inventory_id'=>'inventory_id','customer_id'=>'customer_id','return_date'=>'return_date','staff_id'=>'staff_id','last_update'=>'last_update','_customer_id'=>'_customer_id','_inventory_id'=>'_inventory_id','_staff_id'=>'_staff_id','_payment'=>'_payment',),'staff'=>array('staff_id'=>'staff_id','first_name'=>'first_name','last_name'=>'last_name','address_id'=>'address_id','picture'=>'picture','email'=>'email','store_id'=>'store_id','active'=>'active','username'=>'username','password'=>'password','last_update'=>'last_update','_address_id'=>'_address_id','_store_id'=>'_store_id','_payment'=>'_payment','_rental'=>'_rental','_store'=>'_store',),'store'=>array('store_id'=>'store_id','manager_staff_id'=>'manager_staff_id','address_id'=>'address_id','last_update'=>'last_update','_address_id'=>'_address_id','_manager_staff_id'=>'_manager_staff_id','_customer'=>'_customer','_inventory'=>'_inventory','_staff'=>'_staff',),));
 * </pre>
 */
abstract class AbstractActorRepo extends Sakila
{
    public const ENTITY = 'ActorRepo';
    public const TABLE = 'actor';
    /** @var string A string with the name of the class. It is used to identify itself. */
    public const IDENTITY = 'actor_id';
    /** @var string[] An indexed array with the original name of the primary keys. Note: Only the first primary key is used */
    public const PK = [
	    'actor_id'
	];
    /** @var string A string with the name of the class. It is used to identify itself. */
    public const ME=__CLASS__;
    /** @var string|null $schema you can set the current schema/database used by this class. [Default is null] */
    public static $schema;

    //<editor-fold desc="definitions">
    public const EXTRACOLS='';
    /** @var string[][] an associative array with the definition of the columns */
    public const DEF=[
		    'actor_id' => [
		        'alias' => 'actor_id',
		        'phptype' => 'int',
		        'conversion' => null,
		        'type' => 'int',
		        'size' => null,
		        'null' => false,
		        'identity' => true,
		        'sql' => 'int not null auto_increment'
		    ],
		    'first_name' => [
		        'alias' => 'first_name',
		        'phptype' => 'string',
		        'conversion' => null,
		        'type' => 'varchar',
		        'size' => '45',
		        'null' => false,
		        'identity' => false,
		        'sql' => 'varchar(45) not null'
		    ],
		    'last_name' => [
		        'alias' => 'last_name',
		        'phptype' => 'string',
		        'conversion' => null,
		        'type' => 'varchar',
		        'size' => '45',
		        'null' => false,
		        'identity' => false,
		        'sql' => 'varchar(45) not null'
		    ],
		    'last_update' => [
		        'alias' => 'last_update',
		        'phptype' => 'datetime',
		        'conversion' => null,
		        'type' => 'datetime',
		        'size' => '3',
		        'null' => false,
		        'identity' => false,
		        'sql' => 'datetime(3) not null default \'CURRENT_TIMESTAMP(3)\' DEFAULT_GENERATED'
		    ]
		];
    /** @var string[][] an associative array with the definition of the foreign keys and relations */
    public const DEFFK=[
		    '_film_actor' => [
		        'key' => 'ONETOMANY',
		        'col' => 'actor_id',
		        'reftable' => 'film_actor',
		        'refcol' => '_actor_id',
		        'alias' => '_film_actor',
		        'colalias' => 'actor_id',
		        'refcolalias' => 'actor_id',
		        'refcol2alias' => null
		    ]
		];
    public const DEFFKSQL=[

			];
    /** @var string[] An indexed array with the database name of the columns that are not inserted */
    public const DEFNOINSERT=[
		    'actor_id'
		];
    /** @var string[] An indexed array with the database name of the columns that are not updated */
    public const DEFNOUPDATE=[
		    'actor_id'
		];
    /** @var string[] an associative array that associates the database column with its alias, ex: ['col'=>'colalias'] */
    public const COL2ALIAS=[
		    'actor_id' => 'actor_id',
		    'first_name' => 'first_name',
		    'last_name' => 'last_name',
		    'last_update' => 'last_update'
		];
    /** @var string[] an associative array that associates the alias with its database column, ex: ['colalias'=>'col'] */
    public const ALIAS2COL=[
		    'actor_id' => 'actor_id',
		    'first_name' => 'first_name',
		    'last_name' => 'last_name',
		    'last_update' => 'last_update'
		];
    //</editor-fold>


    /**
    * It returns the definitions of the columns<br>
    * <b>Example:</b><br>
    * <pre>
         * self::getDef(); // ['colName'=>[php type,php conversion type,type,size,nullable,extra,sql],'colName2'=>..]
         * self::getDef('sql'); // ['colName'=>'sql','colname2'=>'sql2']
         * self::getDef('identity',true); // it returns the columns that are identities ['col1','col2']
         * </pre>
    * <b>PHP Types</b>: binary, date, datetime, decimal/float,int, string,time, timestamp<br>
    * <b>PHP Conversions</b>:  datetime (datetime class), datetime2 (iso),datetime3 (human string)
    *                         , datetime4 (sql no conversion!), timestamp (int), bool, int, float<br>
    * <b>Param Types</b>: PDO::PARAM_LOB, PDO::PARAM_STR, PDO::PARAM_INT<br>
    *
    * @param string|null $column =['phptype','conversion','type','size','null','identity','sql'][$i]
    *                             if not null then it only returns the column specified.
    * @param string|null $filter If filter is not null, then it uses the column to filter the result.
    *
    * @return array|array[]
    */
    public static function getDef(?string $column = null,?string $filter = null): array
    {
        $r = self::DEF;
        if ($column !== null) {
            if ($filter === null) {
                foreach ($r as $k => $v) {
                    $r[$k] = $v[$column] ?? null;
                }
            } else {
                $new = [];
                foreach ($r as $k => $v) {
                    if (isset($v[$column]) && $v[$column] === $filter) {
                        $new[] = $k;
                    }
                }
                return $new;
            }
        }
        return $r;
    }

    /**
    * It converts a row returned from the database.<br>
    * If the column is missing then it sets the field as null.
    *
    * @param null|array $row [ref]
    */
    public static function convertOutputVal(?array &$row): void
    {
        if ($row === false || $row === null) {
            return;
        }
        if(count($row)===0) {
            $row=null;
            return;
        }
		!isset($row['actor_id']) and $row['actor_id']=null; // int
		!isset($row['first_name']) and $row['first_name']=null; // varchar
		!isset($row['last_name']) and $row['last_name']=null; // varchar
		!isset($row['last_update']) and $row['last_update']=null; // datetime

    }

    /**
    * It converts a row to be inserted or updated into the database.<br>
    * If the column is missing then it is ignored and not converted.
    *
    * @param array $row [ref]
    * @return array
    */
    public static function convertInputVal(array &$row): array {

        return $row;
    }


    /**
    * It gets all the name of the columns.
    * @param bool $alias if true then it returns the aliases.
    * @return string[]
    */
    public static function getDefName(bool $alias=false): array {
        if($alias) {
            return static::COL2ALIAS;
        }
        return [
		    'actor_id',
		    'first_name',
		    'last_name',
		    'last_update'
		];
    }

    /**
    * It returns an associative array (colname=>key type) with all the keys/indexes (if any)
    *
    * @return string[]
    */
    public static function getDefKey(): array {
        return [
		    'actor_id' => 'PRIMARY KEY',
		    'first_name' => 'KEY'
		];
    }

    /**
    * It returns a string array with the name of the columns that are skipped when insert
    * @return string[]
    */
    public static function getDefNoInsert(): array {
        return [
		    'actor_id'
		];
    }

    /**
    * It returns a string array with the name of the columns that are skipped when update
    * @return string[]
    */
    public static function getDefNoUpdate(): array {
        return [
		    'actor_id'
		];
    }

    /**
    * It adds a condition to the query pipeline. It could be stacked using multiple where()
    * <b>Example:</b><br>
    * <pre>
         * self::where(['col'=>'value'])::toList();
         * self::where(['col']=>['value'])::toList(); // s= string/double/date, i=integer, b=bool
         * self::where(['col=?']=>['value'])::toList(); // s= string/double/date, i=integer, b=bool
         * </pre>
    *
    * @param array|string   $sql =self::factoryUtil()
    * @param null|array|int $param
    *
    * @return PdoOneQuery
    */
    public static function where($sql, $param = PdoOne::NULL): PdoOneQuery
    {
        return static::newQuery()->where($sql, $param,false,ActorRepo::TABLE);
    }

    /**
    * It returns all the relational fields by type. '*' returns all types.<br>
    * It doesn't return normal columns.
    *
    * @param string $type=['*','MANYTOONE','ONETOMANY','ONETOONE','MANYTOMANY'][$i]
    *
    * @return string[]
    * @noinspection SlowArrayOperationsInLoopInspection
    */
    public static function getRelations(string $type = 'all'): array
    {
        $r = [
		    'ONETOMANY' => [
		        '/_film_actor'
		    ]
		];
        if ($type === '*') {
            $result = [];
            foreach ($r as $arr) {
                $result = array_merge($result, $arr);
            }
            return $result;
        }
        return $r[$type] ?? [];
    }

    /**
    * @param array|int  $filter      (optional) if we want to filter the results.
    * @param array|null $filterValue (optional) the values of the filter
    * @return array|bool|null
    * @throws Exception
    */
    public static function toList($filter=PdoOne::NULL,?array $filterValue=null) {
        if(self::$useModel) {
            return false; // no model set
        }
        return self::_toList($filter, $filterValue);
    }

    /**
    * It sets the recursivity. By default, if we query or modify a value, it operates with the fields of the entity.
    * With recursivity, we could use the recursivity of the fields, for example, loading a MANYTOONE relation<br>
    * <b>Example:</b><br>
    * <pre>
    * self::recursive([]); // (default) no use recursivity.
    * self::recursive('*'); // recursive every MANYTOONE,ONETOONE,MANYTOONE and ONETOONE relations (first level)
    * self::recursive('MANYTOONE'); // recursive all relations of the type MANYTOONE (first level)
    * self::recursive(['/_relation1','/_relation2']); // recursive only the relations of the first level
    * self::recursive(['/_relation1','/_relation1/_subrelation1']); //recursive the relations (first and second level)
    * self::recursive(['/_manytomany*'); // the postfix "*" indicates: (only in a many-to-many relation)
    *      // "*": in the case of insert, update or merge, the relational table,left and right table would be modified.
    *      // "" : in the case of insert, update or merge, only the relational table and left would be modified.
    * </pre>
    * If array then it uses the values to set the recursivity.<br>
    * If string then the values allowed are '*', 'MANYTOONE','ONETOMANY','MANYTOMANY','ONETOONE' (first level only)<br>
    * If you don't want to do multiple modifications (insert,update or delete), then simply you can skip this operator.
    *
    * @param string|array $recursive=['/_film_actor'];
    *
    * @return PdoOneQuery
    */
    public static function recursive($recursive=[]): PdoOneQuery
    {
        if(is_string($recursive)) {
            $recursive=ActorRepo::getRelations($recursive);
        }
        return parent::_recursive($recursive);
    }

    /**
    * It adds a "limit" in a query. It depends on the type of database<br>
    * <b>Example:</b><br>
    * <pre>
    *      self::limit("10,20")->toList(); // start row 10th, fetches 20 next 20 rows
    *      self::limit(10,20)->toList();   // start row 10th, fetches 20 next 20 rows
    * </pre>
    *
    * @param mixed $first the first (initial) value or the sql query expression
    * @param mixed|null $second the values to fetches.
    *
    * @return PdoOneQuery
    * @throws Exception
    * @test InstanceOf PdoOne::class,this('1,10')
    */
    public static function limit($first,$second=null) : PdoOneQuery
    {
        return static::newQuery()->limit($first,$second);
    }

    /**
    * It returns the first row of a query.<br>
    * <b>Example:</b><br>
    * <pre>
         * Repo::first(); // it returns the first value encountered.
         * Repo::first(2); // it returns the first value where the primary key is equals to 2 (simple primary key)
         * Repo::first([2,3]); // it returns the first value where the primary key is equals to 2 (multiple primary keys)
         * Repo::first(['id'=>2,'id2'=>3]); // it returns the first value where id=2 and id2=3 (multiple primary keys)
         * </pre>
    * @param array|mixed|null $pk [optional] Specify the value of the primary key.
    *
    * @return array|bool It returns false if not file is found.
    * @throws Exception
    */
    public static function first($pk = PdoOne::NULL,?PdoOneQuery $query=null) {
        if(self::$useModel) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return false; // no model set
        }
        return self::_first($pk,$query);
    }

    /**
    *  It returns true if the entity exists, otherwise false.<br>
    *  <b>Example:</b><br>
    *  <pre>
         *  $this->exist(['id'=>'a1','name'=>'name']); // using an array
         *  $this->exist('a1'); // using the primary key. The table needs a pks, and it only works with the first pk.
         *  </pre>
    *
    * @param array|mixed $entity =self::factoryUtil()
    *
    * @return bool true if the pks exists
    * @throws Exception
    */
    public static function exist($entity): bool {
        return self::_exist($entity);
    }

    /**
    * It inserts a new entity(row) into the database<br>
    * It returns false if the operation failed, otherwise, it returns the entity modified (if it has, for example, an
    * identity field<br>
    * @param array|object $entity        =self::factoryUtil()
    * @param bool         $transactional If true (default) then the operation is transactional
    *
    * @return array|false=self::factoryUtil()
    * @throws Exception
    */
    public static function insert(&$entity,bool $transactional=true) {
        return self::_insert($entity,$transactional,true);
    }

    /**
    * It merges a new entity(row) into the database. If the entity exists then it is updated, otherwise the entity is
    * inserted<br>
    * @param array|object $entity        =self::factoryUtil()
    * @param bool         $transactional If true (default) then the operation is transactional
    *
    * @return array|false=self::factoryUtil()
    * @throws Exception
    */
    public static function merge(&$entity,bool $transactional=true) {
        return self::_merge($entity,$transactional);
    }

    /**
     * Updates an entity. It uses the primary key to determine the row to modify.<br>
     * If <b>$entity</b> has a missing field, then the missing field will not be updated.<br>
     * If <b>$entity</b> has an extra field (a field not defined in the table), then it will throw an error<br>
     * <b>Example:</b><br>
     * <pre>
     * $result=$this::factory();
     * $result['name']='name changed';
     * $ok=$this::update($customer);
     * </pre>
     * @param array|object $entity        =self::factoryUtil()
     * @param bool         $transactional If true (default) then the operation is transactional<br>
     *                                    If false, then it allows to create your own transaction.
     * @return false|int=self::factoryUtil()
     * @throws Exception
     */
    public static function update($entity,bool $transactional=true) {
        return self::_update($entity,$transactional);
    }

    /**
     * It deletes an entity using the fields of the entity as conditions
     *
     * @param array|object $entity =self::factoryUtil()
     * @param bool         $transactional If true (default) then the operation is transactional
     *
     * @return false|int
     * @throws Exception
     */
    public static function delete($entity,bool $transactional=true) {
        return self::_delete($entity,$transactional);
    }

    /**
    * It deletes an entity by the primary key.
    *
    * @param array|mixed $pk =self::factoryUtil()
    * @param bool        $transactional If true (default) then the operation is transactional
    *
    * @return int|false
    * @throws Exception
    */
    public static function deleteById($pk,bool $transactional=true) {
        return self::_deleteById($pk,$transactional);
    }

    /**
     * Returns an array with the default structure of an entity.
     *
     * @param array|null $values          =self::factoryUtil()
     * @param string     $recursivePrefix It is the prefix of the recursivity.
     *
     * @return array
     */
    public static function factory(?array $values = null,string $recursivePrefix = ''): array {
        $recursive=static::getRecursive();
        static::recursive(); // reset the recursivity.
        $row= [
		    'actor_id' => null,
		    'first_name' => null,
		    'last_name' => null,
		    'last_update' => null,
		    '_film_actor' => []
		];

        if ($values !== null) {
            $row = array_merge($row, $values);
        }
        return $row;
    }

    /**
     * An associative array used to define the full structure of an entity.<br>
     * It is only used for autocomplete because it is prone to circular reference.
     *
     * @return array
     */
    public static function factoryUtil():array {
        return [
		    'actor_id' => null,
		    'first_name' => null,
		    'last_name' => null,
		    'last_update' => null,
		    '_film_actor' => [FilmActorRepo::factoryUtil()]
		];
    }

    // [EDIT:content] you can edit this part
    // Here you can add your custom content.
    // [/EDIT] end of edit
}

