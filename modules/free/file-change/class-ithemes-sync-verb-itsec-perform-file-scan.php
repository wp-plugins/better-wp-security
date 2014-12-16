<?php

class Ithemes_Sync_Verb_ITSEC_Perform_File_Scan extends Ithemes_Sync_Verb {

	public static $name = 'itsec-perform-file-scan';
	public static $description = 'Perform a one-time file scan';

	public $default_arguments = array();

	public function run( $arguments ) {

		$module = new ITSEC_File_Change();
		$module->run();

		$response = $module->execute_file_check( false, true );

		return $response;

	}

}