#summary How to get Boris running on your machine

It should be straightforward to get Boris running on your local machine.  Download the latest set of files from SVN.  

You will see a single file called _index.php_ and a directory called _includes_.  These can be placed directly in the site root.  Alternatively, they can be placed within a subdirectory.  If you choose to do this,  you just need to create a new index.php file in the root, and put in the following few lines of code:

{{{
<?php
    		
    // set the location of the main boris directory
    // Leave off trailing slash
    define("INCLUDE_PATH", "./labs/boris/trunk");
	
    // set the location of the directory you want to index from
    define("INDEX_ROOT_PATH", "../../../");
	
    // set the path for the tabs to index
    define("TAB_PATH", "./");
	
    // include (and start) boris localhost browser
    include_once(INCLUDE_PATH."/index.php");
?>
}}}


===System Requirements===
Boris should work with most flavours of PHP4/5