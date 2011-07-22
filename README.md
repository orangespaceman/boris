==Overview==
boris is a PHP-based localhost browser for the Apache Web Server, allowing you to quickly browse through your local projects.

It has been built with jquery and swfaddress to allow you to move around directories on your local server easily. 

For more information please see the [http://code.google.com/p/boris/wiki/ProjectOverview Project Overview]

==Quick installation== 
Check the files out of [http://code.google.com/p/boris/source/checkout SVN] into a subdirectory of your server root.  In the server root, create a new index.php file, and copy in the following text, updating the value for PATH:

{{{
<?php
    // set the location of the main boris directory
    // Leave off trailing slash
    define("INCLUDE_PATH", "./path/to/boris/trunk");

    // set the location of the directory you want to index from
    define("INDEX_ROOT_PATH", "../../../");
	
    // set the path for the tabs to index
    define("TAB_PATH", "./");
	
    // include (and start) boris localhost browser
    include_once(INCLUDE_PATH."/index.php");
?>
}}}

*In the PATH definition, ensure you have the ./ before the path, and no trailing slash*  

For slightly longer instructions see the [http://code.google.com/p/boris/wiki/Installation installation instructions] in the Wiki


==Screenshot==
http://labs.petegoodman.com/boris_localhost_browser/boris_screenshot.gif

==Bookmarklet==
You can now add a bookmarklet to your browser by dragging a link to your bookmark bar. When looking at any page, site or file on your local server, click on the Borisify link to open that directory through Boris.

More details [http://petegoodman.com/labs/boris-localhost-browser/ here]