# Overview

boris is a PHP-based localhost browser for the Apache Web Server, allowing you to quickly browse through your local projects.

It has been built with jquery and swfaddress to allow you to move around directories on your local server easily.

## Quick installation

Check the files out of Git into a subdirectory of your server root.  In the server root, create a new index.php file, and copy in the following text, updating the value for PATH:

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

*In the PATH definition, ensure you have the ./ before the path, and no trailing slash*

For slightly longer instructions see the installation instructions in the docs folder


## Bookmarklet

You can now add a bookmarklet to your browser by dragging a link to your bookmark bar. When looking at any page, site or file on your local server, click on the Borisify link to open that directory through Boris.
