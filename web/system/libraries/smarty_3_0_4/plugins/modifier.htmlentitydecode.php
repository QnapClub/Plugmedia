<?php

function smarty_modifier_htmlentitydecode($string)
{
    return html_entity_decode($string,ENT_NOQUOTES,'UTF-8');
}



?>
