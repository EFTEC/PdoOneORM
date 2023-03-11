<?php
/** @noinspection AccessModifierPresentedInspection
 * @noinspection PhpUnusedAliasInspection
 * @noinspection UnknownInspectionInspection
 * @noinspection PhpUnused
 */
namespace eftec\examples\example1\repo;

use Exception;
// [EDIT:use] you can edit this part
// Here you can add your custom use
// [/EDIT] end of edit

/**
 * Class FilmActorRepo Copyright (c) Jorge Castro C. (https://github.com/EFTEC/PdoOne)<br>
 * <ul>
 * <li><b>actor_id</b>: int (alias of column actor_id) </li>
 * <li><b>film_id</b>: int (alias of column film_id) </li>
 * <li><b>last_update</b>: datetime (alias of column last_update) </li>
 * <li><b>_actor_id</b>: ONETOONE (alias of column _actor_id) (ActorRepoModel)</li>
 * <li><b>_film_id</b>: MANYTOONE (alias of column _film_id) (FilmRepoModel)</li>
 * </ul>
 * Generated by PdoOne Version 1.00 Date generated Sat, 11 Mar 2023 14:32:14 -0300.<br>
 * <b>YOU CAN EDIT THIS CODE</b>. It is not replaced by the generation of the code, unless it is indicated<br>
 * @see ActorRepoModel
 * @see FilmRepoModel
 */
class FilmActorRepo extends AbstractFilmActorRepo
{
    const ME=__CLASS__;
    
    // [EDIT:content] you can edit this part
    // Here you can add your custom content.
    // [/EDIT] end of edit
}
