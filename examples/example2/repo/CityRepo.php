<?php
/** @noinspection AccessModifierPresentedInspection
 * @noinspection PhpUnusedAliasInspection
 * @noinspection UnknownInspectionInspection
 * @noinspection PhpUnused
 */
namespace eftec\examples\example2\repo;

use Exception;
// [EDIT:use] you can edit this part
// Here you can add your custom use
// [/EDIT] end of edit

/**
 * Class CityRepo Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * <ul>
 * <li><b>city_id</b>: int (alias of column city_id) </li>
 * <li><b>city</b>: string (alias of column city) </li>
 * <li><b>country_id</b>: int (alias of column country_id) </li>
 * <li><b>last_update</b>: datetime (alias of column last_update) </li>
 * <li><b>_country_id</b>: MANYTOONE (alias of column _country_id) (CountryRepoModel)</li>
 * <li><b>_address</b>: ONETOMANY (alias of column _address) (AddresRepoModel)</li>
 * </ul>
 * Generated by PdoOne Version 1.00 Date generated Sat, 11 Mar 2023 18:09:05 -0300.<br>
 * <b>YOU CAN EDIT THIS CODE</b>. It is not replaced by the generation of the code, unless it is indicated<br>
 * @see CountryRepoModel
 * @see AddresRepoModel
 */
class CityRepo extends AbstractCityRepo
{
    const ME=__CLASS__;
    
    // [EDIT:content] you can edit this part
    // Here you can add your custom content.
    // [/EDIT] end of edit
}