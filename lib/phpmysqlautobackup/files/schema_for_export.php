<?php
/*******************************************************************************************
    phpMySQLAutoBackup  -  Author:  http://www.DWalker.co.uk - released under GPL License
           For support and help please try the forum at: http://www.dwalker.co.uk/forum/
********************************************************************************************
Version    Date              Comment
0.2.0      7th July 2005     GPL release
0.3.0      June 2006  Upgrade - added ability to backup separate tables
0.4.0      Dec 2006   removed bugs/improved code
1.4.0      Dec 2007   improved faster version
1.5.0      Dec 2008   improved and added FTP backup to remote site
1.5.1      Feb 2009   improved export data - added quotes around field names
1.5.2      April 2009 improved "create table" export and added backup time start & end
1.5.3      Nov 2009   replaced PHP function "ereg_replace" with "str_replace" - all occurances
1.5.4      Nov 2009   replaced PHP function "str_replace" with "substr" line 114
1.5.5      Feb 2011  more options for config added - email reports only and/or backup, save backup file to local and/or remote server.
                                Reporter added: email report of last 6 (or more) backup stats (date, total bytes exported, total lines exported) plus any errors
                                MySQL error reporting added  and Automated version checker added
1.6.0      Dec 2011  PDO version
********************************************************************************************/
$phpMySQLAutoBackup_version="1.6.0";
// ---------------------------------------------------------
$dbc = dbc::instance();

//add new phpmysqlautobackup table if not there...
$result = $dbc->prepare("SHOW TABLES LIKE 'phpmysqlautobackup' ");
$rows = $dbc->executeGetRows($result);
if(count($rows)<1)
{
   $query = "
    CREATE TABLE phpmysqlautobackup (
    id int(11) NOT NULL,
    version varchar(6) default NULL,
    time_last_run int(11) NOT NULL,
    PRIMARY KEY (id)
    )";
   $result = $dbc->prepare($query);
   $result = $dbc->execute($result);
   $query="INSERT INTO phpmysqlautobackup (id, version, time_last_run)
             VALUES ('1', '$phpMySQLAutoBackup_version', '0');";
   $result = $dbc->prepare($query);
   $result = $dbc->execute($result);
}
//check time last run - to prevent malicious over-load attempts
$query="SELECT * from phpmysqlautobackup WHERE id=1 LIMIT 1 ;";
$result = $dbc->prepare($query);
$rows = $dbc->executeGetRows($result);
if (time() < ($rows[0]['time_last_run']+$time_interval)) exit();// exit if already run within last time_interval
//update version number if not already done so
if ($rows[0]['version']!=$phpMySQLAutoBackup_version)
{
 $result = $dbc->prepare("update phpmysqlautobackup set version='$phpMySQLAutoBackup_version'");
 $result = $dbc->execute($result);
}
////////////////////////////////////////////////////////////////////////////////////

$query="UPDATE phpmysqlautobackup SET time_last_run = '".time()."' WHERE id=1 LIMIT 1 ;";
$result = $dbc->prepare($query);
$result = $dbc->execute($result);

if (!isset($table_select))
{
  $result = $dbc->prepare("show tables");
  $i=0;
  $table="";
  $tables = $dbc->executeGetRows($result);
  foreach ($tables as $table_array)
  {
   list(,$table) = each($table_array);
   $exclude_this_table = isset($table_exclude)? in_array($table, $table_exclude) : false;
   if(!$exclude_this_table) $table_select[$i]=$table;
   $i++;
   //echo "$table<br>";
  }
}

$recordBackup = new record();

$thedomain = $_SERVER['HTTP_HOST'];
if (substr($thedomain,0,4)=="www.") $thedomain=substr($thedomain,4,strlen($thedomain));

$buffer = '# MySQL backup created by phpMySQLAutoBackup - Version: '.$phpMySQLAutoBackup_version . "\n" .
          '# ' . "\n" .
          '# http://www.dwalker.co.uk/phpmysqlautobackup/' . "\n" .
          '#' . "\n" .
          '# Database: '. $db . "\n" .
          '# Domain name: ' . $thedomain . "\n" .
          '# (c)' . date('Y') . ' ' . $thedomain . "\n" .
          '#' . "\n" .
          '# Backup START time: ' . strftime("%H:%M:%S",time()) . "\n".
          '# Backup END time: #phpmysqlautobackup-endtime#' . "\n".
          '# Backup Date: ' . strftime("%d %b %Y",time()) . "\n";

$i=0;
$lines_exported=0;
foreach ($table_select as $table)
        {
          $i++;
          $export = " \n" .'drop table if exists `' . $table . '`; ' . "\n";

          //export the structure
          $query='SHOW CREATE TABLE `' . $table . '`';
          $result = $dbc->prepare($query);
          $tables = $dbc->executeGetRows($result);
          //$export.= print_r($tables) ."; \n";
          $export.= $tables[0]['Create Table'] ."; \n";

          $table_list = array();
          $result = $dbc->prepare('show fields from  `' . $table . '`');
          $fields = $dbc->executeGetRows($result);
          foreach ($fields as $field_array) $table_list[] = $field_array['Field'];           

          $buffer.=$export;
          // dump the data
          $query='select * from `' . $table . '` LIMIT '. $limit_from .', '. $limit_to.' ';
          $result = $dbc->prepare($query);
          $rows = $dbc->executeGetRows($result);
          foreach ($rows as $row_array)
          {
            $export = 'insert into `' . $table . '` (`' . implode('`, `', $table_list) . '`) values (';
            $lines_exported++;
            reset($table_list);
            while (list(,$i) = each($table_list))
            {
              if (!isset($row_array[$i])) $export .= 'NULL, ';
              elseif (has_data($row_array[$i]))
              {
                $row = addslashes($row_array[$i]);
                $row = str_replace("\n#", "\n".'\#', $row);
                $export .= '\'' . $row . '\', ';
              }
              else $export .= '\'\', ';
            }
            $export = substr($export,0,-2) . "); \n";
            $buffer.= $export;
          }
        }
$recordBackup->save(time(), strlen($buffer), $lines_exported);
$buffer = str_replace('#phpmysqlautobackup-endtime#', strftime("%H:%M:%S",time()), $buffer);