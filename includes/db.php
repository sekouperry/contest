<?php
/*
+--------------------------------------------------------------------------
|   CubeCart v3
|   ========================================
|   by Alistair Brookbanks
|	CubeCart is a Trade Mark of Devellion Limited
|   Copyright Devellion Limited 2005 - 2006. All rights reserved.
|   Devellion Limited,
|   5 Bridge Street,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 2JU
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Date: Tuesday, 17th July 2007
|   Email: sales (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply 
|   Licence Info: http://www.cubecart.com/site/faq/license.php
+--------------------------------------------------------------------------
|	db.inc.php
|   ========================================
|	Database Class	
+--------------------------------------------------------------------------
*/

if (class_exists('db'))
{
	return;
}

class db
{

	var $query = "";
	var $db = "";
	
	function db()
	{
		global $global;
		
		$this->db = @mysql_connect($global['dbhost'], $global['dbusername'], $global['dbpassword']);		
		if (!$this->db) die ($this->debug(true));	
		
		mysql_set_charset('utf8',$this->db); 
		
		$selectdb = @mysql_select_db($global['dbdatabase']);
		if (!$selectdb) die ($this->debug());
	
	} // end constructor
	
	
	function select($query, $maxRows=0, $pageNum=0, $outputFieldsName = false)
	{
		$this->query = $query;
		
		// start limit if $maxRows is greater than 0
		if($maxRows>0)
		{
			$startRow = $pageNum * $maxRows;
			$query = sprintf("%s LIMIT %d, %d", $query, $startRow, $maxRows);
		}	
		
		$result = mysql_query($query, $this->db);
		
		if ($this->error()) die ($this->debug());
		
		$output=false;
		$fields = false;
		if($outputFieldsName){
			for ($n=0; $n < mysql_num_fields($result); $n++)
			{
				$fields[$n] = mysql_field_name($result, $n);
			}
		}
		
		for ($n=0; $n < mysql_num_rows($result); $n++)
		{
			$row = mysql_fetch_assoc($result);
			$output[$n] = $row;
		}

		if($outputFieldsName){
			return array($output, $fields);
		}
		return $output;
		
	} // end select
	
	function misc($query) {
	
		$this->query = $query;
		$result = mysql_query($query, $this->db);
		
		if ($this->error()) die ($this->debug());
		
		if($result == TRUE){
		
			return TRUE;
			
		} else {
		
			return FALSE;
			
		}
		
	}
	
	function numrows($query) {
		$this->query = $query;
		$result = mysql_query($query, $this->db);
		return mysql_num_rows($result);
	}
	
	function paginate($numRows, $maxRows, $pageNum=0, $pageVar="page", $class="txtLink")
	{
	global $lang;
	$navigation = "";
	
	// get total pages
	$totalPages = ceil($numRows/$maxRows);
	
	// develop query string minus page vars
	$queryString = "";
		if (!empty($_SERVER['QUERY_STRING'])) {
			$params = explode("&", $_SERVER['QUERY_STRING']);
			$newParams = array();
				foreach ($params as $param) {
					if (stristr($param, $pageVar) == false) {
						array_push($newParams, $param);
					}
				}
			if (count($newParams) != 0) {
				$queryString = "&" . htmlentities(implode("&", $newParams));
			}
		}
		
	// get current page	
	$currentPage = $_SERVER['PHP_SELF'];
	
	// build page navigation
	if($totalPages> 1){
	$navigation = $lang['misc']['pages']; 
	
	$upper_limit = $pageNum + 3;
	$lower_limit = $pageNum - 3;
	
		if ($pageNum > 0) { // Show if not first page
			
			if(($pageNum - 2)>0){
			$first = sprintf("%s?".$pageVar."=%d%s", $currentPage, 0, $queryString);
			$navigation .= "<a href='".$first."' class='".$class."'>&laquo;</a> ";}
			
			$prev = sprintf("%s?".$pageVar."=%d%s", $currentPage, max(0, $pageNum - 1), $queryString);
			$navigation .= "<a href='".$prev."' class='".$class."'>&laquo;</a> ";
		} // Show if not first page
		
		// get in between pages
		for($i = 0; $i < $totalPages; $i++){
		
			$pageNo = $i+1;
			
			if($i==$pageNum){
				$navigation .= "&nbsp;<strong>[".$pageNo."]</strong>&nbsp;";
			} elseif($i!==$pageNum && $i<$upper_limit && $i>$lower_limit){
				$noLink = sprintf("%s?".$pageVar."=%d%s", $currentPage, $i, $queryString);
				$navigation .= "&nbsp;<a href='".$noLink."' class='".$class."'>".$pageNo."</a>&nbsp;";
			} elseif(($i - $lower_limit)==0){
				$navigation .=  "&hellip;";
			} 
		}
		  
		if (($pageNum+1) < $totalPages) { // Show if not last page
			$next = sprintf("%s?".$pageVar."=%d%s", $currentPage, min($totalPages, $pageNum + 1), $queryString);
			$navigation .= "<a href='".$next."' class='".$class."'>&raquo;</a> ";
			if(($pageNum + 3)<$totalPages){
			$last = sprintf("%s?".$pageVar."=%d%s", $currentPage, $totalPages-1, $queryString);
			$navigation .= "<a href='".$last."' class='".$class."'>&raquo;</a>";}
		} // Show if not last page 
		
		} // end if total pages is greater than one
		
		return $navigation;
	
	}
	
