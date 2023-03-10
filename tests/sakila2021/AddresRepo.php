<?php
/** @noinspection AccessModifierPresentedInspection
 * @noinspection PhpUnusedAliasInspection
 * @noinspection UnknownInspectionInspection
 * @noinspection PhpUnused
 * @noinspection ReturnTypeCanBeDeclaredInspection
 */
namespace eftec\tests\sakila2021;

// [EDIT:use] you can edit this part
// Here you can add your custom use
// [/EDIT] end of edit

/**
 * Class AddresRepo Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * <ul>
 * <li>address_id: int (alias of column address_id) </li>
 * <li>address: string (alias of column address) </li>
 * <li>address2: string (alias of column address2) </li>
 * <li>district: string (alias of column district) </li>
 * <li>city_id: int (alias of column city_id) </li>
 * <li>postal_code: string (alias of column postal_code) </li>
 * <li>phone: string (alias of column phone) </li>
 * <li>last_update: datetime (alias of column last_update) </li>
 * <li>_city_id: MANYTOONE (alias of column _city_id) (CityRepoModel)</li>
 * <li>_customer: ONETOMANY (alias of column _customer) (CustomerRepoModel)</li>
 * <li>_staff: ONETOMANY (alias of column _staff) (StaffRepoModel)</li>
 * <li>_store: ONETOMANY (alias of column _store) (StoreRepoModel)</li>
 * </ul>
 * Generated by PdoOne Version 3.15 Date generated Sat, 11 Feb 2023 18:40:05 -0300.<br>
 * <b>YOU CAN EDIT THIS CODE</b>. It is not replaced by the generation of the code, unless it is indicated<br>
 * <pre>
 * $code=$pdoOne->generateCodeClassRepo(''address'',''eftec\tests\sakila2021'','array('actor'=>'ActorRepo','address'=>'AddresRepo','category'=>'CategoryRepo','city'=>'CityRepo','country'=>'CountryRepo','customer'=>'CustomerRepo','film'=>'FilmRepo','film_actor'=>'FilmActorRepo','film_category'=>'FilmCategoryRepo','film_text'=>'FilmTextRepo','inventory'=>'InventoryRepo','language'=>'LanguageRepo','payment'=>'PaymentRepo','rental'=>'RentalRepo','staff'=>'StaffRepo','store'=>'StoreRepo',)','''','array(0=>array(0=>'address_id',1=>'int',2=>NULL,),1=>array(0=>'address',1=>'string',2=>NULL,),2=>array(0=>'address2',1=>'string',2=>NULL,),3=>array(0=>'district',1=>'string',2=>NULL,),4=>array(0=>'city_id',1=>'int',2=>NULL,),5=>array(0=>'postal_code',1=>'string',2=>NULL,),6=>array(0=>'phone',1=>'string',2=>NULL,),7=>array(0=>'last_update',1=>'datetime',2=>NULL,),8=>array(0=>'_city_id',1=>'MANYTOONE',2=>'CityRepoModel',),9=>array(0=>'_customer',1=>'ONETOMANY',2=>'CustomerRepoModel',),10=>array(0=>'_staff',1=>'ONETOMANY',2=>'StaffRepoModel',),11=>array(0=>'_store',1=>'ONETOMANY',2=>'StoreRepoModel',),)','array('actor'=>array('actor_id'=>'actor_id','first_name'=>'first_name','last_name'=>'last_name','last_update'=>'last_update','_film_actor'=>'_film_actor',),'address'=>array('address'=>'address','address2'=>'address2','address_id'=>'address_id','city_id'=>'city_id','district'=>'district','last_update'=>'last_update','phone'=>'phone','postal_code'=>'postal_code','_city_id'=>'_city_id','_customer'=>'_customer','_staff'=>'_staff','_store'=>'_store',),'category'=>array('category_id'=>'category_id','last_update'=>'last_update','name'=>'name','_film_category'=>'_film_category',),'city'=>array('city'=>'city','city_id'=>'city_id','country_id'=>'country_id','last_update'=>'last_update','_country_id'=>'_country_id','_address'=>'_address',),'country'=>array('country'=>'country','country_id'=>'country_id','last_update'=>'last_update','_city'=>'_city',),'customer'=>array('active'=>'active','address_id'=>'address_id','create_date'=>'create_date','customer_id'=>'customer_id','email'=>'email','first_name'=>'first_name','last_name'=>'last_name','last_update'=>'last_update','store_id'=>'store_id','_address_id'=>'_address_id','_store_id'=>'_store_id','_payment'=>'_payment','_rental'=>'_rental',),'film'=>array('description'=>'description','film_id'=>'film_id','language_id'=>'language_id','last_update'=>'last_update','length'=>'length','original_language_id'=>'original_language_id','rating'=>'rating','release_year'=>'release_year','rental_duration'=>'rental_duration','rental_rate'=>'rental_rate','replacement_cost'=>'replacement_cost','special_features'=>'special_features','title'=>'title','_language_id'=>'_language_id','_original_language_id'=>'_original_language_id','_film_actor'=>'_film_actor','_film_text'=>'_film_text','_inventory'=>'_inventory',),'film_actor'=>array('actor_id'=>'actor_id','film_id'=>'film_id','last_update'=>'last_update','_actor_id'=>'_actor_id','_film_id'=>'_film_id',),'film_category'=>array('category_id'=>'category_id','film_id'=>'film_id','last_update'=>'last_update','_category_id'=>'_category_id',),'film_text'=>array('description'=>'description','film_id'=>'film_id','title'=>'title','_film_id'=>'_film_id',),'inventory'=>array('film_id'=>'film_id','inventory_id'=>'inventory_id','last_update'=>'last_update','store_id'=>'store_id','_film_id'=>'_film_id','_store_id'=>'_store_id','_rental'=>'_rental',),'language'=>array('language_id'=>'language_id','last_update'=>'last_update','name'=>'name','_film'=>'_film',),'payment'=>array('amount'=>'amount','customer_id'=>'customer_id','last_update'=>'last_update','payment_date'=>'payment_date','payment_id'=>'payment_id','rental_id'=>'rental_id','staff_id'=>'staff_id','_customer_id'=>'_customer_id','_rental_id'=>'_rental_id','_staff_id'=>'_staff_id',),'rental'=>array('customer_id'=>'customer_id','inventory_id'=>'inventory_id','last_update'=>'last_update','rental_date'=>'rental_date','rental_id'=>'rental_id','return_date'=>'return_date','staff_id'=>'staff_id','_customer_id'=>'_customer_id','_inventory_id'=>'_inventory_id','_staff_id'=>'_staff_id','_payment'=>'_payment',),'staff'=>array('active'=>'active','address_id'=>'address_id','email'=>'email','first_name'=>'first_name','last_name'=>'last_name','last_update'=>'last_update','password'=>'password','picture'=>'picture','staff_id'=>'staff_id','store_id'=>'store_id','username'=>'username','_store_id'=>'_store_id','_address_id'=>'_address_id','_payment'=>'_payment','_rental'=>'_rental','_store'=>'_store',),'store'=>array('address_id'=>'address_id','last_update'=>'last_update','manager_staff_id'=>'manager_staff_id','store_id'=>'store_id','_staff'=>'_staff','_address_id'=>'_address_id','_manager_staff_id'=>'_manager_staff_id','_customer'=>'_customer','_inventory'=>'_inventory',),)');
 * </pre>
 * @see CityRepoModel
 * @see CustomerRepoModel
 * @see StaffRepoModel
 * @see StoreRepoModel
 */
class AddresRepo extends AbstractAddresRepo
{
    const ME=__CLASS__;

    // [EDIT:content] you can edit this part
    // Here you can add your custom content.
    // [/EDIT] end of edit
}
