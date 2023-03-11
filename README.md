# Database Access Object wrapper for PHP and PDO in a single class

PdoOne. It's a simple wrapper for PHP's PDO library compatible with SQL Server (2008 R2 or higher), MySQL (5.7 or higher) and Oracle (12.1 or higher).

This library tries to **work as fast as possible**. Most of the operations are simple string/array managements and work in the bare metal of the PDO library, but it also allows to create an ORM.

[![Packagist](https://img.shields.io/packagist/v/eftec/PdoOneORM.svg)](https://packagist.org/packages/eftec/PdoOneORM)
[![Total Downloads](https://poser.pugx.org/eftec/PdoOne/downloads)](https://packagist.org/packages/eftec/PdoOneORM)
[![Maintenance](https://img.shields.io/maintenance/yes/2023.svg)]()
[![composer](https://img.shields.io/badge/composer-%3E1.6-blue.svg)]()
[![php](https://img.shields.io/badge/php-7.1-green.svg)]()
[![php](https://img.shields.io/badge/php-8.x-green.svg)]()
[![CocoaPods](https://img.shields.io/badge/docs-70%25-yellow.svg)]()

Turn this

```php
$stmt = $pdo->prepare("SELECT * FROM myTable WHERE name = ?");
$stmt->bindParam(1,$_POST['name'],PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->get_result();
$products=[];
while($row = $result->fetch_assoc()) {
  $product[]=$row; 
}
$stmt->close();
```

into this using the ORM.

```php
ProductRepo // this class was generated with echo $pdoOne()->generateCodeClass(['Product']); or using the cli.
    ::where("name = ?",[$_POST['name']])
    ::toList();
```

# Table of contents

<!-- TOC -->
* [Database Access Object wrapper for PHP and PDO in a single class](#database-access-object-wrapper-for-php-and-pdo-in-a-single-class)
* [Table of contents](#table-of-contents)
  * [Installation](#installation)
    * [Install (using composer)](#install--using-composer-)
    * [Install (manually)](#install--manually-)
  * [How to create a Connection?](#how-to-create-a-connection)
    * [OCI](#oci)
  * [ORM](#orm)
      * [What is an ORM?](#what-is-an-orm)
    * [Building and installing the ORM](#building-and-installing-the-orm)
      * [Creating the repository class](#creating-the-repository-class)
      * [Creating multiples repositories classes](#creating-multiples-repositories-classes)
      * [Creating all repositories classes](#creating-all-repositories-classes)
    * [Using the Repository class.](#using-the-repository-class)
      * [Using multiples connections](#using-multiples-connections)
    * [DDL  Database Design Language](#ddl--database-design-language)
    * [Nested Operators](#nested-operators)
    * [DQL Database Query Language](#dql-database-query-language)
    * [DML Database Model Language](#dml-database-model-language)
    * [Validate the model](#validate-the-model)
    * [Recursive](#recursive)
      * [recursive()](#recursive--)
      * [getRecursive()](#getrecursive--)
      * [hasRecursive()](#hasrecursive--)
  * [Benchmark (mysql, estimated)](#benchmark--mysql-estimated-)
  * [Error FAQs](#error-faqs)
    * [Uncaught Error: Undefined constant eftec\_BasePdoOneRepo::COMPILEDVERSION](#uncaught-error--undefined-constant-eftecbasepdoonerepo---compiledversion)
  * [Changelist](#changelist)
  * [License](#license)
<!-- TOC -->

## Installation

This library requires PHP 7.1 and higher, and it requires the extension PDO and the extension PDO-MYSQL (Mysql), PDO-SQLSRV (sql server) or PDO-OCI (Oracle)

### Install (using composer)

Edit  **composer.json** the next requirement, then update composer.

```json
  {
      "require": {
        "eftec/PdoOneORM": "^1.0"
      }
  }
```
or install it via cli using

> composer require eftec/PdoOneORM

### Install (manually)

Just download the folder lib from the library and put in your folder project.  Then you must include all the files included on it.

## How to create a Connection?

Create an instance of the class PdoOne as follows. Then, you can open the connection using the method connect() or open()

```php
use eftec\PdoOneORM;
// mysql
$dao=new PdoOneORM("mysql","127.0.0.1","root","abc.123","sakila","");
$conn->logLevel=3; // it is for debug purpose and it works to find problems.
$dao->connect();

// sql server 10.0.0.1\instance or (local)\instance or machinename\instance or machine (default instance)
$dao=new PdoOneORM("sqlsrv","(local)\sqlexpress","sa","abc.123","sakila","");
$conn->logLevel=3; // it is for debug purpose and it works to find problems.
$dao->connect();

// test (mockup)
$dao=new PdoOneORM("test","anyy","any","any","any","");
$dao->connect();

// oci (oracle) ez-connect. Remember that you must have installed Oracle Instant client and added to the path.

$cs='(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))(CONNECT_DATA =(SERVER = DEDICATED)(SERVICE_NAME = instancia1)))';
$dao=new PdoOneORM("oci",$cs,"sa","abc.123"); // oracle uses the user as the schema
$conn->logLevel=3; // it is for debug purpose and it works to find problems.
$dao->connect();

// oci (oracle) tsnnames (the environment variables TNS_ADMIN and PATH must be correctly configured), also tnsnames.ora must exists.
$cs='instancia1';
$dao=new PdoOneORM("oci",$cs,"sa","abc.123"); // oracle uses the user as the schema
$conn->logLevel=3; // it is for debug purpose and it works to find problems.
$dao->connect();

```

where

> $dao=new PdoOneORM("mysql","127.0.0.1","root","abc.123","sakila","");

* "**mysql**" is the MySQL database. It also allows sqlsrv (for sql server) or "oci" (oracle)
* **127.0.0.1** is the server where is the database.
* **root** is the user
* **abc.123** is the password of the user root.
* **sakila** is the database used.
* "" (optional) it could be a log file, such as c:\temp\log.txt

### OCI

* Windows installation. Add the Oracle Instant client to the path and try to run from it.
  * If it fails, the copy the oracle bin folder (instant client) into the apache folder. 

## ORM

This library allows creating and use it as an ORM. To use it as an ORM, you must create the classes.

#### What is an ORM?

An ORM transforms queries to the database in objects serializables.

Let's say the next example

```php
$result=$pdoOne->runRawQuery('select IdCustomer,Name from Customers where IdCustomer=?',1); 
```

You can also run using the Query Builder

```php
$result=$pdoOne->select('IdCustomer,Name')->from('Customers')->where('IdCustomer=?',1)->toList();
```

What if you use the same table over and over.  You can generate a new class called **CustomerRepo** and calls the code as

```php
$result=CustomerRepo::where('IdCustomer=?',1)::toList();
```

While it is simple, but it also hides part of the implementation.  It could hurt the performance a bit, but it adds more simplicity and consistency.



### Building and installing the ORM

There are several ways to generate a Repository code, it is possible to generate a code using the CLI, the GUI or using the next code:

```php
$pdo=new PdoOneORM('mysql','127.0.0.1','root','abc.123','sakila');
$pdo->connect();
$table=$pdo->generateCodeClass('Tablename','repo'); // where Tablename is the name of the table to analyze. it must exsits.
echo $clase;
```

The code generated looks like this one

```php
class TableNameRepo extends _BasePdoOneRepo
{
// ....
}
```

#### Creating the repository class

> This method is not recommended. Uses the method to create multiple classes.

There are several ways to create a class, you could use the UI, the CLI or directly via code.

It is an example to create our repository class

```php
$class = $pdoOne->generateCodeClass('Customer'); // The table Customer must exists in the database
file_put_contents('CustomerRepo.php',$clase); // and we write the code into a file.
```

It will build our Repository class.

```php
<?php
use eftec\PdoOneORM;
use eftec\_BasePdoOneRepo;

class CustomerRepo extends _BasePdoOneRepo
{
    //....
}
```

```php
$class = $pdoOne->generateCodeClass('Customer','namespace\anothernamespace'); 
```

It will generate the next class

```php
<?php
namespace namespace\anothernamespace;    
use eftec\PdoOneORM;
use eftec\_BasePdoOneRepo;

class CustomerRepo extends _BasePdoOneRepo
{
    //....
}
```

#### Creating multiples repositories classes

In this example, we have two classes, messages and users

```php
// converts all datetime columns into a ISO date.
$pdoOne->generateCodeClassConversions(['datetime'=>'datetime2']);

$errors=$pdoOneORM->generateAllClasses(
    ['messages'=>'MessageRepo','users'=>'UserRepo'] // the tables and their name of classes
    ,'BaseClass' // a base class.
    ,'namespace1\repo' // the namespaces that we will use
    ,'/folder/repo' // the folders where we store our classes
    ,false // [optional] if true the we also replace the Repo classes
    ,[] // [optional] Here we could add a custom relation of conversion per column.
    ,[] // [optional] Extra columns. We could add extra columns to our repo.
    ,[] // [optional] Columns to exclude.    
);
var_dump($errors); // it shows any error or an empty array if success.
```

It will generate the next classes:

```
📁 folder
   📁repo
       📃AbstractMessageRepo.php   [namespace1\repo\AbstractMessageRepo] NOT EDIT OR REFERENCE THIS FILE
       📃MessageRepo.php         [namespace1\repo\MessageRepo] EDITABLE
       📃AbstractUserRepo.php     [namespace1\repo\AbstractUserRepo] NOT EDIT OR REFERENCE THIS FILE
       📃UserRepo.php               [namespace1\repo\UserRepo]  EDITABLE
       📃BaseClass.php              [namespace1\repo\BaseClass] NOT EDIT OR REFERENCE THIS FILE      
```

* Abstract Classes are classes with all the definitions of the tables, indexes and such. They contain the whole definition of a class.
  * This class should be rebuilded if the table changes. How? You must run the method **generateAllClasses**() again.
* Repo Classes are classes that works as a placeholder of the Abstract class. These classes are safe for edit, so we could add our own methods and logic.
  * Note: if you run **generateAllClasses**() again, then those classes are not touched unless we force it (argument **$forced**) or we delete those files.
* Base Class is a unique class (per schema) where it contains the definition of all the tables and the relations between them.
  * This class should be rebuilt if the table changes. How? You must run the method **generateAllClasses**() again.

#### Creating all repositories classes

We could automate even further

```php
$allTablesTmp=$pdoOne->objectList('table',true); // we get all the tables from the current schema.
$allTables=[];
foreach($allTablesTmp as $table) {
    $allTables[$table]=ucfirst($table).'Repo';
}
$errors=$pdoOne->generateAllClasses(
   $allTables // tables=>repo class
    ,'MiniChat' // a base class.
    ,'eftec\minichat\repo' // the namespaces that we will use
    ,'/folder/repo' // the folders where we store our classes
);
echo "Errors (empty if it is ok:)";
var_dump($errors);
```



### Using the Repository class.

For started, the library must know to know where to connect, so you must set an instance of the PdoOne and there are 3 ways to instance it.

The repository class is smart, and it does the next operation:

> If the Repository base doesn't have a connection, then it will try to use the latest connection available.


The easiest way is to create an instance of PdoOne();

```php
$pdo=new PdoOneORM('mysql','127.0.0.1','root','abc.123','sakila');
$pdo->connect();
$listing=TableNameRepo::toList(); // it will inject automatically into the Repository base class, instance of PdoOneORM.
```

You could also do it by creating a root function called **pdoOneORM()**

```php
function pdoOneORM() {
   $pdo=new PdoOneORM('mysql','127.0.0.1','root','abc.123','sakila');
   $pdo->connect();
}
```

Or creating a global variable called **$pdoOne**

```php
$pdoOne=new PdoOneORM('mysql','127.0.0.1','root','abc.123','sakila');
$pdoOne->connect();
```

Or injecting the instance into the class using the static method **Class::setPdoOne()**

```php
$pdo=new PdoOneORM('mysql','127.0.0.1','root','abc.123','sakila');
$pdo->connect();
TableNameRepo::setPdoOne($pdo); // TableNameRepo is our class generated. You must inject it once per all schema.
```

#### Using multiples connections

Note: If you are using multiples connections, then you must use the method **RepoClass::setPdoOne()** and it injects the connection inside the Repository Base. 

Every repository base could hold only one connection at the same time

Example:

* BaseAlpha (Base class)
  * Table1AlphaRepo (Repository class)
  * Table2AlphaRepo (Repository class)
* BaseBeta (Base class)
  * Table1BetaRepo (Repository class)
  * Table2BetaRepo (Repository class)

```php
$con1=new PdoOneORM('mysql','127.0.0.1','root','abc.123','basealpha');
$con1->connect();
$con2=new PdoOneORM('mysql','127.0.0.1','root','abc.123','basebeta');
$con2->connect();
// every base with its own connection:
Table1AlphaRepo::setPdoOne($pdo); // ✅ Table1AlphaRepo and Table2AlphaRepo will use basealpha
Table1BetaRepo::setPdoOne($pdo); // ✅ Table1BetaRepo and Table2BetaRepo will use basebeta
// however, it will not work as expected
// every base with its own connection:
Table1AlphaRepo::setPdoOne($pdo); // ✅ Table1AlphaRepo and Table2AlphaRepo will use basealpha
Table2AlphaRepo::setPdoOne($pdo); // ❌ And now, Table1AlphaRepo and Table2AlphaRepo will use basebeta
```

What if you want to use the same base for different connections? You can't. However, you could copy the files and create two different bases and repositories (or you could generate a code to create a new base and repository classes), then you can use multiples connections.



### DDL  Database Design Language

The next commands usually are executed alone (not in a chain of methods)

| Method              | Description                                                        | Example                               |
|---------------------|--------------------------------------------------------------------|---------------------------------------|
| createTable()       | Creates the table and indexes using the definition inside the Repo | TablaParentRepo::createTable();       |
| createForeignKeys() | Create all foreign keys of the table                               | TablaParentRepo::createForeignKeys(); |
| dropTable()         | Drop the table                                                     | TablaParentRepo::dropTable();         |
| truncate()          | Truncate the table                                                 | TablaParentRepo::truncate();          |
| validTable()        | Validate if the table hasn't changed                               | $ok=TablaParentRepo::validTable();    |

```php
TablaParentRepo::createTable();
TablaParentRepo::createForeignKeys();
TablaParentRepo::dropTable();
TablaParentRepo::truncate();
// We don't have a method to alter a table.
$ok=TablaParentRepo::validTable(); // it returns true if the table matches with the definition stored into the clas
```

### Nested Operators

The nested operators are methods that should be in between of our chain of methods.

> ClassRepo::op()::where()::finalop() is ✅
>
> ClassRepo::op()::op()::where() will left the chain open ❌

For example:

```php
// select * 
//        from table 
//        inner join table2 on t1=t2 
//        where col=:arg
//        and col2=:arg2
//    group by col
//        having col3=:arg3
//    order by col
//    limit 20,30
$results=$pdo->select('*')
    ->from('table')
    ->innerjoin('table2 on t1=t2')
    ->where('col=:arg and col2:=arg2',[20,30]) 
    // it also works with ->where('col=:arg',20)->where('col2'=>30)
    // it also works with ->where('col=?',20)->where('col2=?'=>30)
    ->group('col')
    ->having('col3=:arg3',400)
    ->order('col')
    ->limit('20,30')
    ->toList(); // end of the chain

```



| Method      | Description                           | Example                      |
|-------------|---------------------------------------|------------------------------|
| where()     | It adds a where to the chain          | TablaParentRepo::where()     |
| order()     | It adds a order by to the chain       | TablaParentRepo::order()     |
| group()     | it adds a group by to the chain       | TablaParentRepo::group()     |
| limit()     | It limits the results                 | TablaParentRepo::limit()     |
| page()      | Its similar to limit but it uses page | TablaParentRepo::page()      |
| innerjoin() | It adds a inner join to the query     | TablaParentRepo::innerjoin() |
| left()      | It adds a left join to the query      | TablaParentRepo::left()      |
| right()     | It adds a right join to the query     | TablaParentRepo::right()     |



### DQL Database Query Language

We have different methods to generate a DQL (query) command in our database.

> If the operation fails, they return a FALSE, and they could trigger an exception.
>
> The next methods should be at the end of the chain.  Examples:
>
> ClassRepo::op()::op()::toList() is ✅
>
> ClassRepo::op()::toList()::op() will trigger an exception ❌

| Command  | Description                           | Example                                                                                                                                                                                                                         |
|----------|---------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| toList() | Returns an array of elements          | $data=TableNameRepo::toList(); // select * from tablerepo<br />$data=TableNameRepo::where('a1=?',[$value])::toList(); // select * from tablerepo where a1=$value                                                                |
| first()  | Returns a simple row                  | $data=TableNameRepo::first($pk); // select * from tablerepo where pk=$pk  (it always returns 1 or zero values)<br />$data=TableNameRepo::where('a1=?',[$value])::first(); // it returns the first value (or false if not found) |
| exist()  | Returns true if a primary key exists  | $data=TableNameRepo::exist($pk); // returns true if the object exists.                                                                                                                                                          |
| count()  | Returns the number of rows in a query | $data=TableNameRepo::count($conditions); <br />$data=TableNameRepo::where('a1=?',[$value])::count();                                                                                                                            |

### DML Database Model Language

The next methods allow inserting,update or delete values in the database.

| Method     | Description                                                                | Example                                  |
|------------|----------------------------------------------------------------------------|------------------------------------------|
| insert     | It inserts a value into the database. It could return an identity          | $identity=TablaParentRepo::insert($obj); |
| update     | It updates a value into the database.                                      | TablaParentRepo::update($obj);           |
| delete     | It deletes a value from the database.                                      | TablaParentRepo::delete($obj);           |
| deletebyId | It deletes a value (using the primary key as condition) from the database. | TablaParentRepo::deleteById($pk);        |



```php
// where obj is an associative array or an object, where the keys are the name of the columns (case sensitive)
$identity=TablaParentRepo::insert($obj); 
TablaParentRepo::update($obj);
TablaParentRepo::delete($obj);
TablaParentRepo::deleteById(id);
```



### Validate the model

It is possible to validate the model. The model is validated using the information of the database, using the type of the column, the length, if the value allows null and if it is identity (auto numeric).

```php
$obj=['IdUser'=>1,'Name'='John Doe']; 
UserRepo::validateModel($obj,false,['_messages']); // returns true if $obj is a valid User.
```

### Recursive

A recursive array is an array of  strings with values that it could be read or obtained or compared.  For example, to join a table conditionally.
PdoOne does not use it directly but _BasePdoOneRepo uses it (_BasePdoOneRepo is a class used when we generate a repository service class automatically).

Example

```php
$this->select('*')->from('table')->recursive(['table1','table1.table2']);
// some operations that involves recursive
if($this->hasRecursive('table1')) {
    $this->innerJoin('table1 on table.c=table1.c');
}
if($this->hasRecursive('table1.table2')) {
    $this->innerJoin('table1 on table1.c=table2.c');
}
$r=$this->toList(); // recursive is resetted.
```

#### recursive()

It sets a recursive array.

> This value is resets each time a chain methods ends.

#### getRecursive()

It gets the recursive array.

#### hasRecursive()

It returns true if recursive has some needle.

If $this->recursive is ['*'] then it always returns true.

```php
$this->select('*')->from('table')->recursive(['*']);
$this->hasRecursive('anything'); // it always returns true.
```


## Benchmark (mysql, estimated)

| Library                 | Insert | findPk | hydrate | with | time   |
|-------------------------|--------|--------|---------|------|--------|
| PDO                     | 671    | 60     | 278     | 887  | 3,74   |
| **PdoOne**              | 774    | 63     | 292     | 903  | 4,73   |
| LessQL                  | 1413   | 133    | 539     | 825  | 5,984  |
| YiiM                    | 2260   | 127    | 446     | 1516 | 8,415  |
| YiiMWithCache           | 1925   | 122    | 421     | 1547 | 7,854  |
| Yii2M                   | 4344   | 208    | 632     | 1165 | 11,968 |
| Yii2MArrayHydrate       | 4114   | 213    | 531     | 1073 | 11,22  |
| Yii2MScalarHydrate      | 4150   | 198    | 421     | 516  | 9,537  |
| Propel20                | 2507   | 123    | 1373    | 1960 | 11,781 |
| Propel20WithCache       | 1519   | 68     | 1045    | 1454 | 8,228  |
| Propel20FormatOnDemand  | 1501   | 72     | 994     | 1423 | 8,228  |
| DoctrineM               | 2119   | 250    | 1592    | 1258 | 18,139 |
| DoctrineMWithCache      | 2084   | 243    | 1634    | 1155 | 17,952 |
| DoctrineMArrayHydrate   | 2137   | 240    | 1230    | 877  | 16,83  |
| DoctrineMScalarHydrate  | 2084   | 392    | 1542    | 939  | 18,887 |
| DoctrineMWithoutProxies | 2119   | 252    | 1432    | 1960 | 19,822 |
| Eloquent                | 3691   | 228    | 708     | 1413 | 12,155 |

PdoOne adds a bit of ovehead over PDO, however it is simple a wrapper to pdo.


## Error FAQs

### Uncaught Error: Undefined constant eftec\_BasePdoOneRepo::COMPILEDVERSION

It means that you are updated PdoOne, and you are using one class generated by the ORM. This class must be re-generated.



## Changelist

In a nutshell:

> Every major version means that it could break old code. I.e. 1.0 -> 2.0
>
> Every minor version means that it adds a new functionality i.e. 1.5 -> 1.6 (new methods)
>
> Every decimal version means that it patches/fixes/refactoring a previous functionality i.e. 1.5.0 -> 1.5.1 (fix)

* 1.0 2023-03-11
  * First version. This version is split from library PdoOne. 


## License

Copyright Jorge Castro Castillo 2023. Dual license, commercial and LGPL-3.0
