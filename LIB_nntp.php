<?php


#-----------------------------------------------------------------------
# F U N C T I O N S
#
# read_nntp_buffer($socket)
#    Used by the other functions to read the buffer that receives data
#    from news servers
#
# get_nntp_groups($server)
#    Returns a list of newsgroups on a valid news server
#
# get_nntp_article_ids($server, $newsgroup)
#    Returns a list of article ids for a newsgroup on a news server
#
# read_nntp_article($server, $newsgroup, $article)
#    Downloads a single article from a news group on a news server
#
#-----------------------------------------------------------------------

/***********************************************************************
read_nntp_buffer($socket)
-------------------------------------------------------------
DESCRIPTION:
        Reads data from a news server

        THIS FUNCTION IS USED INTERNALLY AND NOT USEFUL ALONE

INPUT:
        $socket       Reference to the socket of the connection to
                      the news server

OUTPUT:
        The data sent from the news server
***********************************************************************/
function read_nntp_buffer($socket)
    {
    $this_line ="";
    $buffer ="";

    while($this_line!=".\r\n")          // Read until lone . found on line
        {
        $this_line = fgets($socket);    // Read line from socket
        $buffer = $buffer . $this_line;
        #
        # UNCOMMENT THE FOLLOWING LINE IF YOU NEED TO SEE PROGRESS (This script may take a long time to run).
         echo "this_line=$this_line;<br>";
        #
        }
    return $buffer;
    }

/***********************************************************************
get_nntp_groups($server)
-------------------------------------------------------------
DESCRIPTION:
        Reads available newsgroups from a news server

INPUT:
        $socket       Reference to the socket of the connection to
                      the news server

OUTPUT:
        A list of newsgroups on the news server
***********************************************************************/
function get_nntp_groups($server)
    {
    # Open socket connection to the mail server
    $fp = fsockopen($server, $port="119", $errno, $errstr, 30);
    if (!$fp)
        {
        # If socket error, issue error
        $return_array['ERROR'] = "ERROR: $errstr ($errno)";
        }
    else
        {
        # Else tell server to return a list of hosted newsgroups
        $out = "LIST\r\n";
        fputs($fp, $out);
        $groups = read_nntp_buffer($fp);

        $groups_array = explode("\r\n", $groups); // Convert to an array
        }
    fputs($fp, "QUIT \r\n"); // Log out
    fclose($fp); // Close socket

    return $groups_array;
    }

/***********************************************************************
get_nntp_article_ids($server, $newsgroup)
-------------------------------------------------------------
DESCRIPTION:
        Reads available article ids from a news server

INPUT:
        $server      Address of news server
        $newsgroup   Name of newsgroup

OUTPUT:
        Returns available article ids for the newsgroup on the server
***********************************************************************/
function get_nntp_article_ids($server, $newsgroup)
    {
    # Open socket connection to the mail server
    $socket = fsockopen($server, $port="119", $errno, $errstr, 30);
    if (!$socket)
        {
        # If socket error, issue error
        $return_array['ERROR'] = "ERROR: $errstr ($errno)";
        }
    else
        {
        # Else tell server which group to connect to
        fputs($socket, "GROUP ".$newsgroup." \r\n");
        $return_array['GROUP_MESSAGE'] = trim(fread($socket, 2000));
        # Get the range of available articles for this group
        fputs($socket, "NEXT \r\n");
        $res = fread($socket, 2000);
        $array = explode(" ", $res);
        $return_array['RESPONSE_CODE'] = $array[0];
        $return_array['EST_QTY_ARTICLES'] = $array[1];
        $return_array['FIRST_ARTICLE'] = $array[2];
        $return_array['LAST_ARTICLE'] = $array[3];
        }
    fputs($socket, "QUIT \r\n");
    fclose($socket);
    return $return_array;
    }

/***********************************************************************
read_nntp_article($server, $newsgroup, $article)
-------------------------------------------------------------
DESCRIPTION:
        Reads article from a news server

INPUT:
        $server      Address of news server
        $newsgroup   Name of newsgroup
        $article_id  ID of article to read

OUTPUT:
        Returns the article specified by the article id
***********************************************************************/
function read_nntp_article($server, $newsgroup, $article)
    {
    # Open socket connection to the mail server
    $socket = fsockopen($server, $port="119", $errno, $errstr, 30);
    if (!$socket)
        {
        # If socket error, issue error
        $return_array['ERROR'] = "ERROR: $errstr ($errno)";
        }
    else
        {
        # Else tell server which group to connect to
        fputs($socket, "GROUP ".$newsgroup." \r\n");
        # Request this article's HEAD
        fputs($socket, "HEAD $article \r\n");
        $return_array['HEAD'] = read_nntp_buffer($socket);
        # Request the article
        fputs($socket, "BODY $article \r\n");
        $return_array['ARTICLE'] = read_nntp_buffer($socket);
        }
    fputs($socket, "QUIT \r\n");    // Sign out (newsgroup server)
    fclose($socket);                // Close socket
    return $return_array;           // Return data array
    }
?>
