<?php

# Specifies if parse includes the delineator
define("EXCL", true);
define("INCL", false);
# Specifies if parse returns the text before or after the delineator
define("BEFORE", true);
define("AFTER", false);

function split_string($string, $delineator, $desired, $type)
    {
    # Case insensitive parse, convert string and delineator to lower case
    $lc_str = strtolower($string);
	$marker = strtolower($delineator);

    # Return text BEFORE the delineator
    if($desired == BEFORE)
        {
        if($type == EXCL)  // Return text ESCL of the delineator
            $split_here = strpos($lc_str, $marker);
        else               // Return text INCL of the delineator
            $split_here = strpos($lc_str, $marker)+strlen($marker);

        $parsed_string = substr($string, 0, $split_here);
        }
    # Return text AFTER the delineator
    else
        {
        if($type==EXCL)    // Return text ESCL of the delineator
            $split_here = strpos($lc_str, $marker) + strlen($marker);
        else               // Return text INCL of the delineator
            $split_here = strpos($lc_str, $marker) ;

        $parsed_string =  substr($string, $split_here, strlen($string));
        }
    return $parsed_string;
    }

function return_between($string, $start, $stop, $type)
    {
    $temp = split_string($string, $start, AFTER, $type);
    return split_string($temp, $stop, BEFORE, $type);
    }


function parse_array($string, $beg_tag, $close_tag)
    {
    preg_match_all("($beg_tag(.*)$close_tag)siU", $string, $matching_data);
    return $matching_data[0];
    }


function get_attribute($tag, $attribute)
    {
    # Use Tidy library to 'clean' input
    $cleaned_html = tidy_html($tag);

    # Remove all line feeds from the string
    $cleaned_html = str_replace("\r", "", $cleaned_html);
    $cleaned_html = str_replace("\n", "", $cleaned_html);

    # Use return_between() to find the properly quoted value for the attribute
    return return_between($cleaned_html, strtoupper($attribute)."=\"", "\"", EXCL);
    }


function remove($string, $open_tag, $close_tag)
    {
    # Get array of things that should be removed from the input string
    $remove_array = parse_array($string, $open_tag, $close_tag);

    # Remove each occurrence of each array element from string;
    for($xx=0; $xx<count($remove_array); $xx++)
        $string = str_replace($remove_array, "", $string);

    return $string;
    }

function tidy_html($input_string)
    {
    // Detect if Tidy is in configured
    if( function_exists('tidy_get_release') )
        {
        # Tidy for PHP version 4
        if(substr(phpversion(), 0, 1) == 4)
            {
            tidy_setopt('uppercase-attributes', TRUE);
            tidy_setopt('wrap', 800);
            tidy_parse_string($input_string);
            $cleaned_html = tidy_get_output();
            }
        # Tidy for PHP version 5
        if(substr(phpversion(), 0, 1) == 5)
            {
            $config = array(
                           'uppercase-attributes' => true,
                           'wrap'                 => 800);
            $tidy = new tidy;
            $tidy->parseString($input_string, $config, 'utf8');
            $tidy->cleanRepair();
            $cleaned_html  = tidy_get_output($tidy);
            }
        }
    else
        {
        # Tidy not configured for this computer
        $cleaned_html = $input_string;
        }
    return $cleaned_html;
    }