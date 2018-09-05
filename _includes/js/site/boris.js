/**
 * Javascript for Boris: localhost browser
 *
 * A Localhost browser that enables you to quickly look through all the files on your local web server
 * Any suggestions, comments, compliments and complaints happily received.
 *
 * http://code.google.com/p/boris/
 *
 */


/**
 * on dom load functionality
 */
$(document).ready(function() {

	// add a class to the body element, for JS-only CSS styles
	$('body').addClass('js');

	// hide initial (non-js) displayed projects, to stop flash of content
	$("#projectlist").css({visibility:"hidden"});

	// add click handlers on each tab
	boris.addProjectTabHandlers();

	// add click handlers on the initial list of projects
	boris.addProjectClickHandlers();

	// add listeners for the options menu
	boris.addOptionsHandlers();

    // set up home button
    boris.addHomeButtonHandler();

    // set initial transition time, from cookie
    boris.setInitialTransitionTime();

    // add listeners for keys
    boris.setKeyListeners();

	// add listener for swf address changes, also used as initialiser for page load
	SWFAddress.addEventListener(SWFAddressEvent.CHANGE, function(event){
		// console.log('SWF ADDRESS triggered, updating projects');
		boris.hideProjects(event);
	});
});


/*
 * On full page load functionality
 */
$(window).load(function() {
    // start up the revision checker
    boris.checkRevision();
});


/*
 * Put all functions into boris namespace
 */
