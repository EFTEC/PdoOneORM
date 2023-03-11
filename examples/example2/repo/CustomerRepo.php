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
 * Class CustomerRepo Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * <ul>
 * <li><b>customer_id</b>: int (alias of column customer_id) </li>
 * <li><b>store_id</b>: int (alias of column store_id) </li>
 * <li><b>first_name</b>: string (alias of column first_name) </li>
 * <li><b>last_name</b>: string (alias of column last_name) </li>
 * <li><b>email</b>: string (alias of column email) </li>
 * <li><b>address_id</b>: int (alias of column address_id) </li>
 * <li><b>active</b>: string (alias of column active) </li>
 * <li><b>create_date</b>: datetime (alias of column create_date) </li>
 * <li><b>last_update</b>: datetime (alias of column last_update) </li>
 * <li><b>_address_id</b>: MANYTOONE (alias of column _address_id) (AddresRepoModel)</li>
 * <li><b>_store_id</b>: MANYTOONE (alias of column _store_id) (StoreRepoModel)</li>
 * <li><b>_payment</b>: ONETOMANY (alias of column _payment) (PaymentRepoModel)</li>
 * <li><b>_rental</b>: ONETOMANY (alias of column _rental) (RentalRepoModel)</li>
 * </ul>
 * Generated by PdoOne Version 1.00 Date generated Sat, 11 Mar 2023 18:09:05 -0300.<br>
 * <b>YOU CAN EDIT THIS CODE</b>. It is not replaced by the generation of the code, unless it is indicated<br>
 * @see AddresRepoModel
 * @see StoreRepoModel
 * @see PaymentRepoModel
 * @see RentalRepoModel
 */
class CustomerRepo extends AbstractCustomerRepo
{
    const ME=__CLASS__;
    
    // [EDIT:content] you can edit this part
    // Here you can add your custom content.
    // [/EDIT] end of edit
}