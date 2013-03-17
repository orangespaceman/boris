<?php
/**
 * Boris : Localhost Browser
 *
 * A Localhost browser that enables you to quickly look through all the files on your local web server
 * Any suggestions, comments, compliments and complaints happily received.
 *
 * http://github.com/thegingerbloke/boris/
 *
 * @author Pete Goodman - pete@petegoodman.com
 */
    // condition : display php info?  (Link in footer)
	if (isset($_GET['phpinfo'])) {
	    phpinfo();
	    exit();
	}

	// define INCLUDE_PATH for file imports - check PATH for legacy
	if (!defined('INCLUDE_PATH')) {
	    define('INCLUDE_PATH', ".");
    }

    // define Tab indexing path
	if (!defined('TAB_PATH')) {
	    define('TAB_PATH', "./");
    }


    // define WEB_ROOT_PATH
	if (!defined('INDEX_ROOT_PATH')) {
	    define('INDEX_ROOT_PATH', "./");
    }

	// import and use the localization class
	include_once(INCLUDE_PATH."/_includes/php/lib/php4/singleton.php");
	include_once(INCLUDE_PATH."/_includes/php/lib/localization/Localization.php");
	//$strings = Localization::getInstance();
	$strings =& singleton('Localization');

	// initialise the Localhost browser class
	include_once(INCLUDE_PATH."/_includes/php/site/Boris.php");
	$boris = new Boris();

	// get the initial left hand nav tabs
	$tabs = $boris->getTabs(TAB_PATH);

	// condition : get the currently selected tab (non-js only), or select first tab if none selected previously
	if(isset($_GET['tab'])) {
		// check the tab exists
		if (array_key_exists($_GET['tab'], $tabs)) {
			$selectedtab = $tabs[$_GET['tab']];
		} else {
			$selectedtab = $tabs['home'];
		}
	} else {
		$selectedtab = $tabs['home'];
	}

	// optional : set a INCLUDE_PATH for a phpMyAdmin directory, to have it display in the site footer, or comment out to hide
	$phpMyAdminDir = "./phpMyAdmin/";

	// set layout variables, from cookies
    $tabsview = (isset($_COOKIE['optionsTabs']) && $_COOKIE['optionsTabs'] == "hide") ? " full" : "";
    $optionsview = (isset($_COOKIE['optionsView']) && $_COOKIE['optionsView'] == "grid") ? "grid" : "list";

	// retreive Boris version number, for footer
	//$rc = new RevisionCheck();
	//$revision = $rc->getLocalRevision();

	// build the top of the page
	$pagebuilder = new PageBuilder();
	$pagebuilder->buildPageTop();

?>

	<div id="content" class="clearfix">

		<div id="projects" class="clearfix<?php echo $tabsview; ?>">

			<div id="projectlist" class="<?php echo $optionsview; ?>">
				<?php
					// retrieve and insert the initial file list (echo inside method, for JS)
					$filelist = $boris->createFileList();
				?>
			</div>

			<ul id="projecttabs"><?php echo '<!--'; //IE6 fix ?>
				<?php
					// loop through tabs
					foreach ($tabs as $key => $tab) {

						// condition : check if this is the currently selected tab
						$selected = ($tab == $selectedtab) ? ' class="selected"' : "";

						echo '
				--><li><a href="./?tab='.$key.'"'.$selected.' rel="'.$tab.'">'.$key.'</a></li><!--
						';
					}
				?>
			--></ul>
		</div>

		<div id="footer" class="clearfix">
			<dl id="server" class="horiznavlist clearfix">
				<dt class="first"><?php echo $strings->getString('server'); ?>: </dt>
			    <?php
			        // insert localhost value
			        $localhost = $boris->getLocalhost();
			        echo $localhost;
			    ?>
				<dd>
					<?php
						// Insert apache version number
						if (function_exists('apache_get_version')) {
						    $apache_version = explode('PHP', apache_get_version());
						    echo $apache_version[0];
						}

						// to do, calculate MySQL server version (if present)
					?>
				</dd>
				<dd>PHP: <?php echo phpversion(); ?></dd>
                <?php
                    // condition : if there is a phpMyAdmin directory set, display link
                    if(isset($phpMyAdminDir)) {
                        echo '
			    <dd><a href="'.$phpMyAdminDir.'">phpMyAdmin</a></dd>
                        ';
                    }
			    ?>
				<dd class="last"><a href="?phpinfo">phpinfo( )</a></dd>
			</dl>
			<p><a href="http://github.com/thegingerbloke/boris/" class="externallink">Boris</a></p>
		</div>
	</div>
<?php
	$pagebuilder->buildPageBottom();
?>