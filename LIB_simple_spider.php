<?php

function harvest_links($url)
    {
    # Initialize
    global $DELAY;
    $link_array = array();

    # Get page base for $url
    $page_base = get_base_page_address($url);

    # Download webpage
    sleep($DELAY);
    $downloaded_page = http_get($url, "");
    $anchor_tags = parse_array($downloaded_page['FILE'], "<a", "</a>", EXCL);
    # Put http attributes for each tag into an array
    for($xx=0; $xx<count($anchor_tags); $xx++)
        {
        $href = get_attribute($anchor_tags[$xx], "href");
        $resolved_addres = resolve_address($href, $page_base);
        $link_array[] = $resolved_addres;
        echo "Harvested: ".$resolved_addres." \n";
        }
    return $link_array;
    }

function archive_links($spider_array, $penetration_level, $temp_link_array)
    {
    for($xx=0; $xx<count($temp_link_array); $xx++)
        {
        # Don't add exlcuded links to $spider_array
        if(!excluded_link($spider_array, $temp_link_array[$xx]))
            {
            $spider_array[$penetration_level][] = $temp_link_array[$xx];
            }
        }
    return $spider_array;
    }


function get_domain($url)
    {
    // Remove protocol from $url
    $url = str_replace("http://", "", $url);
    $url = str_replace("https://", "", $url);

    // Remove page and directory references
    if(stristr($url, "/"))
        $url = substr($url, 0, strpos($url, "/"));

    return $url;
    }

function excluded_link($spider_array, $link)
    {
    # Initialization
    global $SEED_URL, $exclusion_array, $ALLOW_OFFISTE;
    $exclude = false;

    // Exclude links that are JavaScript commands
    if(stristr($link, "javascript"))
        {
        echo "Ignored JavaScript fuction: $link\n";
        $exclude=true;
        }

    // Exclude redundant links
    for($xx=0; $xx<count($spider_array); $xx++)
        {
        $saved_link="";
        while(isset($saved_link))
            {
            $saved_link=array_pop($spider_array[$xx]);
            if($link == array_pop($spider_array[$xx]))
                {
                echo "Ignored redundant link: $link\n";
                $exclude=true;
                break;
                }
            }
        }

    // Exclude links found in $exclusion_array
    for($xx=0; $xx<count($exclusion_array); $xx++)
        {
        if(stristr($link, $exclusion_array[$xx]))
            {
            echo "Ignored excluded link: $link\n";
            $exclude=true;
            }
        }

    // Exclude offsite links if requested
    if($ALLOW_OFFISTE==false)
        {
        if(get_domain($link)!=get_domain($SEED_URL))
            {
            echo "Ignored offsite link: $link\n";
            $exclude=true;
            }
        }

    return $exclude;
    }
?>