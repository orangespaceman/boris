/**
 * Libman: a Library Manager tool for JS
 * Includes js files and calculates the path to the web site root, for file inclusion
 *
 * @author julio ruiz - 
 * @author michael allanson - 
 * @author pete goodman - petegoodman.com
 * @author richard hallows - richardhallows.com
 */
LibMan = {
	
	// String : The path to libman.js
	path: null,


	/**
	 * Calculates and sets the relative path to libman.js
	 *
	 * @return void  
	 */
	calculatePath: function() {
		// condition : is object var already set?
		if (this.path == null) {
			// get path from LibraryManager javascript include string
			var libman = document.getElementById("libman");
			if (!libman) return false;
			
			// remove the filename before setting the path 
			this.path = libman.src.replace(/libman\.js(\?.*)?$/,'');
		}
	},
	
	/**
	 * Includes a js file
	 * 
	 * @param array libFiles paths to files to include, relative to libman.js
	 *
	 * @return void  
	 */
	require: function(libFiles) {
		// insert each script
		for (var i=0; i < libFiles.length; i++) {
			// match strings starting with 'http://'
			var pattern = /^http:\/\//;
			
			// if libFiles[i] matches pattern, path is absolute, otherwise it's relative
			libFiles[i] = (pattern.test(libFiles[i])) ? libFiles[i] : this.path + libFiles[i];
			document.write('<script type="text/javascript" src="'+ libFiles[i] +'"></script>');
		};
	}
}

	
// on DOM ready
$(function() {

    // calculate file path
    LibMan.calculatePath();

    // require any additional files
    /*
    LibMan.require([ 
       LibMan.path + "/file.js",
       LibMan.path + "/file.js" 
    ]);
    */
});


// stop firebug console.log errors
if(!window.console)     { window.console = {} };
if(!window.console.log) { window.console.log = function(){} };