var boris = {

    /**
     * @param transitionNormalTime int milliseconds for a standard transition, when enabled
     */
    transitionTimes : {
        normal : {
            transition : 500,
            iteration : 20
        },
        fast : {
            transition : 100,
            iteration : 20
        },
        off : {
            transition : 1,
            iteration : 1
        }
    },


    /**
     * @param currentTransition object set to one of the values above, depending on speed of transitions
     */
    currentTransition : 'fast',


    /**
     * @param keys array an array of keyboard buttons that are currently held down, for modifying behaviour
     */
    keys : {
        control : {
            keyCode : [17],
            pressed : false
        },
        command : {
            keyCode : [91,93,224],
            pressed : false
        },
        shift : {
            keyCode : [16],
            pressed : false
        },
        alt : {
            keyCode : [18],
            pressed : false
        }
    },


    /**
     * add listeners to each tab link (left hand nav)
     * @returns void
     */
    addProjectTabHandlers: function() {

    	// set listener for project tab buttons
    	$("ul#projecttabs li a").click(function (e) {

    		// stop link refreshing page
    		e.preventDefault();

    		// lose focus on current link
    		this.blur();

    		// set the left hand nav selected tab
    		boris.setSelectedTab(this);

    		// update the url via SWF Address (removing the front ./)
    		var addLocation = $(this).attr('rel');
    		SWFAddress.setValue(addLocation.slice(2));
    	});
    },



    /**
     * Set the selected tab link, or deselect all
     * Called when a tab is called, or back button pressed, or breadcrumb used...
     *
     * @param {object} element clicked html link element
     * @returns void
     */
    setSelectedTab: function(element){

    	// loop through link elements
    	$("ul#projecttabs li a").each(function (i) {

    		// condition : link is currently selected, but hasn't been clicked
    		if($(this).hasClass("selected") && $(this).attr('rel') != $(element).attr('rel') && $(this).attr('rel') != "." + $(element).attr('href')) {
    			$(this).removeClass('selected');

    		// condition : link isn't selected, but HAS just been clicked
    		} else if(!$(this).hasClass("selected") && ($(this).attr('rel') == $(element).attr('rel') || $(this).attr('rel') == "." + $(element).attr('href'))) {
    			$(this).addClass('selected');
    		}
    	});
    },



    /**
     * add click handlers on each project within the project list
     * @returns void
     */
    addProjectClickHandlers: function(){
    	// set listener for clicking on each project
    	$("#projectlist a").click(function (e){

    		// stop link refreshing page
    		e.preventDefault();

			// lose focus on current link
    		this.blur();

    		// set the left hand nav selected tab
    		boris.setSelectedTab(this);

            // check if the destination of the selected link is a file, directory with an index link, or an internal link
    		boris.checkProjectForIndex(this, e);
    	});
    },



    /**
     * add listeners for option selection
     * @returns void
     */
    addOptionsHandlers: function() {

    	// set listener for project tab buttons
    	$("#options-view li a, #options-tabs li a, #options-transitions li a, #options-colourscheme li a, #options-language li a").click(function (e) {

    		// stop link refreshing page
    		e.preventDefault();

    		// lose focus on current link
    		this.blur();

    		// condition : which link has been clicked
    		switch (this.parentNode.id) {

    		    // condition : layout - grid?
    		    case "options-view-grid":
        		    if ($('#projectlist').hasClass('list')) {
            		    boris.hideProjects(null, function(){
            		        $('#projectlist').removeClass('list').addClass('grid');
                		    boris.scrollProjectContainerHeight();
            		    });
            		    cookie.set("optionsView", "grid", 1000, '/');

            		    // change selected icon - add/remove strong element
                        $('#options-view-grid a').html('<strong>'+strings['grid']+'</strong>');
                        $('#options-view-list a').html(strings['list']);
        		    }
        		break;

        		// condition : layout - list?
        		case "options-view-list":
                    if ($('#projectlist').hasClass('grid')) {
                        boris.hideProjects(null, function(){
            		        $('#projectlist').removeClass('grid').addClass('list');
            		        boris.scrollProjectContainerHeight();
                        });
            		    cookie.set("optionsView", "list", 1000, '/');

            		    // change selected icon - add/remove strong element
                        $('#options-view-grid a').html(strings['grid']);
                        $('#options-view-list a').html('<strong>'+strings['list']+'</strong>');
        		    }
        		break;


        		// condition : tabs - show them?
    		    case "options-tabs-on":
        		    // consition : if the tabs are currently hidden, show them
        		    if ($('#projects').hasClass('full')) {
            		    boris.hideProjects(null, function(){
            		        $('#projects').removeClass('full');
            		        boris.scrollProjectContainerHeight();
            		        $('#projecttabs').css({
            		          opacity : "0"
            		        }).animate({
            		            opacity:"1"
            		        },
            		        boris.transitionTimes[boris.currentTransition].transition);
            		    });
            		    cookie.set("optionsTabs", "show", 1000, '/');

            		    // change selected icon - add/remove strong element
                        $('#options-tabs-on a').html('<strong>'+strings['on']+'</strong>');
                        $('#options-tabs-off a').html(strings['off']);
        		    }
        		break;


        		// condition : tabs - hide them?
    		    case "options-tabs-off":
        		    // condition : if the tabs are currently shown, hide them
        		    if (!$('#projects').hasClass('full')) {
            		    boris.hideProjects(null, function(){
            		        $('#projecttabs').css({
            		          opacity:"1"
            		        }).animate({
            		            opacity:"0"
            		        },
            		        boris.transitionTimes[boris.currentTransition].transition,
            		        function(){
            		            $('#projects').addClass('full');
            		            boris.scrollProjectContainerHeight();
            		        });
            		    });
            		    cookie.set("optionsTabs", "hide", 1000, '/');

            		    // change selected icon - add/remove strong element
                        $('#options-tabs-on a').html(strings['on']);
                        $('#options-tabs-off a').html('<strong>'+strings['off']+'</strong>');
        		    }
        		break;

        		// condition : transitions - turn on normal?
    		    case "options-transitions-normal":
            		    cookie.set("optionsTransitions", "normal", 1000, '/');
            		    boris.currentTransition = 'normal';

            		    // change selected icon - add/remove strong element
                        $('#options-transitions-normal a').html('<strong>'+strings['normal']+'</strong>');
                        $('#options-transitions-fast a').html(strings['fast']);
                        $('#options-transitions-off a').html(strings['off']);
        		break;

        		// condition : transitions - turn on fast?
        		case "options-transitions-fast":
            		    cookie.set("optionsTransitions", "fast", 1000, '/');
            		    boris.currentTransition = 'fast';

            		    // change selected icon - add/remove strong element
                        $('#options-transitions-normal a').html(strings['normal']);
                        $('#options-transitions-fast a').html('<strong>'+strings['fast']+'</strong>');
                        $('#options-transitions-off a').html(strings['off']);
        		break;

        		// condition : transitions - turn on fast?
        		case "options-transitions-off":
            		    cookie.set("optionsTransitions", "off", 1000, '/');
            		    boris.currentTransition = 'off';

            		    // change selected icon - add/remove strong element
                        $('#options-transitions-normal a').html(strings['normal']);
                        $('#options-transitions-fast a').html(strings['fast']);
                        $('#options-transitions-off a').html('<strong>'+strings['off']+'</strong>');
        		break;
            }

			// condition : separate statement for skin selection - dynamic content
			if (this.parentNode.parentNode.id == 'skinselect') {

				// retrieve selected skin name, and set cookie
				var skin = this.parentNode.id.split('options-colourscheme-')[1];

				// get the html body tag, to add/remove classes later
				var bodyTag = $('body');

				// run through all available skins, adding/removing classes
				$('ul#skinselect li a').each(function(i){

					var skinName = this.parentNode.id.split('options-colourscheme-')[1];

					// condition : selected skin
					if (skinName == skin) {

						// if this skin is already selected, don't do anything
						if (!bodyTag.hasClass(skin)) {
							cookie.set("optionsColourscheme", skin, 1000, '/');
							bodyTag.addClass(skin);
							$(this).html("<strong>"+boris.ucFirst(skinName)+"</strong>");
						}
					} else {
						bodyTag.removeClass(skinName);
						$(this).text(boris.ucFirst(skinName));
					}
				});
			}

			// condition : separate statement for language selection - dynamic content
			if (this.parentNode.parentNode.id == 'languageselect') {

				// retrieve selected skin name, and set cookie
				var lang = this.parentNode.id.split('options-language-')[1];

				// run through all available skins, adding/removing classes
				$('ul#languageselect li a').each(function(i){

					var languageName = this.parentNode.id.split('options-language-')[1];

					cookie.set("localizationLanguage", lang, 1000, '/');
					window.location.reload();
				});
			}
		});
    },



    /**
     * Fade in the list of projects, one at a time
     * @returns void
     */
    showProjects: function(){

    	// debug
    	// console.log('showProjects(): displaying all new projects');
    	// console.log('[----------------------------------------------------------------------]');

    	// show the project list (if currently hidden)
    	$("#projectlist").css({
    		visibility:"visible",
    		opacity:"1",
    		top:"0"
    	});

    	// hide the current project boxes, and fade them in with a little animation
    	$("#projectlist p")
    		.css({
    			position:"relative",
    			opacity:"0",
    			top:"20px"
    		})
    		.each(function(i){
    		    var counter = (i < 20) ? i : 20; // stop slowdown of big directories
    			$(this).animate({
    			opacity:"1",
    			top: "0px"
    	      	}, boris.transitionTimes[boris.currentTransition].transition+(counter*boris.transitionTimes[boris.currentTransition].iteration)); // fade each in gradually
    		});
    },



    /**
     * Fade out all projects, at once.
     * This function can either request new projects, or call a custom function when complete
     *
     * @param object e the html link element that may have been clicked
     * @param function func a custom js function to execute if we aren't requesting new projects
     * @returns void
     */
    hideProjects: function(e, func){

        // debug
    	// console.log('hideProjects(): hiding all old projects');

    	// fade project list out
    	$("#projectlist").animate({
        	opacity:"0",
        	top: "20px"
          	}, boris.transitionTimes[boris.currentTransition].transition, function(){

    		// condition : if we are requesting new projects...request them!
    		if (func == null) {
    		    boris.toggleProjects(e);

    		// using the same projects, execute custom function
    		} else {
    		    func();
    		}
    	});
    },



    /**
     * Check the type of file that has been clicked, to see whether to open/preview/launch
     * @param {object} element the current link to check
	 * @param {object} e the link event, for span class checking
     * @returns void
     */
    checkProjectForIndex: function(element, e) {

		// condition : if selected link is a file, redirect to file
    	if ($(element).parent().hasClass('file')) {

    		// debug
    		// console.log('checkProjectForIndex(): link is file');

            // condition : capture preview text selections
			if ($(e.target).hasClass("code")) {
				boris.previewTextFile(element);

			// capture preview image selections
			} else if ($(e.target).hasClass("image")) {
				// thickbox will deal with it

			// redirect to file
			} else {
	    		// condition : if a modifier button is pressed, open in a new window
                if (boris.keys.control.pressed || boris.keys.command.pressed) {
                    window.open(element, "_blank");
                } else {
                    window.location.href = element;
                }
			}

    	// selected link is a directory
    	} else {

    		// debug
    		// console.log('checkProjectForIndex(): link is directory');

			// condition : capture site launch selections
			if (e.target.className == "site") {

				// condition : if a modifier button is pressed, open in a new window
                if (boris.keys.control.pressed || boris.keys.command.pressed) {
                    window.open(element, "_blank");
                } else {
                    window.location.href = element;
                }

			// update the url via SWF Address (removing the front ./)
			} else {
				var addLocation = $(element).attr('href');

				// IE uses absolute links, so need to remove server path before use
				var locationString = 'http://' + $("#localhost").text();
				locationString = $.trim(locationString);
				var newLocation = addLocation.replace(locationString, '');

				// redirect
				SWFAddress.setValue(newLocation.slice(1));
			}
    	}
    },



    /***
     * Toggle projects
     * @param {object} element the link in question
     * @returns void
     */
    toggleProjects: function(element) {

    	var path;

    	// condition : check for a rel (correct path, added for tab [left nav] links)
    	if ($(element).attr('rel')) {
    		path = $(element).attr('rel');

    	// else, try using the link href (from project box links)
    	} else if ($(element).attr('href')) {
    		path = $(element).attr('href');

    	// else, it has been called through SWF Address, or back/forward buttons
    	} else if (element.path) {
    		path = element.path;
    	}


        // debug
    	// console.log('toggleProjects(): getting all new projects for: ' + path);

    	// make ajax call through jquery
    	$.ajax({
    	   type: "POST",
    	   url: LibMan.path + "../../../php/site/Ajax.php",
    	   data: "path="+path+"&indexRootPath="+indexRootPath+"&functionname=createFileList",

    	   // process the response
    	   success: function(response){

    			// replace current projects with these new ones
    			$("#projectlist").html(response);

    			// set the new page title, from the current URL
    			boris.setPageTitle(path);

    			// set the new breadcrumb, from the current URL
    			boris.setBreadcrumb(path);

    			// scroll the height of the projects container
    			boris.scrollProjectContainerHeight();

				// new content has been added, so re-bind thickbox links
				tb_init('span.preview span.image');

    	   }
    	 });
    },



    /**
     * scroll the height of the project container, once new projects have been added (but are currently hidden)
     * @returns void
     */
    scrollProjectContainerHeight: function() {

    	// debug
    	// console.log('scrollProjectContainerHeight(): changing the height of the project container');

	    var tabsHeight;

	    // condition : if the left hand tabs are shown, calculate their height
    	if($('#projects').hasClass('full')) {
        	tabsHeight = 0;
    	} else {
    	    tabsHeight = $("#projecttabs").height()-2;
    	}

    	// calculate the new height to set the project container, so nothing gets hidden
	    var listHeight = $("#projectlist").height();
    	var newHeight = (listHeight >= tabsHeight) ? listHeight : tabsHeight;

    	// animate the height change of the main project container
    	$("#projects").animate({
    		height:newHeight
          	}, boris.transitionTimes[boris.currentTransition].transition*2,

    		// once the change has completed, show the new projects
    		function() {

    			// display new projects
    			boris.showProjects();

    			// re-set new click handlers on each new project
    			boris.addProjectClickHandlers();
    	});
    },



    /**
     * Set page title, on state change
     * @param {string} title the url of the new link
     * @returns void
     */
    setPageTitle: function(title) {

    	// format the current URL to appear as a new page title
    	var newTitle = 'Boris : '+strings['localhost_browser']+': - ' + (title != '/' ? ' / ' + title.substr(1, title.length - 2).replace(/\//g, ' / ') : strings['home']);

        // set the new page title via SWF Address
    	SWFAddress.setTitle(newTitle);
    },



    /**
     * Build the new breadcrumb
     * @param {string} title the url of the new link
     * @returns void
     */
    setBreadcrumb: function(title) {

    	// split the title into each segment
    	var titleSegments = title.split('/');

        // get the current localhost name (IP address? Name?)
        var localhost = $("#localhost").text();

        var newBreadcrumb;

    	// start the new breadcrumb
    	if(titleSegments.length <= 2) {
    		newBreadcrumb = '<li>'+localhost+'</li>';
    	} else {
    		newBreadcrumb = '<li><a href="/">'+localhost+'</a>';
    	}

    	// start a variable to hold the path back to each segment
    	var path = '/';

    	// traditional for loop, to miss out first and last segments
    	for (var i=1; i < titleSegments.length-2; i++) {
    		path += titleSegments[i] + "/";
    		newBreadcrumb += '<ul><li><a href="'+path+'">'+titleSegments[i]+'</a>';
    	}

    	// add the last item to the breadcrumb - no link required
    	newBreadcrumb += '<ul><li>'+titleSegments[titleSegments.length-2];

    	// add the ending for each crumb element
    	for (var j=1; j < titleSegments.length-2; j++) {
    		newBreadcrumb += '</li></ul>';
    	}

    	// insert the new breadcrumb
    	$("ul#crumb").html(newBreadcrumb);

    	// reset the links on the breadcrumb elements to use SWF Address
    	$("ul#crumb li a").click(function(e){

    		// stop link refreshing page
    		e.preventDefault();

    		// lose focus on current link
    		this.blur();

    		// set the left hand nav selected tab
    		boris.setSelectedTab(this);

			var linkLocation = $(this).attr('href');

			// IE uses absolute links, so need to remove server path before use
			var locationString = 'http://' + $("#localhost").text();
			locationString = $.trim(locationString);
			var newLocation = linkLocation.replace(locationString, '');

    		// update the url via SWF Address
    		SWFAddress.setValue(newLocation);
    	});
    },



    /**
     * reset the home link on the h1 element to use SWF Address, rather than page refresh
     *
     * @returns void
     */
    addHomeButtonHandler: function(){
    	$("h1 a").click(function(e){

    		// stop link refreshing page
    		e.preventDefault();

    		// lose focus on current link
    		this.blur();

    		// update the url via SWF Address
    		SWFAddress.setValue("/");

    		// reset the left hand nav tabs
    		boris.setSelectedTab(this);
    	});
    },



    /**
     * set the initial transition time, from the cookie (if it exists)
     *
     * @returns void
     */
     setInitialTransitionTime: function(){
        var transitions = cookie.get('optionsTransitions');
        if (transitions == "fast") {
             boris.currentTransition = 'fast';
        } else if (transitions == "off") {
            boris.currentTransition = 'off';
        } else if (transitions == "normal") {
			boris.currentTransition = 'normal';
	    }
     },



	/**
	 * Preview a text file, through thickbox
     * @param {object} element the current link
	 */
	previewTextFile: function(element) {

		// get the filename (to display above text)
		var t = $(element).find("strong").text() || "";

		// calculate ajax get request
		var a = LibMan.path + "../../../php/site/Ajax.php?functionname=processAjaxFileRequest&path="+element.href;

		// call thickbox to show file contents
		tb_show(t,a,false);
	},


	/*
	 * set listeners for when the control and/or command [mac] buttons are held down
	 * to enable variations on actions
	 */
	setKeyListeners: function() {

	    // set modifier value for key down
	    $(document).keydown(function (e) {
            for (key in boris.keys) {
                if (boris.inArray(e.which, boris.keys[key].keyCode)) {
                    //console.log(key + ' pressed');
                    boris.keys[key].pressed = true;
                }
            }
        });

        // remove modifier when key is released
        $(document).keyup(function (e) {
            for (key in boris.keys) {
                if (boris.inArray(e.which, boris.keys[key].keyCode)) {
                    boris.keys[key].pressed = false;
                }
            }
        });
	 },


	/**
	 * Check the current revision number against the live version
	 */
	checkRevision: function() {

	    // make ajax call through jquery
    	$.ajax({
    	    type: "POST",
    	    url: LibMan.path + "../../../php/site/Ajax.php",
    	    data: "functionname=checkRevision",

        	// process the response
        	success: function(response){

        		// replace default revision check info
        		$("#revision").html(response);
        	}
        });
	},



    /**
     * function to Capitalise the First text Character of A string
     * @param {string} title the url of the new link
     * @returns {string} the new Text String
     */
    ucFirst: function(textString) {
    	return textString.substr(0,1).toUpperCase() + textString.substr(1,textString.length);
    },


    /*
     * function to test whether a value exists in an array
     */
     inArray: function (what, where){
         var found = false;
         for(var i=0; i<where.length; i++){
             if(what == where[i]){
                 found = true;
                 break;
             }
         }
         return found;
     }
};