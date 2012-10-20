<?php

function download_parse_rss($target)
    {
    # download tge rss page
    $news = http_get($target, "");

    # Parse title & copyright notice
    $rss_array['TITLE'] = return_between($news['FILE'], "<title>", "</title>", EXCL);
    $rss_array['COPYRIGHT'] = return_between($news['FILE'], "<copyright>", "</copyright>", EXCL);

    # Parse the items
    $item_array = parse_array($news['FILE'], "<item>", "</item>");
    for($xx=0; $xx<count($item_array); $xx++)
        {
        $rss_array['ITITLE'][$xx] = return_between($item_array[$xx], "<title>", "</title>", EXCL);
        $rss_array['ILINK'][$xx] = return_between($item_array[$xx], "<link>", "</link>", EXCL);
        $rss_array['IDESCRIPTION'][$xx] = return_between($item_array[$xx], "<description>", "</description>", EXCL);
        $rss_array['IPUBDATE'][$xx] = return_between($item_array[$xx], "<pubDate>", "</pubDate>", EXCL);
        }

    return $rss_array;
    }


function display_rss_array($rss_array)
    {?>
    <table border="0">
        <!-- Display the article title and copyright notice -->
        <tr><td><font size="+1"><b><?echo strip_cdata_tags($rss_array['TITLE'])?></b></font></td></tr>
        <tr><td><?echo strip_cdata_tags($rss_array['COPYRIGHT'])?></td></tr>

        <!-- Display the article descriptions and links -->
        <?for($xx=0; $xx<count($rss_array['ITITLE']); $xx++)
            {?>
            <tr>
                <td>
                    <a href="<?echo strip_cdata_tags($rss_array['ILINK'][$xx])?>">
                        <b><?echo strip_cdata_tags($rss_array['ITITLE'][$xx])?></b>
                    </a>
                </td>
            </tr>
            <tr>
                <td><?echo strip_cdata_tags($rss_array['IDESCRIPTION'][$xx])?></td>
            </tr>
            <tr>
                <td><font size="-1"><?echo strip_cdata_tags($rss_array['IPUBDATE'][$xx])?></font></td>
            </tr>
          <?}?>
    </table>
  <?}


function strip_cdata_tags($string)
    {
    # Strip XML CDATA characters from all array elements
    $string = str_replace("<![CDATA[", "", $string);
    $string = str_replace("]]>", "", $string);
    return $string;
    }
  ?>
