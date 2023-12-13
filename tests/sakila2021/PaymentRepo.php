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
 * Class PaymentRepo Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * <ul>
 * <li><b>payment_id</b>: int (alias of column payment_id) </li>
 * <li><b>customer_id</b>: int (alias of column customer_id) </li>
 * <li><b>staff_id</b>: int (alias of column staff_id) </li>
 * <li><b>rental_id</b>: int (alias of column rental_id) </li>
 * <li><b>amount</b>: float (alias of column amount) </li>
 * <li><b>payment_date</b>: datetime (alias of column payment_date) </li>
 * <li><b>last_update</b>: datetime (alias of column last_update) </li>
 * <li><b>_customer_id</b>: MANYTOONE (alias of column _customer_id) (CustomerRepoModel)</li>
 * <li><b>_rental_id</b>: MANYTOONE (alias of column _rental_id) (RentalRepoModel)</li>
 * <li><b>_staff_id</b>: MANYTOONE (alias of column _staff_id) (StaffRepoModel)</li>
 * </ul>
 * Generated by PdoOne Version 1.4 Date generated Tue, 12 Dec 2023 19:40:55 -0300.<br>
 * <b>YOU CAN EDIT THIS CODE</b>. It is not replaced by the generation of the code, unless it is indicated<br>
 * @see CustomerRepoModel
 * @see RentalRepoModel
 * @see StaffRepoModel
 */
class PaymentRepo extends AbstractPaymentRepo
{
    public static $ME=__CLASS__;
    
    // [EDIT:content] you can edit this part
    // Here you can add your custom content.
    // [/EDIT] end of edit
}
