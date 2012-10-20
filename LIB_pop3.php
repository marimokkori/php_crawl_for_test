<?php

define("END_CHAR", ".");        // Identifies the POP3 multi line
                                // termination character (if found
                                // at the beginning of a line.
define("POP3_PORT", "110");     // POP3 servers listen to port 110

function POP3_connect($server, $user, $pass)
    {
    $error="";
    $handle = fsockopen($server, $port=POP3_PORT, $errno, $errstr, 30);
    $message = fgets($handle, 4096);
    if(stristr($message, "+OK") === FALSE)
        $error = $error . $message;

    // Send the username
    fwrite($handle, "user ".$user."\n");
    $user_reply = fgets($handle, 4096);
    if(stristr($user_reply, "+OK") === FALSE)
        $error = $error . $user_reply;

    // Send the password
    fwrite($handle, "pass ".$pass."\n");
    $pass_reply = fgets($handle, 4096);
    if(stristr($pass_reply, "+OK") === FALSE)
        $error = $error . $pass_reply;

    // Evaluate the results of presenting user/pass credentials
    if(strlen($error)==0)
        {
        $ret_array['login']=true;
        $ret_array['message']=$message;
        }
    else
        {
        $ret_array['login']=false;
        $ret_array['message']=$error;
        }
    $ret_array['handle']=$handle;

    // Return result array
    return $ret_array;
    }

function POP3_list($handle)
    {
    // Initialization
    $list_array = array();
    $reply="";

    // Make "list" request
    $list = fputs($handle, "LIST\n");
    $list_reply = fgets($handle);

    // Fill an array with mail server reply
    if(stristr($list_reply, "+OK"))
        {
    	while(!stristr($reply, END_CHAR))
            {
            $reply = fgets($handle);
            if(!stristr($reply, END_CHAR))
                array_push($list_array, $reply);
            }
        return $list_array;
        }
    }

function POP3_retr($handle, $id)
    {
    // Initialize
    $flag="";
    $message="";

    // Make POP3 "RETR #id" request
    fputs($handle, "RETR ".$id."\n");

    // Gather the multiline response
   	while($flag=="")
        {
        $this_line = fgets($handle, 4095);
        if(substr($this_line, 0, 1)==".")
            $flag="FLAG";

        $message = $message . $this_line;
        }
    return $message;
    }

function POP3_delete($handle, $id)
    {
    fputs($handle, "DELE ".$id."\n");
    $reply = fgets($handle);
    return $reply;
    }


function POP3_quit($handle)
    {
    fputs($handle, "QUIT\n");
    $reply = fgets($handle);
    return $reply;
    }
?>
