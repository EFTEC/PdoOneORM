<?php
/** @noinspection AccessModifierPresentedInspection
 * @noinspection PhpUnusedAliasInspection
 * @noinspection UnknownInspectionInspection
 * @noinspection PhpUnused
 */
namespace eftec\tests\sakila2021;

use Exception;
// [EDIT:use] you can edit this part
// Here you can add your custom use
// [/EDIT] end of edit

/**
 * Class InventoryRepo Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * <ul>
 * <li><b>inventory_id</b>: int (alias of column inventory_id) </li>
 * <li><b>film_id</b>: int (alias of column film_id) </li>
 * <li><b>store_id</b>: int (alias of column store_id) </li>
 * <li><b>last_update</b>: datetime (alias of column last_update) </li>
 * <li><b>_film_id</b>: MANYTOONE (alias of column _film_id) (FilmRepoModel)</li>
 * <li><b>_store_id</b>: MANYTOONE (alias of column _store_id) (StoreRepoModel)</li>
 * <li><b>_rental</b>: ONETOMANY (alias of column _rental) (RentalRepoModel)</li>
 * </ul>
 * Generated by PdoOne Version 1.4 Date generated Tue, 12 Dec 2023 19:40:55 -0300.<br>
 * <b>YOU CAN EDIT THIS CODE</b>. It is not replaced by the generation of the code, unless it is indicated<br>
 * @see FilmRepoModel
 * @see StoreRepoModel
 * @see RentalRepoModel
 */
class InventoryRepo extends AbstractInventoryRepo
{
    public static $ME=__CLASS__;
    
    // [EDIT:content] you can edit this part
    // Here you can add your custom content.
    // [/EDIT] end of edit
}
