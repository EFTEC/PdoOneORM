<?php http_response_code(404); die(1); // It is a template file, not a code to execute directly. This line is used to avoid to execute or read it. ?>
/** @noinspection PhpIncompatibleReturnTypeInspection
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
namespace {namespace};
use eftec\PdoOne;
{exception}
// [EDIT:use] you can edit this part
// Here you can add your custom use
// [/EDIT] end of edit

/**
 * Class {classname}. Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * Generated by PdoOne Version {version}.<br>
 * <b>DO NOT EDIT THE CODE OUTSIDE EDIT BLOCKS</b>. This code is generated<br>
 * <pre>
 * $code=$pdoOne->generateAbstractModelClass({args});
 * </pre>
 */
abstract class Abstract{classname}
{
{fields}

{fieldsrel}


    /**
     * Abstract{classname} constructor.
     *
     * @param array|null $array
     */
    public function __construct($array=null)
    {
        if($array===null) {
            return;
        }
        foreach($array as $k=>$v) {
            $this->{$k}=$v;
        }
    }

    //<editor-fold desc="array conversion">
    public static function fromArray($array) {
        if($array===null) {
            return null;
        }
        $obj=new {classname}();
        {fieldsfa}
        {fieldsrelfa}

        return $obj;
    }

    /**
     * It converts the current object in an array
     *
     * @return mixed
     */
    public function toArray() {
        return static::objectToArray($this);
    }

    /**
     * It converts an array of arrays into an array of objects.
     *
     * @param array|null $array
     *
     * @return array|null
     */
    public static function fromArrayMultiple($array) {
        if($array===null) {
            return null;
        }
        $objs=[];
        foreach($array as $v) {
            $objs[]=self::fromArray($v);
        }
        return $objs;
    }
    //</editor-fold>
    // [EDIT:content] you can edit this part
    // Here you can add your custom content.
    // [/EDIT] end of edit
} // end class
