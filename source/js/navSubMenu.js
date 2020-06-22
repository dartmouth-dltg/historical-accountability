$(document).ready(function(){

    //check for submenu links, bind enter event, stop check

    checkInit(); 

    function checkInit() {
        check = setInterval(checkTrigger, 100);
    }

    setTimeout(function(){ checkClear(); }, 2000); //clear timer if it cant find any subnav links

    function checkClear() {
        clearInterval(check);
    }
    //hide mobile nav on shift + tab on first element
    $(".access-dropdown-mobile").children("ul").children("li").first().on('keydown',  $(".access-dropdown-mobile").children("ul").children("li").first(), function(e) { 
        var keyCode = e.keyCode || e.which; 
      
        if (keyCode == 9) { 
            if(e.shiftKey) {
                $(".access-dropdown-mobile").css("display", "none");
         
            }                 
        }            
    });
    //hide mobile nav on tab on last element
    $(".access-dropdown-mobile").children("ul").children("li").last().on('keydown',  $(".access-dropdown-mobile").children("ul").children("li").last(), function(e) { 
        var keyCode = e.keyCode || e.which; 
      
        if (keyCode == 9) { 
            if(!e.shiftKey) {
                $(".access-dropdown-mobile").css("display", "none");
         
            }                 
        }            
    });


    function checkTrigger() {
        var arrowCheck =   $('.submenu-arrow > span');
        if ( arrowCheck.length > 0) {
            $.fn.pressEnter = function(fn) {  
                return this.each(function() {  
                    $(this).bind('enterPress', fn);
                    $(this).keyup(function(e){
                        if(e.keyCode == 13)
                        {
                          $(this).trigger("enterPress");
                        }
                    })
                });  
             }; 
            
            //on press enter open submenu
            $(".submenu-arrow > span").pressEnter(function(){
                $("nav.desktop-nav > .navigation > li > ul").hide();
                $(".submenu-arrow > span").css("border-top", "12px solid white");
                $(this).css("border-top", "12px solid #F2E55B");
                var parent = $(this).parent();
                parent.children("ul").children("li").last().on('keydown', parent.children("ul").children("li").last(), function(e) { 
                    var keyCode = e.keyCode || e.which; 
                  
                    if (keyCode == 9) { 
                        if(!e.shiftKey) {
                            if($(".submenu-arrow > span").hasClass('isOpen')) {
                                $("nav.desktop-nav > .navigation > li > ul").hide();
                                $(".submenu-arrow > span").removeClass("isOpen");
                                $(".submenu-arrow > span").css("border-top", "12px solid white");
                            }                         
                        }
                    }
                });
       
                parent.children("ul").children("li").first().on('keydown', parent.children("ul").children("li").first(), function(e) { 
                    var keyCode = e.keyCode || e.which; 
                  
                    if (keyCode == 9) { 
                        if(e.shiftKey) {
                            if($(".submenu-arrow > span").hasClass('isOpen')) {
                                $("nav.desktop-nav > .navigation > li > ul").hide();
                                $(".submenu-arrow > span").removeClass("isOpen");
                                $(".submenu-arrow > span").css("border-top", "12px solid white");
                            }                         
                        }                 
                    }            
                });
              
                if($(this).hasClass('isOpen')) {
                    $("nav.desktop-nav > .navigation > li > ul").hide();
                    $(this).css("border-top", "12px solid white");
                    $(this).removeClass("isOpen");
                }

                else  {
                    parent.children().slideDown("ul");
                    $(this).addClass("isOpen");    
                }

            });
        
            checkClear();
        }

    }

    $(document).keydown(function(e) {
        var code = e.keyCode || e.which;
        if (code === 9) {  
            $(".submenu-arrow > span").removeClass("outline-add");
        }
        if (code === 27) {  
            $("nav.desktop-nav > .navigation > li > ul").hide();
            $(".submenu-arrow > span").removeClass("isOpen");
            $(".access-dropdown-mobile").css("display", "none");

        }
    });
   
    // toggle sub menu on desktop header nav
    $(document).on("click", ".submenu-arrow > span", function(e) {
        $(".submenu-arrow > span").addClass("outline-add");
        $("nav.desktop-nav > .navigation > li > ul").hide();
        $(".submenu-arrow > span").css("border-top", "12px solid white");
        $(this).css("border-top", "12px solid #F2E55B");
        var parent = $(this).parent();
         if($(this).hasClass('isOpen')) {
            $("nav.desktop-nav > .navigation > li > ul").hide();
            $(this).css("border-top", "12px solid white");
            $(this).removeClass("isOpen");
        }

        else  {
            parent.children().slideDown("ul");
            $(this).addClass("isOpen");
        }

    });
    
    $('#primary-content').click(function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
        $(".submenu-arrow > span").removeClass("isOpen");
        $(".submenu-arrow > span").css("border-top", "12px solid white");
    });
    
    $('#splash').click(function() {
        $("nav.desktop-nav > .navigation > li > ul").hide();
        $(".submenu-arrow > span").removeClass("isOpen");
        $(".submenu-arrow > span").css("border-top", "12px solid white");
    });
    

    // add class to menu items with subnav to dislay down arrow
    var withMenutags = $(".navigation > li").filter(function() {
        return $(this).children("ul").length !== 0;
    });
    withMenutags.addClass("submenu-arrow");
    withMenutags.children("a").after("<span></span>");
    withMenutags.children("ul").attr('aria-label', 'sub nav');
    withMenutags.children("span").addClass('outline-add');

   

    //get subnav to display properly without admin bar
    $(window).resize(function() {
        //take out tab index on mobile, add on desktop
        if($(window).width() >= 739) {
            withMenutags.children("span").attr('tabIndex', 0);
        }
        else   {
            withMenutags.children("span").removeAttr("tabIndex");
            withMenutags.children("ul").removeAttr("tabIndex");
        }
         
        if ( $('#user-bar').length !== 0 ) {
            $(".navigation > li > ul").css("top", $("header").height() + 43);
        }
        else {
            $(".navigation > li > ul").css("top", $("header").height() + 5);
        }

    });
    if ( $('#user-bar').length !== 0 ) {
        $(".navigation > li > ul").css("top", $("header").height() + 43);
    }
    else {
        $(".navigation > li > ul").css("top", $("header").height() + 5);
    }
    $(window).trigger('resize');

});