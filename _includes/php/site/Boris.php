<?php
/**
 * This class controls the main functionality for the Boris application
 *
 * Boris : Localhost Browser
 * A Localhost browser that enables you to quickly look through all the files on your local web server
 * Any suggestions, comments, compliments and complaints happily received.
 *
 */

// import filetype helper class
include_once(INCLUDE_PATH."/_includes/php/site/FileHelper.php");
include_once(INCLUDE_PATH."/_includes/php/site/PageBuilder.php");
include_once(INCLUDE_PATH."/_includes/php/site/RevisionCheck.php");
include_once(INCLUDE_PATH."/_includes/php/lib/php4/singleton.php");
include_once(INCLUDE_PATH."/_includes/php/lib/localization/Localization.php");

/**
 * This class is responsible for the main functionality of the Boris application
 *
 */
class Boris {

	/**
	 * A list of filenames & directory names to ignore
	 *
	 * @var array $ignorelist The names to skip over
	 */
	var $ignorelist = array (
		'.',
		'..',
		'.svn',
		'CVS',
		'.DS_Store',
		'_htaccess',
		'.htaccess',
		'_htpasswd',
		'.htpasswd',
		'Thumbs.db'
	);


	/**
	 * A list of plain text file types that are preview-able
	 *
	 * @var array $previewableTextFiletypes The file types that can be previewed
	 */
	var  $previewableTextFiletypes = array (
		'htm',
		'html',
		'shtml',
		'php',
		'inc',
		'as',
		'js',
		'css',
		'txt',
		'sql',
		'xml'
	);


	/**
	 * A list of image file types that are preview-able
	 *
	 * @var array $previewableImageFiletypes The file types that can be previewed
	 */
	var  $previewableImageFiletypes = array (
		'jpg',
		'jpeg',
		'gif',
		'png',
		'bmp'
	);


    /**
    * A list of possible index filenames
    *
    * @var array $indexFiles The common names of index files
    */
	var  $indexFiles = array(
		'index.html',
		'index.htm',
		'index.php'
	);



	/**
    * A list of all text strings for the current language
    *
    * @var array $strings All text strings
    */
	var  $strings;




	/**
	 * Constructor
	 *
	 */
	function __construct() {
		  date_default_timezone_set('Europe/London');
	}



	/**
	 * Retrieve the list of directories within the root directory, for the left-hand navigation
	 *
     * @param string $path The server path to look for files in
	 * @return array $tabs Associative array of all tabs, with paths
	 * @access public
	 */
	 function getTabs($path) {

		// start the tabs array, with a home tab
		$tabs = array(
			"home"=>"./"
		);

		// open the root directory
		if (is_dir($path)) {

            // loop through the contents
            $dh  = opendir($path);
            while (false !== ($file = readdir($dh))) {

                // condition : check if it is a directory, not in the ignore list, and not a mac ._ file
				if (is_dir($path.$file) && !in_array($file, $this->ignorelist) && (strpos($file, "._") !== 0)) {

					// add to tabs array
					$tabs[$file] = $path . $file . "/";
				}
			}
		}

		// return results
		return $tabs;
	}



