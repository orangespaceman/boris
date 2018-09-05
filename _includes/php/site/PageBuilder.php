<?php
/**
 * This class builds the main html header and footer for each page
 *
 * Boris : Localhost Browser
 * A Localhost browser that enables you to quickly look through all the files on your local web server
 * Any suggestions, comments, compliments and complaints happily received.
 *
 */

/**
 * A class to generate the HTML for common page elements.
 */
include_once(INCLUDE_PATH."/_includes/php/lib/php4/singleton.php");
include_once(INCLUDE_PATH."/_includes/php/lib/localization/Localization.php");

class PageBuilder {


	/**
	 * Constructor
	 *
	 */
    function __construct() {

		//$this -> strings = Localization::getInstance();
		$this -> strings =& singleton('Localization');

    	// condition : set cookies for options, if set
    	if (isset($_GET['type'])) {
            switch($_GET['type']) {
                case "view":
                    setcookie('optionsView', $_GET['layout'], time()+60*60*24*30, '/', false, 0);
                    header("Location : ./");
                break;
                case "tabs":
                    setcookie('optionsTabs', $_GET['display'], time()+60*60*24*30, '/', false, 0);
                    header("Location : ./");
                break;
                case "transitions":
                    setcookie('optionsTransitions', $_GET['transitions'], time()+60*60*24*30, '/', false, 0);
                    header("Location : ./");
                break;
				/*case "language":
					$this -> strings -> setLanguage( $_GET['language'] );
					header('Location : ./');
				break;*/
            }
    	}

	}


