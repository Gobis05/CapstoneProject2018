<?php
/**
 * The following will check to see if today is the first of the year. If it is, then the database will expire all user's memberships
 *
 */
 //include(require_once('NHOHVA/PHPMailer'));
 //system('ls NHOHVA/PHPMailer');

/** 
 * The following creates a backup of the NHOHVA Database in the NHOHVABackups folder on the local turing drive.
 *
 */

 //Database information
define('DB_HOST', 'localhost');
define('DB_NAME', 'NHOHVA');
define('DB_USER', 'mg1021');
define('DB_PASSWORD', 'goodspec');
define('BACKUP_SAVE_TO', '/home/mg1021/Home/NHOHVABackups'); // without trailing slash
 
 //For file name purposes, get the date
$time = time();
$date = date('Y-m-d', $time);
 
 //Create the file name and where its going
$backupFile = BACKUP_SAVE_TO . '/' . DB_NAME . '_' . $date . '.gz';
if (file_exists($backupFile)) {
    unlink($backupFile);
}

$command = 'mysqldump --opt -h ' . DB_HOST . ' -u ' . DB_USER . ' -p\'' . DB_PASSWORD . '\' ' . DB_NAME . ' | gzip > ' . $backupFile;
system($command);

?>