<?php


########################################################################
#
# LIB_mysql.php     MySQL database Routines
#
#-----------------------------------------------------------------------
# FUNCTIONS
#
#    insert()
#               Inserts a row into database,
#               as defined by a keyed array
#
#    update()
#               Updates an existing row in a database,
#               as defined by a keyed array and a row index
#
#    exe_sql()
#               Executes a SQL command and return a result set
#
########################################################################

/***********************************************************************
MySQL Constants (scope = global)
----------------------------------------------------------------------*/
define("MYSQL_ADDRESS", "");          // Define the IP address of your MySQL Server
define("MYSQL_USERNAME", "");         // Define your MySQL user name
define("MYSQL_PASSWORD", "");         // Define your MySQL password
define("DATABASE", "");               // Define your default database
define("SUCCESS", true);              // Successful operation flag
define("FAILURE", false);             // Failed operation flag

if(strlen(MYSQL_ADDRESS) + strlen(MYSQL_USERNAME) + strlen(MYSQL_PASSWORD) + strlen(MYSQL_ADDRESS) + strlen(DATABASE) == 0)
    echo "WARNING: MySQL not configured.<br>\n";

/***********************************************************************
Database connection routine (only used by routines in this library
----------------------------------------------------------------------*/
function connect_to_database()
	{
	return(mysql_connect(MYSQL_ADDRESS, MYSQL_USERNAME, MYSQL_PASSWORD));
	}

/***********************************************************************
insert($database, $table, $data_array)
-------------------------------------------------------------
DESCRIPTION:
        Inserts a row into database as defined by a keyed array
INPUT:
        $database     Name of database (where $table is located)
        $table        Table where row insertion occurs
        $data_array   A keyed array with defines the data to insert
                      (i.e. $data_array['column_name'] = data)
RETURNS
        SUCCESS or FAILURE
***********************************************************************/
function insert($database, $table, $data_array)
	{
    # Connect to MySQL server and select database
	$mysql_connect = connect_to_database();
	mysql_select_db ($database, $mysql_connect);

    # Create column and data values for SQL command
    foreach ($data_array as $key => $value)
        {
        $tmp_col[] = $key;
        $tmp_dat[] = "'$value'";
        }
     $columns = join(",", $tmp_col);
     $data = join(",", $tmp_dat);

    # Create and execute SQL command
	$sql = "INSERT INTO ".$table."(".$columns.")VALUES(". $data.")";
    $result = mysql_query($sql, $mysql_connect);

    # Report SQL error, if one occured, otherwise return result
    if(mysql_error($mysql_connect))
        {
        echo "MySQL Update Error: ".mysql_error($mysql_connect);
        $result = "";
        }
    else
        {
        return $result;
        }
	}

/***********************************************************************
update($database, $table, $data_array, $key_column, $id)
-------------------------------------------------------------
DESCRIPTION:
        Inserts a row into database as defined by a keyed array
INPUT:
        $database     Name of database (where $table is located)
        $table        Table where row insertion occurs
        $data_array   A keyed array with defines the data to insert
                      (i.e. $data_array['column_name'] = data)
RETURNS
        SUCCESS or FAILURE
***********************************************************************/
function update($database, $table, $data_array, $key_column, $id)
	{
    # Connect to MySQL server and select database
	$mysql_connect = connect_to_database();
	$bool= mysql_select_db ($database, $mysql_connect);

    # Create column and data values for SQL command
	$setting_list="";
	for ($xx=0; $xx<count($data_array); $xx++)
		{
		list($key,$value)=each($data_array);
		$setting_list.= $key."="."\"".$value."\"";
		if ($xx!=count($data_array)-1)
			$setting_list .= ",";
		}

    # Create SQL command
	$sql="UPDATE ".$table." SET ".$setting_list." WHERE ". $key_column." = " . "\"" . $id . "\"";
    $result = mysql_query($sql, $mysql_connect);

    # Report SQL error, if one occured, otherwise return result
    if(mysql_error($mysql_connect))
        {
        echo "MySQL Update Error: ".mysql_error($mysql_connect);
        $result = "";
        }
    else
        {
        return $result;
        }
	}

/***********************************************************************
exe_sql($database, $sql)
-------------------------------------------------------------
DESCRIPTION:
        Executes a SQL command and returns the result
INPUT:
        $database     Name of database to operate on
        $sql          sql command applied to $database
RETURNS
        An array containing the results of sql operation
***********************************************************************/
function exe_sql($database, $sql)
	{
    # Connect to MySQL server and select database
	$mysql_connect = connect_to_database();
	mysql_select_db($database, $mysql_connect);

    # Execute SQL command
	$result = mysql_query($sql, $mysql_connect);

    # Report SQL error, if one occured
    if(mysql_error ($mysql_connect))
        {
        echo "MySQL ERROR: ".mysql_error($mysql_connect);
        $result_set = "";
        }
    else
        {
        # Fetch every row in the result set
        for ($xx=0; $xx<mysql_numrows($result); $xx++)
    	    {
		    $result_set[$xx] = mysql_fetch_array($result);
    	    }

        # If the result set has only one row, return a single dimension array
        if(sizeof($result_set)==1)
            $result_set=$result_set[0];

        return $result_set;
        }
	}
?>