	function insert ($tablename, $record)
	{
		if(!is_array($record)) die ($this->debug("array", "Insert", $tablename));
		
		$count = 0;
		foreach ($record as $key => $val)
		{
			if ($count==0) {$fields = "`".$key."`"; $values = $this->mySQLSafe($val);}
			else {$fields .= ", "."`".$key."`"; $values .= ", ".$this->mySQLSafe($val);}
			$count++;
		}	
		
		$query = "INSERT INTO ".$tablename." (".$fields.") VALUES (".$values.")";
		
		$this->query = $query;
		mysql_query($query, $this->db);
		
		if ($this->error()) die ($this->debug());
		
		if ($this->affected() > 0) return true; else return false;
		
	} // end insert
	
	
	function update ($tablename, $record, $where)
	{
		if(!is_array($record)) die ($this->debug("array", "Update", $tablename));
	
		$count = 0;
		
		foreach ($record as $key => $val)
		{
			if ($count==0) $set = "`".$key."`"."=".$this->mySQLSafe($val);
			else $set .= ", " . "`".$key."`". "= ".$this->mySQLSafe($val);
			$count++;
		}	
	
		$query = "UPDATE ".$tablename." SET ".$set." WHERE ".$where;
		
		$this->query = $query;
		mysql_query($query, $this->db);
		if ($this->error()) die ($this->debug());
		
		if ($this->affected() > 0) return true; else return false;
		
	} // end update
	
	function increment ($tablename, $record, $where)
	{
		if(!is_array($record)) die ($this->debug("array", "Update", $tablename));
	
		$count = 0;
		
		foreach ($record as $key => $val)
		{
			if ($count==0) $set = "`".$key."`"."="."`".$key."`"."+1";
			else $set .= "`".$key."`"."="."`".$key."`"."+1";
			$count++;
		}	
	
		$query = "UPDATE ".$tablename." SET ".$set." WHERE ".$where;
		
		$this->query = $query;
		mysql_query($query, $this->db);
		if ($this->error()) die ($this->debug());
		
		if ($this->affected() > 0) return true; else return false;
		
	} // end increment
	
	function categoryNos($cat_id, $sign, $amount = 1) {
		
		global $global;
	
		if($cat_id > 0) {
	
			do {
		
				$record['noProducts'] = " noProducts ".$sign.$amount;
				$where = "cat_id = ".$cat_id; 
				$this->update($global['dbprefix']."CubeCart_category", $record, $where, "");
			
				$query = "SELECT cat_father_id FROM ".$global['dbprefix']."CubeCart_category WHERE cat_id = ".$cat_id;
				$cfi = $this->select($query);
				$cat_id = $cfi['0']['cat_father_id'];
			
			} while ($cat_id > 0);
			
		} 
	
	}
	
	function delete($tablename, $where, $limit="")
	{
		$query = "DELETE from ".$tablename." WHERE ".$where;
		if ($limit!="") $query .= " LIMIT " . $limit;
		$this->query = $query;
		mysql_query($query, $this->db);
		
		if ($this->error()) die ($this->debug());
	
		if ($this->affected() > 0){ 
			return TRUE; 
		} else { 
			return FALSE;
		}
	
	} // end delete
	
	//////////////////////////////////
	// Clean SQL Variables (Security Function)
	////////
	function mySQLSafe($value, $quote="'") { 
		
		// strip quotes if already in
		$value = str_replace(array("\'","'"),"&#39;",$value);
		
		// Stripslashes 
		if (get_magic_quotes_gpc()) { 
			$value = stripslashes($value); 
		} 
		// Quote value
		if(version_compare(phpversion(),"4.3.0")=="-1") {
			$value = mysql_escape_string($value);
		} else {
			$value = mysql_real_escape_string($value);
		}
		$value = $quote . trim($value) . $quote; 
	 
		return $value; 
	}
	
	
	
	function debug($type="", $action="", $tablename="")
	{
		switch ($type)
		{
			case "connect":
				$message = "MySQL Error Occured";
				$result = mysql_errno() . ": " . mysql_error();
				$query = "";
				$output = "Could not connect to the database. Be sure to check that your database connection settings are correct and that the MySQL server in running.";
			break;
		
		
			case "array":
				$message = $action." Error Occured";
				$result = "Could not update ".$tablename." as variable supplied must be an array.";
				$query = "";
				$output = "Sorry an error has occured accessing the database. Be sure to check that your database connection settings are correct and that the MySQL server in running.";
				
			break;
		
			
			default:
				if (mysql_errno($this->db))
				{
					$message = "MySQL Error Occured";
					$result = mysql_errno($this->db) . ": " . mysql_error($this->db);
					$output = "Sorry an error has occured accessing the database. Be sure to check that your database connection settings are correct and that the MySQL server in running.";
				}
				else 
				{
					$message = "MySQL Query Executed Succesfully.";
					$result = mysql_affected_rows($this->db) . " Rows Affected";
					$output = "view logs for details";
				}
				
				$linebreaks = array("\n", "\r");
				if($this->query != "") $query = "QUERY = " . str_replace($linebreaks, " ", $this->query); else $query = "";
			break;
		}
		
		$output = "<b style='font-family: Arial, Helvetica, sans-serif; color: #0B70CE;'>".$message."</b><br />\n<span style='font-family: Arial, Helvetica, sans-serif; color: #000000;'>".$result."</span><br />\n<p style='Courier New, Courier, mono; border: 1px dashed #666666; padding: 10px; color: #000000;'>".$query."</p>\n";
		
		return $output;
	}
	
	
	function error()
	{
		if (mysql_errno($this->db)) return true; else return false;
	}
	
	
	function insertid()
	{
		return mysql_insert_id($this->db);
	}
	
	function affected()
	{
		return mysql_affected_rows($this->db);
	}
	
	function close() // close conection
	{
		mysql_close($this->db);
	} 
	

} // end of db class
?>