	/**
	 * Builds the top of the HTML page, including meta data and linking in
	 * javascript functionality and the style sheets.
	 *
	 * @return void
	 * @access public
	 */
    function buildPageTop() {

		// create skin includes
		$skinIncludes = $this->buildSkinIncludes();

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Boris: '.$this->strings->getString('localhost_browser').'</title>

	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="MSSmartTagsPreventParsing" content="true" />
	<link rel="shortcut icon" href="'.INCLUDE_PATH.'/_includes/img/favicon.ico" />

	<meta name="description" content="Boris: '.$this->strings->getString('metadescription').'" />
	<meta name="keywords" content="" />

	<style media="screen" type="text/css">
		@import "'.INCLUDE_PATH.'/_includes/css/global.css";
		@import "'.INCLUDE_PATH.'/_includes/css/thickbox.css";
		';

		// loop through an insert each skin
		foreach($skinIncludes as $skin) {
			echo '
		@import "'.INCLUDE_PATH.'/_includes/skins/'.$skin.'/css/skin.css";
			';
		}

		echo '
	</style>

	<!--[if lt IE 7]>
		<link rel="stylesheet" type="text/css" href="'.INCLUDE_PATH.'/_includes/css/ie6.css" media="screen" />
	<![endif]-->
	<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="'.INCLUDE_PATH.'/_includes/css/ie7.css" media="screen" />
	<![endif]-->

	<script type="text/javascript" src="'.INCLUDE_PATH.'/_includes/js/lib/swfaddress/swfaddress.js"></script>
	<script type="text/javascript" src="'.INCLUDE_PATH.'/_includes/js/lib/swfaddress/swfaddress-optimizer.js"></script>

	<script type="text/javascript" src="'.INCLUDE_PATH.'/_includes/js/lib/jquery/jquery-1.2.6.min.js"></script>
	<script type="text/javascript" src="'.INCLUDE_PATH.'/_includes/js/site/boris.js"></script>

	<script id="libman" type="text/javascript" src="'.INCLUDE_PATH.'/_includes/js/lib/libman/libman.js"></script>
	<script type="text/javascript" src="'.INCLUDE_PATH.'/_includes/js/lib/cookies/cookies.js"></script>

	<script type="text/javascript" src="'.INCLUDE_PATH.'/_includes/js/lib/thickbox/thickbox.js"></script>
	<script type="text/javascript" src="'.INCLUDE_PATH.'/_includes/js/lib/suckerfish/sfhover.js"></script>

</head>
		';

		// condition : if a skin cookie is set, display that skin by default
		$bodyClass = (isset($_COOKIE['optionsColourscheme'])) ? " ".$_COOKIE['optionsColourscheme'] : "";

		echo '
<body id="home" class="home'.$bodyClass.'">

	<div id="wrapper">

		<div id="header" class="clearfix">
			<h1><a href="/">Boris: '.$this->strings->getString('localhost_browser').'</a></h1>
			<ul id="crumb">
				<li>'.$_SERVER['HTTP_HOST'].'</li>
			</ul>
		</div>
		<div id="options" class="clearfix">
		    <ul>
		        <li id="options-view"><a href="#">'.$this->strings->getString('layout').'</a>
		            <ul>
		            ';

			        // condition : search for a cookie for the project list layout type
		            if (isset($_COOKIE['optionsView']) && $_COOKIE['optionsView'] == "grid") {
		                echo '
    		                <li id="options-view-grid"><a href="?layout=grid&amp;type=view"><strong>'.$this->strings->getString('grid').'</strong></a></li>
    		                <li id="options-view-list"><a href="?layout=list&amp;type=view">'.$this->strings->getString('list').'</a></li>
		                ';
		            } else {
		                echo '
    		                <li id="options-view-grid"><a href="?layout=grid&amp;type=view">'.$this->strings->getString('grid').'</a></li>
    		                <li id="options-view-list"><a href="?layout=list&amp;type=view"><strong>'.$this->strings->getString('list').'</strong></a></li>
		                ';
		            }

		            echo '
		            </ul>
		        </li>
		        <li id="options-tabs"><a href="#">'.$this->strings->getString('tabs').'</a>
		            <ul>
                    ';

    			     // condition : search for a cookie for the project list layout type
    		         if (isset($_COOKIE['optionsTabs']) && $_COOKIE['optionsTabs'] == "hide") {
    		            echo '
                            <li id="options-tabs-on"><a href="?display=show&amp;type=tabs">'.$this->strings->getString('on').'</a></li>
                            <li id="options-tabs-off"><a href="?display=hide&amp;type=tabs"><strong>'.$this->strings->getString('off').'</strong></a></li>
                        ';
                    } else {
                        echo '
                            <li id="options-tabs-on"><a href="?display=show&amp;type=tabs"><strong>'.$this->strings->getString('on').'</strong></a></li>
                            <li id="options-tabs-off"><a href="?display=hide&amp;type=tabs">'.$this->strings->getString('off').'</a></li>
                        ';
                    }

                    echo '
                    </ul>
                </li>
                <li id="options-transitions"><a href="#">'.$this->strings->getString('transitions').'</a>
		            <ul>
		            ';

		             // condition : search for a cookie for the project list layout type
       		         if (isset($_COOKIE['optionsTransitions']) && $_COOKIE['optionsTransitions'] == "fast") {
       		            echo '
                            <li id="options-transitions-normal"><a href="?transitions=normal&amp;type=transitions"><strong>'.$this->strings->getString('normal').'</strong></a></li>
                            <li id="options-transitions-fast"><a href="?transitions=fast&amp;type=transitions">'.$this->strings->getString('fast').'</a></li>
                            <li id="options-transitions-off"><a href="?transitions=off&amp;type=transitions">'.$this->strings->getString('off').'</a></li>

                        ';
                    } else if (isset($_COOKIE['optionsTransitions']) && $_COOKIE['optionsTransitions'] == "off") {
                        echo '
                            <li id="options-transitions-normal"><a href="?transitions=normal&amp;type=transitions">'.$this->strings->getString('normal').'</a></li>
                            <li id="options-transitions-fast"><a href="?transitions=fast&amp;type=transitions">'.$this->strings->getString('fast').'</a></li>
                            <li id="options-transitions-off"><a href="?transitions=off&amp;type=transitions"><strong>'.$this->strings->getString('off').'</strong></a></li>
                        ';
                    } else {
                       echo '
                           <li id="options-transitions-normal"><a href="?transitions=normal&amp;type=transitions">'.$this->strings->getString('normal').'</a></li>
                           <li id="options-transitions-fast"><a href="?transitions=fast&amp;type=transitions"><strong>'.$this->strings->getString('fast').'</strong></a></li>
                           <li id="options-transitions-off"><a href="?transitions=off&amp;type=transitions">'.$this->strings->getString('off').'</a></li>
                        ';
                    }

                        echo '
                    </ul>
                </li>
                <li id="options-colourscheme"><a href="#">'.$this->strings->getString('colour_scheme').'</a>
		            <ul id="skinselect">
		            ';

			        // condition : search for a cookie for the colour scheme
		            // loop through an insert each skin
					foreach($skinIncludes as $skin) {
						if (isset($_COOKIE['optionsColourscheme']) && $_COOKIE['optionsColourscheme'] == $skin) {
							$strong = '<strong>';
							$closestrong = '</strong>';
						} else if (isset($_COOKIE['optionsColourscheme']) && $_COOKIE['optionsColourscheme'] != $skin) {
							$strong = $closestrong = '';
						} else if (!isset($_COOKIE['optionsColourscheme']) && $skin == 'standard') {
							$strong = '<strong>';
							$closestrong = '</strong>';
						} else {
							$strong = "";
							$closestrong = "";
						}

						echo '
							<li id="options-colourscheme-'.$skin.'"><a href="?scheme='.$skin.'&amp;type=colourscheme">'.$strong.ucfirst($skin).$closestrong.'</a></li>
						';
					}

		            echo '
		            </ul>
		        </li>
		';

		echo '

		<li id="options-language"><a href="#">'.$this->strings->getString('language').'</a>
		            <ul id="languageselect">
		            ';

					$languages = $this -> strings -> getLanguages();
					foreach($languages as $language){
						if( isset ( $_COOKIE['localizationLanguage'] ) ){
							if( $_COOKIE['localizationLanguage'] == $language[0] ){
								$strong = '<strong>';
								$closestrong = '</strong>';
							} else {
								$strong = "";
								$closestrong = "";
							}
						} else {
							if( $language[0] == 'en_EN' ){
								$strong = '<strong>';
								$closestrong = '</strong>';
							} else {
								$strong = "";
								$closestrong = "";
							}
						}
						echo '
							<li id="options-language-'.$language[0].'"><a href="?language='.$language[0].'&amp;type=language">'.$strong.$language[1].$closestrong.'</a></li>
						';
					}

		            echo '
		            </ul>
		        </li>
            </ul>
		</div>
		';
}


	/**
	 * Renders bottom html
	 *
	 * @return void
	 * @access public
	 */
	function buildPageBottom() {
		echo $this->strings->getJavscriptStrings();
		echo '
        <script type="text/javascript">
        // <![CDATA[
            var indexRootPath = "'.INDEX_ROOT_PATH.'";
        // ]]>
        </script>
	</div>
</body>
</html>';
	}


	/**
	 * Retrieve all skins from the skins directory
	 *
	 * @return array $return An array of potential skins
	 * @access private
	 */
	 function buildSkinIncludes() {

		// create an array to return
		$return = array();

		// open the directory
		//$dirContent = scandir(INCLUDE_PATH.'/_includes/skins/');

		// loop through the contents
        $dh  = opendir(INCLUDE_PATH.'/_includes/skins/');
        while (false !== ($file = readdir($dh))) {


        // loop through all files, checking if we should use the current one
		//foreach($dirContent as $key => $file) {
			if (strpos($file, ".") !== 0) {
				$return[] = $file;
			}
		}

		// return the result
		return $return;
	}
}

?>