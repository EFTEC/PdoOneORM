<?php
namespace eftec\tests;

use eftec\CliOne\CliOne;
use eftec\PdoOneORMCli;
use PHPUnit\Framework\TestCase;


class Cli_Test extends TestCase
{
    public $cli;
    public function setUp(): void
    {
        // Note, it is an integration test, it requires mysql, user: root, password: abc.123 and database called sakila.

        parent::setUp();


    }
    /**
     * @covers:\eftec\PdoOneORMCli
     */
    public function testrun():void {
        // it is because phpunit could run elsewhere
        chdir(__DIR__);
        //var_dump(__DIR__);
        CliOne::testUserInput(null);
        CliOne::testUserInput(['connect','configure','mysql','','root','abc.123','sakila','','repo'
            ,'folder','repo','Repo','eftec\\tests\\repo'
            ,'scan'
            ,'save','test','','']);
        $this->cli=new PdoOneORMCli();
        $this->assertFileExists(__DIR__.'/test.config.php');
        unlink(__DIR__.'/test.config.php');
        $this->assertEquals(true,true); // if reach there then it succeeded
    }

}
