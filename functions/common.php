<?php

// from legolas558 d0t users dot sf dot net at http://www.php.net/is_writable
function BWPS_can_write($path) {		 
	
	if ($path{strlen($path)-1} == '/') {
		return BWPS_can_write($path.uniqid(mt_rand()).'.tmp');
	} elseif (is_dir($path)) {
		return BWPS_can_write($path.'/'.uniqid(mt_rand()).'.tmp');
	}
	
	$rm = file_exists($path);
	$f = @fopen($path, 'a');
	
	if ($f===false) {
		return false;
	}
	
	fclose($f);
	
	if (!$rm) {
		unlink($path);
	}
	
	return true;
}

function BWPS_remove_section( $filename, $marker ) {
	if (!file_exists( $filename ) || BWPS_can_write( $filename ) ) {
		if (!file_exists( $filename ) ) {
			return '';
		} else {
			$markerdata = explode( "\n", implode( '', file( $filename ) ) );
		}

		$f = fopen( $filename, 'w' );
		$foundit = false;
		if ( $markerdata ) {
			$state = true;
			foreach ( $markerdata as $n => $markerline ) {
				if (strpos($markerline, '# BEGIN ' . $marker) !== false)
					$state = false;
				if ( $state ) {
					if ( $n + 1 < count( $markerdata ) )
						fwrite( $f, "{$markerline}\n" );
					else
						fwrite( $f, "{$markerline}" );
				}
				if (strpos($markerline, '# END ' . $marker) !== false) {
					$state = true;
				}
			}
		}
		return true;
	} else {
		return false;
	}
}
