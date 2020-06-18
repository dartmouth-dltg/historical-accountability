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
                // parent.children("ul > li").attr('tabIndex', 0);
                // parent.children("ul").focus();

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
    withMenutags.children("span").attr('tabIndex', 0);
    withMenutags.children("ul").attr('tabIndex', 0);
    withMenutags.children("ul").attr('aria-label', 'sub nav');
    withMenutags.children("span").addClass('outline-add');

    //get subnav to display properly without admin bar
    $(window).resize(function() {
        
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