	/**
	 * Create a list of all files within a specific directory
	 *
     * @param string $path The server path to look for files in
	 * @return array $fileList Array of all files, with file/directory details
	 * @access public
	 */
	function createFileList($args="") {

        // calculate the include path - takes you back from this file to the boris top level
	    $includepath = "../../../";

	    // calculate the path, based on whether one has been sent through
        if (isset($args['path'])) {
            $path = $includepath.$args['indexRootPath'].$args['path'];

        // no path sent through
        } else {
            $path = INDEX_ROOT_PATH;
        }

		//$this -> strings = Localization::getInstance();
		$this->strings =& singleton('Localization');

		// temporary arrays to hold separate file and directory content
		$filelist = array();
		$directorylist = array();

		// get the ignore list, in local scope (can't use $this-> later on)
		$ignorelist = $this->ignorelist;

		// Open directory and read contents
		if (is_dir($path)) {

			// loop through the contents
            //$dh  = opendir($path);
            //while (false !== ($file = readdir($dh))) {
            $dirContent = scandir($path);

			foreach($dirContent as $key => $file) {

				// skip over any files in the ignore list, and mac-only files starting with ._
				if (!in_array($file, $ignorelist) && (strpos($file, "._") !== 0)) {

					// set a return path, for link insertion
					$returnpath = str_replace('../', '', $path);
					$returnpath = str_replace('./', '', $returnpath);
					if (substr($returnpath, 0, 1) == '/') {
					    $returnpath  = "." . $returnpath;
					}

					// condition : if it is a directory, add to dir list array
					if (is_dir($path.$file)) {

						$directorylist[] = array(
							"path" => $returnpath,
							"file" => $file,
							"filenameclean" => $file,
							"filetype" => 'directory',
							"displaytext" => $this->strings->getString('directory'),
							"date" => date("M d Y H:i", filemtime($path.$file."")),
							"isExtLink" => $this->checkDirectoryForIndex($path.$file),
							"isPreviewableText" => false,
							"isPreviewableImage" => false,
							"filecount" => $this->countRelevantFiles($path.$file),
							"filesize" => 0
						);

                    // file, add to file array
					} else {

						$filelist[] = array(
							"path" => $returnpath,
							"file" => $file,
							"filenameclean" => $file,
							"filetype" => FileHelper::getFileType($path.$file) . " file",
							"displaytext" => FileHelper::getFileType($path.$file) . " " . $this->strings->getString('file'),
							"date" => date("M d Y H:i", filemtime($path.$file."")),
							"isExtLink" => false,
							"isPreviewableText" => $this->checkIfFileIsPreviewable($path.$file, 'text'),
							"isPreviewableImage" => $this->checkIfFileIsPreviewable($path.$file, 'image'),
							"filecount" => 0,
							"filesize" => FileHelper::getFileSize(filesize($path.$file))
						);
					}
				}
			}
		}

		// merge file and directory lists
		$finalList = array_merge($directorylist, $filelist);
		//return $finalList;


		// loop through each file
		foreach ($finalList as $key => $value) {

			// condition : add trailing slash for directories
			$trailingslash = ($value['filetype'] == 'directory' ) ? '/' : '';

			// condition : if it is a directory, display count of subfiles
			if ($value['filetype'] == 'directory' ) {
				$fileending = ($value['filecount'] == 1) ? $this->strings->getString('file') : $this->strings->getString('files');
				$filedetails = ' ('.$value['filecount'].' '.$fileending.')';

			// else, if it is a file, display file size
			} else {
				$filedetails = ' ('.$value['filesize'].')';
			}

			// condition : if the file is an external link, add class to link for bg image
			$launchlink = ($value['isExtLink'] === true) ? ' <span class="site">'.$this->strings->getString('launch_site').'</span>' : '';

			// condition : if the file is previewable (image/text) then display option
			$previewlink = ($value['isPreviewableText'] === true) ? ' <span class="code">'.$this->strings->getString('view_code').'</span>' : '';
			if (!$previewlink) {
				$previewlink = ($value['isPreviewableImage'] === true) ? ' <span class="image">'.$this->strings->getString('preview_image').'</span>' : '';
			}


			// create the html for each project
			echo '
				<p class="' . $value['filetype'].'" id="proj_' . $value['file'] . '">
					<a href="'.$value['path'] . $value['file'] . $trailingslash . '" class="clearfix">
						<strong>' . $value['filenameclean'] . '</strong>
						<span class="preview">';

			// condition : if a link is previewable, display link, otherwise leave gap
			if (!empty($previewlink) || !empty($launchlink)) {
				echo $launchlink . $previewlink;
			}

			// non-breaking space to give container content for Safari
			echo '&nbsp;</span>
						<span class="type">' . $value['displaytext'] . '</span>
						<span class="details">' . $filedetails . '</span>
		 				<span class="date">(' . $value['date'] . ')</span>
					</a>
				</p>
			';
		}

		return false;
	}



	/**
	 * count the number of files in a directory, not including the list of ignorable files
	 *
	 * @param string $path The server path to look for files in
	 * @return int $count The number of relevant files
	 * @access private
	 */
	function countRelevantFiles($path) {

		// start a count
		$count = 0;

		// open the directory
		if (is_dir($path)) {

            // loop through all files, checking if we should count the current one
            //$dh  = opendir($path);
            //while (false !== ($file = readdir($dh))) {
            $dirContent = scandir($path);
			foreach($dirContent as $key => $file) {

				if (!in_array($file, $this->ignorelist) && (strpos($file, "._") !== 0)) {
					$count++;
				}
			}
		}

		// return the result
		return $count;
	}



