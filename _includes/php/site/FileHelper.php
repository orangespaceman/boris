<?php
/**
 * This static class offers some helpful File-related methods
 *
 * @author pete goodman
 */


/**
 * This class contains some useful methods for dealing with files
 *
 */
class FileHelper {


	/**
	 * Create a nice readable filesize from the number of bytes in a file
	 *
	 * @param int $size the size in bytes
	 * @param string $retstring
	 *
	 * @return string the size in nice words
	 */
	static function getFileSize($size, $retstring = null)
	{
	    $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	    if ($retstring === null) { $retstring = '%01.2f %s'; }
	    $lastsizestring = end($sizes);
	    foreach ($sizes as $sizestring) {
	            if ($size < 1024) { break; }
	            if ($sizestring != $lastsizestring) { $size /= 1024; }
	    }
	    if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
	    return sprintf($retstring, $size, $sizestring);
	}


	/**
	 * Function to find a file type for a given filename
	 *
	 * @param string $file filename/path
	 *
	 */
    static function getFileType($file="") {

		// get file name
		$filearray = explode("/", $file);
		$filename = array_pop($filearray);

		// condition : if no file extension, return
		if(strpos($filename, ".") === false) return false;

		// get file extension
		$filenamearray = explode(".", $filename);
		$extension = $filenamearray[(count($filenamearray) - 1)];
		return $extension;

	}
}

?>