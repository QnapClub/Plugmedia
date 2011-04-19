<?php

/**
 * Project:     SmartyPaginate: Pagination for the Smarty Template Engine
 * File:        function.paginate_middle.php
 * Author:      Monte Ohrt <monte at newdigitalgroup dot com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @link http://www.phpinsider.com/php/code/SmartyPaginate/
 * @copyright 2001-2005 New Digital Group, Inc.
 * @author Monte Ohrt <monte at newdigitalgroup dot com>
 * @package SmartyPaginate
 * @version 1.5
 */

function smarty_function_paginate_middle($params, &$smarty) {
    
    $_id = 'default';
    $_prefix = '[';
    $_suffix = ']';
    $_link_prefix = '';
    $_link_suffix = ''; 
    $_page_limit = null;
    $_attrs = array();

    if (!class_exists('SmartyPaginate')) {
        $smarty->trigger_error("paginate_middle: missing SmartyPaginate class");
        return;
    }
    if (!isset($_SESSION['SmartyPaginate'])) {
        $smarty->trigger_error("paginate_middle: SmartyPaginate is not initialized, use connect() first");
        return;        
    }
        
    foreach($params as $_key => $_val) {
        switch($_key) {
            case 'id':
                if (!SmartyPaginate::isConnected($_val)) {
                    $smarty->trigger_error("paginate_middle: unknown id '$_val'");
                    return;        
                }
                $_id = $_val;
                break;
            case 'prefix':
                $_prefix = $_val;
                break;
            case 'suffix':
                $_suffix = $_val;
                break;
            case 'link_prefix':
                $_link_prefix = $_val;
                break;
            case 'link_suffix':
                $_link_suffix = $_val;
                break; 
            case 'page_limit';
                $_page_limit = $_val;
                break;
            case 'delta';
                $_delta = $_val;
                break;
            case 'format':
                break;
            default:
                $_attrs[] = $_key . '="' . $_val . '"';
                break;   
        }
    }
	
	if (!isset($_delta))
		$_delta = 1;

    if (!isset($_SESSION['SmartyPaginate'][$_id]['item_total'])) {
        $smarty->trigger_error("paginate_middle: total was not set");
        return;        
    }
    
    if(!isset($_page_limit) && isset($_SESSION['SmartyPaginate'][$_id]['page_limit'])) {
        $_page_limit = $_SESSION['SmartyPaginate'][$_id]['page_limit'];
    }
        
    $_url = $_SESSION['SmartyPaginate'][$_id]['url'];
    
    $_total = SmartyPaginate::getTotal($_id);
    $_curr_item = SmartyPaginate::getCurrentItem($_id);
    $_limit = SmartyPaginate::getLimit($_id);
	
    $_item = 1;
    $_page = 1;
    $_displayed_pages = 0;
    
    $_attrs = !empty($_attrs) ? ' ' . implode(' ', $_attrs) : '';
    
    if(isset($_page_limit)) {
		// find halfway point
        $_page_limit_half = floor($_page_limit / 2);
        // determine what item/page we start with
        $_item_start = $_curr_item - $_limit * $_page_limit_half;
        if( ($_view = ceil(($_total - $_item_start) / $_limit)) < $_page_limit) {
            $_item_start -= ($_limit * ( $_page_limit - $_view ));
        }
        $_item = ($_item_start >= 1) ? $_item_start : 1;
        $_page = ceil($_item / $_limit);
    }
    $get_url_var = SmartyPaginate::getUrlVar($_id);
	$_page_total = ceil($_total / $_limit);
	$_current_page = ceil($_curr_item / $_limit);	
	
	$adjacents = $_delta;
	$limit = $_limit;
	$page = $_current_page;
	
	$prev = $page - 1;									
	$next = $page + 1;									
	$lastpage = ceil($_total / $_limit);				
	$lpm1 = $lastpage - 1;								
	
	$pagination = "";
	if($lastpage > 1)
	{	
	
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			$start_item =1;
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination .= disabledLink($_link_prefix . $counter  . $_link_suffix);
				else
				{
					$_this_url = prepareUrl($_url, $start_item, $get_url_var);
					$pagination .= enabledLink($_this_url, $counter, $_attrs, $_link_prefix, $_link_suffix);
				}
				$start_item += $_limit;
								
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page <= 1 + ($adjacents * 3))		
			{
				$start_item =1;
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination .= disabledLink($_link_prefix . $counter  . $_link_suffix);
					else
					{	
						$_this_url = prepareUrl($_url, $start_item, $get_url_var);
						$pagination .= enabledLink($_this_url, $counter, $_attrs, $_link_prefix, $_link_suffix);
				
					}
					$start_item += $_limit;
				}
				$pagination.= disabledLink('...');
				$pagination .= getLastPage(1, $_url, $_page_total, $_attrs, $_link_prefix, $_link_suffix, $get_url_var,$_limit);
				// last page
				$pagination .= getLastPage(0, $_url, $_page_total, $_attrs, $_link_prefix, $_link_suffix, $get_url_var,$_limit);
			
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination .= getLastPage($_page_total-1, $_url, $_page_total, $_attrs, $_link_prefix, $_link_suffix, $get_url_var,$_limit); // FIRST PAGE
				$pagination .= getLastPage($_page_total-2, $_url, $_page_total, $_attrs, $_link_prefix, $_link_suffix, $get_url_var,$_limit); // SECOND PAGE
				
				$pagination.= disabledLink('...');

				$to_start = (($page - $adjacents -1)*$_limit)+1; 
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination .= disabledLink($_link_prefix . $counter  . $_link_suffix);
					else
					{	
						$_this_url = prepareUrl($_url, $to_start, $get_url_var);
						$pagination .= enabledLink($_this_url, $counter, $_attrs, $_link_prefix, $_link_suffix);
					}
					$to_start += $_limit;					
				}
				
				$pagination.= disabledLink('...');
				$pagination .= getLastPage(1, $_url, $_page_total, $_attrs, $_link_prefix, $_link_suffix, $get_url_var,$_limit);
				// last page
				$pagination .= getLastPage(0, $_url, $_page_total, $_attrs, $_link_prefix, $_link_suffix, $get_url_var,$_limit);
			}
			//close to end; only hide early pages
			else
			{
				$pagination .= getLastPage($_page_total-1, $_url, $_page_total, $_attrs, $_link_prefix, $_link_suffix, $get_url_var,$_limit); // FIRST PAGE
				$pagination .= getLastPage($_page_total-2, $_url, $_page_total, $_attrs, $_link_prefix, $_link_suffix, $get_url_var,$_limit); // SECOND PAGE
				
				$pagination.= disabledLink('...');
				$_item = ($lastpage - (2 + ($adjacents * 3)) ) * $_limit;
				$_item ++;
				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination .= disabledLink($_link_prefix . $counter  . $_link_suffix);
					else
					{	
						$_this_url = prepareUrl($_url, $_item, $get_url_var);
						$pagination .= enabledLink($_this_url, $counter, $_attrs, $_link_prefix, $_link_suffix);
					}
					$_item += $_limit;			
				}
			}
		}
	}
	
	
    return $pagination;
    
}

function disabledLink($text)
{
	return '<a class="page disabled" href="javascript:void(0)">' . $text .'</a>';	
}
function enabledLink($url, $text, $attrs, $link_prefix, $link_suffix)
{
	return ''. $link_prefix . '<a href="' . str_replace('&', '&amp;', $url) . '"' . $attrs . ' class="page custom_target"><span>' . $text . '</span></a>' . $link_suffix .'';
}

function prepareUrl($url, $next, $getUrl_var)
{
	$_formated_url = $url;
	$_formated_url .= (strpos($url, '?') === false) ? '?' : '&';
	$_formated_url .= $getUrl_var . '=' . $next;
	return $_formated_url;
}

function getLastPage($numberbefore=0, $url, $page_total, $_attrs, $_link_prefix, $_link_suffix, $get_url_var,$_limit)
{
	$page_total = $page_total-$numberbefore;
	$_this_url = prepareUrl($url, ((($page_total-1)*$_limit)+1), $get_url_var);
	return enabledLink($_this_url, $page_total, $attrs, $_link_prefix, $_link_suffix);
}

?>
