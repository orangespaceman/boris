<?php
/**
 * This class deals with the current SVN revision number of Boris
 * The number of the checked-out version is compared with
 * the current SVN revision number in google code, and a message
 * is created for display in the footer
 *
 * $Rev: 46 $
 *
 */
include_once(INCLUDE_PATH."/_includes/php/lib/php4/singleton.php");
include_once(INCLUDE_PATH."/_includes/php/lib/localization/Localization.php");

class RevisionCheck {

	/**
	 * The local revision number, automatically generated/updated by SVN
	 *
	 * @var string $localRevisionString The local revision number string
	 */
	var $localRevisionString = '$Rev: 46 $';


	/**
	 * The local revision number
	 *
	 * @var int $localRevision The local revision number
	 */
	var $localRevision;

	/**
	 * The live revision number
	 *
	 * @var int $liveRevision The live revision number
	 */
	var $liveRevision;

	/**
	 * Init the revision checker, get the two values
	 *
	 * @return void
	 */
	function __construct()
	{
		// extract the local revision number from the generated string
		$this->localRevision = $this->getLocalRevision();

		//Language strings
		//$this -> strings = Localization::getInstance();
		$this -> strings =& singleton('Localization');
	}


	/**
	 * Check the revision number
	 *
	 * @return string notice whether you are running the latest version of Boris
	 */
	function checkRevision()
	{
		// retreive the current number from Google Code
		$this->liveRevision = $this->getLiveRevision();

		// ensure that you could open the google code URL and access the latest revision number
		if (!is_int($this->liveRevision)) {

			return $this->localRevision . " (".$this -> strings -> getString('running_offline').")";

		} else {

			// condition : return a string based upon whether it is up-to-date or not
			if ($this->localRevision == $this->liveRevision) {
				return $this->localRevision;
			} else {
				return $this->localRevision . " (".$this -> strings -> getString('update_available').")";
			}
		}

		// debug only
		//echo "Local Revision: " . $this->localRevision . ", Live Revision: ". $this->liveRevision;
	}



	/**
	 * Get the local Boris SVN revision number
	 * @return int the local revision number
	 * @access private
	 */
	function getLocalRevision() {
		return intval(substr($this->localRevisionString, 6, -2));
	}



	/**
	 * Get the live Boris SVN revision number
	 * @return int the live revision number
	 * @access private
	 */
	function getLiveRevision() {

		// condition : get live version, only if curl is installed
		if (function_exists('curl_init') && (int)PHP_VERSION > 4) {

			// set url of html page containing latest revision number
			$liveRevisionUrl = "http://labs.petegoodman.com/boris_localhost_browser/";

			// retrieve xml through curl
			$ch = curl_init($liveRevisionUrl);
			curl_setopt($ch, CURLOPT_URL, $liveRevisionUrl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$xml = curl_exec($ch);
			curl_close($ch);

			// load in the xml stream
			$dom = new DomDocument();
			@$dom->loadXML($xml);

			// get revision number containing element
			$revisionNumber = $dom->getElementsByTagName("var");

			// condition : if no data found, return an error string
			if ($revisionNumber->length < 1) {
				return false;
			} else {

				// get revision number
				$latestUpdate = $revisionNumber->item(0);
				return intval($latestUpdate->textContent);
			}
		} else {
			return false;
		}
	}
}
?>