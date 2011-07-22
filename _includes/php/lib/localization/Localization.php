<?php

	/**
	 *	Localization controller class.
	 *	Singleton
	 *
	 *	author : julio ruiz
	 *	date : 10.02.2009
	 *
	 *	php5 only
	 */
	class Localization {
	
		var $instance = NULL;
		
		/**
		 *	Path for language files...
		 */
		var $path = 'languages/';
		
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
			'Thumbs.db'
		);
		
		/**
		 * A list of form variables to ignore
		 *
		 * @var array $ignoreformlist The variablez to strip out
		 */
		var $ignoreformlist = array (
			'meta_name',
			'meta_locale',
			'meta_windowslocale',
			'author',
			'submit'
		);
	
		/**
		 * Main array to hold all languages
		 *
		 * @var array $languages The languages avaialable
		 */
		var $languages = array();
	
		/**
		 *	Test mode off
		 */
		var $testmode = false; 
		
		/**
		 *	Current language
		 */
		var $currentLanguage = 'en_EN';
		
		/* 
		 * PHP4 Constructor initialisation
		 */
		function Localization() {
		    $this->__construct();
		}
		
		
		/**
		 * Constructor
		 * 
		 */
	    function __construct() {
		
			//Get all available languages and build up the languages array
			$path = dirname(__FILE__) . '/' . $this -> path;
			
			// get the ignore list, in local scope (can't use $this-> later on)
			$ignorelist = $this->ignorelist;
		
			if (is_dir($path)) {
				
				// loop through the contents
                $dh  = opendir($path);
                while (false !== ($file = readdir($dh))) {
                    				
					// skip over any files in the ignore list, and mac-only files starting with ._
					if (!in_array($file, $ignorelist) && (strpos($file, "._") !== 0)) {
						//If all is good, load in the language file...
						require_once( $this -> path . $file );
					}
				}
			}
			
			//Set default language is not set in cookie...
			if (!isset($_COOKIE['localizationLanguage'])) {
				setcookie('localizationLanguage', $this->currentLanguage, time()+60*60*24*30, '/', false, 0);
				$this -> setLanguage( $this->currentLanguage );
			} else {
				$this -> setLanguage( $_COOKIE['localizationLanguage'] );
			}
		}
		
		/**
		 *	Return array of all available languages...
		 *	Will return locale and language of each loaded file as an array...
		 */
		 function getLanguages(){
			$ret = array();
			foreach($this -> languages as $language){
				array_push( $ret , array( $language['metadata']['meta_locale'] , $language['metadata']['meta_name'] ) );
			}
			return $ret;
		}
		
		/**
		 *	Return instance of singleton
		 */
		function getInstance() {
			if(!isset($this->instance)){
				$this->$instance = new Localization();
			}		
			return $this->$instance;
		}
		
		/**
		 *	Return localized string....
		 */
		function getString( $str , $lang ='' ){
			//Check if we passed in language, otherwise use the current one...
			$lang = ($lang!=''?$lang:$this->currentLanguage);
			//If testmode, we return the string in curly braces...
			if( $this -> testmode == true ){
				return "{ $str }";
			} else {
				//Or if not in testmode, but cant find the string, again, the string in curly braces...
				if( !isset($this->languages[$lang]['strings'][$str]) ){
					return "{ $str }";
				} else {
					//Or just return the string we wanted...
					return $this->languages[$lang]['strings'][$str];
				}
			}
		}
		
		/**
		 *	Return localized array...
		 *	Loops through all languages and returns a single array with all language translations for the string...
		 *	Might be useful for something...?!!
		 */
		function getStringTranslations( $str ){
			$ret = array();
			foreach($this -> languages as $key => $value){
				$ret[$key] = $this -> getString( $str , $key );
			}
			return $ret;
		}
		
		/**
		 *	Set language
		 *	Takes a locale as parameter and sets the active language string array...
		 */
		function setLanguage( $i ){
			if( isset( $this -> currentLanguage ) ){
				if( $this -> currentLanguage != $i ){
					if( $this -> languages[$i] ){
						$this -> currentLanguage = $i;
						$this -> setLocale();
						return true;
					} 
				}
			}
			return false;
		}
		
		/**
		 *	Set locale
		 */
		function setLocale(){
			//Read locale values from language arrays. There are two, one for unix/mac, the other for windows specifically.
			$locale = $this -> languages[$this->currentLanguage]['metadata']['meta_locale'];
			$windowslocale = $this -> languages[$this->currentLanguage]['metadata']['meta_windowslocale'];
			//Set the locale so all content generation funcions in php will return the right language (strftime, date, etc)...
			setlocale( LC_ALL , $locale, $windowslocale );
		}
		
		/**
		 *	Create language
		 */
		function createLanguageForm( $lang='' ){
			$lang = ($lang!=''?$lang:$this -> currentLanguage);
			//Create form...
			$ret = '
<form action="" method="post" id="localizationForm">
	<fieldset>
		<legend>Meta Data</legend>
		';	
			foreach( $this -> languages [$lang]['metadata'] as $key => $value ){
				$ret .='
		<div class="formelement">
			<label for="'.$key.'">{ '.$key.' } :</label>
			<input type="text" id="'.$key.'" name="'.$key.'" value="'.$value.'" />
		</div>
				';
			}
						
			$ret .= '
		<div class="formelement">
			<label for="author">Author :</label>
			<input type="text" id="author" name="author" value="" />
		</div>
	</fieldset>
	<fieldset>
		<legend>Strings</legend>
		';
			foreach( $this -> languages [$lang]['strings'] as $key => $value ){
				$ret .='
		<div class="formelement">
			<label for="'.$key.'">{ '.$key.' } :</label>
			<input type="text" id="'.$key.'" name="'.$key.'" value="'.$value.'" />
		</div>				
				';
			}		
			$ret .= '
		<div class="formelement">
			<input type="submit" id="submit" name="submit" value="Save Language" />
		</div>
	</fieldset>
</form>
			';
			return $ret;
		}
		
		/**
		 *	Save language form data
		 */
		function saveLanguageForm( $data ){
			//Define locale and see if the file exists or it's new...
			$locale = $data['meta_locale'];
			$file = fopen($this -> path . '/' . $locale .'.php' , 'w+');
			if( $file ){
				//Generate file output...
				$filedata = '<?php

	/**
	 *	Localization Language File
	 *	Language : '.$data['meta_name'].'
	 *	Author : '.$data['author'].'
	 *	Date : '.strftime('%d.%m.%Y').'
	 */

	//The key should be the same as the filename and ideally it is the Unix locale.
	$this -> languages[\''.$data['meta_locale'].'\'] = Array(
			\'metadata\' => Array(
				\'meta_name\'			=>			"'.$data['meta_name'].'",
				\'meta_locale\'			=>			"'.$data['meta_locale'].'",
				\'meta_windowslocale\'	=>			"'.$data['meta_windowslocale'].'"
			),
			/**
			 *	Set strings from here onwards...
			 */
			\'strings\' => Array(
				';
				$strings = $this -> cleanFormData( $data );
				$stringCount = count($strings);
				$x = 1;
				foreach($strings as $key => $value){
					$filedata .= '
				\''.$key.'\'			=>			"'.$value.'"';
					if($x!=$stringCount){
						$filedata .= ',';
					}
					$x++;
				}
				$filedata .= '
			)
		);
	
?>';
				fwrite( $file , $filedata );
				fclose( $file );
				return true;
			}
			return false;
		}
		
		/**
		 *	Returns a form array stripped of all NON string data
		 */		
		function cleanFormData( $data ){
			$cleanArray = array();
			foreach($data as $key => $value){
				if( !in_array($key, $this -> ignoreformlist) ) {
					$cleanArray[$key] = $value;
				}
			}
			return $cleanArray;
		}
		
		/**
		 *	Return a javascript version of the strings array
		 */
        function getJavscriptStrings( $lang='' ){
			$lang = ($lang!=''?$lang:$this -> currentLanguage);
			$ret = '
				<script type="text/javascript">
				// <![CDATA[
					var strings = []';
			
			
			foreach( $this->languages[$lang]['strings'] as $key => $value ){
				$ret .= '
					strings[\''.$key.'\'] = "'.$value.'";';
			}		
			
			$ret .= '
				// ]]>
				</script>
			';
			return $ret;
		}
	}

?>