	/**
	 * function to check a directory for an index file
	 * @param string $dir The server path to look for files in
	 * @return boolean is there an index file?
	 * @access private
	 */
	function checkDirectoryForIndex($dir) {

		//search for an index file in the directory
	    //$dh  = opendir($dir);
        //while (false !== ($file = readdir($dh))) {
        //    $fileArray[] = $file;
        //}
        $fileArray = array();
        $dirContent = scandir($dir);
		foreach($dirContent as $key => $file) {
		    $fileArray[] = $file;
		}

		foreach ($this->indexFiles as $indexFile) {
			// if an index file is found, break loop
			if (in_array($indexFile, $fileArray)) {
				return true;
			}
		}

		// no index file found
		return false;
	}



	/**
	 * function to check whether a specific file is previewable
	 * @param string $dir The server path to the file
	 * @return boolean is it previewable?
	 * @access private
	 */
	function checkIfFileIsPreviewable($dir, $type) {

		// check which type of preview we're talking about, text or images
		$previewtype = ($type == "text") ? $this->previewableTextFiletypes : $this->previewableImageFiletypes;

		// get the filetype
		$filetype = FileHelper::getFileType($dir);

		// condition : is the filetype previewable?
		if (in_array($filetype, $previewtype)) {
			return true;
		} else {
			return false;
		}
	}



	/**
	 * function to process a text file on the server, and return processed results
	 * @param string $dir The server path to the file
	 * @return string the text string
	 * @access public
	 */
	function processAjaxFileRequest($args) {

		$path = $args['path'];
		if (!$path) return false;

		// cleanse path - remove all slashes before filename, and change to an internal link so no pre-processing occurs
		$path = strstr($path, 'http://'.$_SERVER['HTTP_HOST'].'/');
		$path = str_replace('http://'.$_SERVER['HTTP_HOST'].'/', '', $path);

		// convert url to filesystem path
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		$path = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $path;


		// import GeSHi library, and display the source code
		// see - http://qbnz.com/highlighter/geshi-doc.html
		include_once(INCLUDE_PATH."/_includes/php/lib/geshi/geshi.php");
		$geshi = new GeSHi();
		$geshi->load_from_file($path);
		$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
		$geshi->set_line_style('background: #fcfcfc;', 'background: #f0f0f0;');
		$geshi->set_header_type(GESHI_HEADER_PRE_TABLE);

		// return parsed code, or error
		echo $geshi->parse_code();
		echo $geshi->error();
		return false;
	}



	/**
	 * function to create the page breadcrumb
	 *
	 * @return array $breadstring the path to the current directory
	 * @access private
	 */
	function createBreadCrumb() {

		//calculate path to server for breadcrumb
		$path = "http://" . $_SERVER['HTTP_HOST'] . "/";

		//start the breadcrumb string with the server
		$breadstring = array(
			array(
				"name" => $_SERVER['HTTP_HOST'],
				"path" => $path
			)
		);

		//explode the path to the current file
		$filetrail = explode("/", $_SERVER['REQUEST_URI']);

		//calculate length of string
		$pathlength = count($filetrail);

		//cycle through URI and create breadcrumb
		for ($x=0; $x < $pathlength; $x++) {
			$path.= $filetrail[$x] . "/";
			$breadstring[] = array(
				"name" => $filetrail[$x],
				"path" => $path
			);
		}

        // return final breadcrumb
		return $breadstring;
	}


	/**
	 * Get the localhost address, and if possible add in a link to a publicly viewable URL
	 * @return string the localhost string to enter into the page footer
	 */
	function getLocalhost() {

	    // calculate 'localhost' value
	    $localhost = $_SERVER['HTTP_HOST'];

	    // condition : if localhost is a value that can't be shared, offer switch to IP
	    if (($localhost == 'localhost' || $localhost == '127.0.0.1') && strpos($_SERVER['REMOTE_ADDR'], "192.168.") !== false) {

	        // create IP address link
	        $ip = 'http://' . $_SERVER['REMOTE_ADDR'] . '/';

            return '<dd><a id="localhost" href="'.$ip.'" title="'.$this->strings->getString('localhost_switch').$_SERVER['REMOTE_ADDR'].'">'.$localhost.'</a></dd>';

        // set a standard return value to use when conditions above aren't met
	    } else {
	        return '<dd id="localhost">'.$localhost.'</dd>';
	    }
	 }



	/**
	  * Perform an AJAX revision version check after page load
	  * @return string the returned revision check string
	  */
	function checkRevision(){
	    //include_once(INCLUDE_PATH."/_includes/php/site/RevisionCheck.php");
      	//$rc = new RevisionCheck();
      	//$revision = $rc->checkRevision();
      	//echo $revision;
      	return false;
	}
}

?>