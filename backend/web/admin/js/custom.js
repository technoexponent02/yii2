$(window).on('load', function() {
   	bodyheightcontrol();
	adminNavHeight();
	$("body").on("click",".mobileNavclick", function(){
		$("body").addClass("smnavShow");
	});
	$("body").on("click",".dashboardRgtOvrly, .navMain a", function(){
		$("body").removeClass("smnavShow");
	});
	
	
	/* Dashboard tabs */
	var firsttab = $(".mobileTab li:first-Child a").attr("href");
	$(firsttab).addClass("showNow");
	
	$("body").on("click",".mobileTab a", function(event){
		event.preventDefault();
		$(this).addClass("sl");
		$(this).parent().siblings().children().removeClass("sl");
		var tab = $(this).attr("href");
		var tab2 = (tab + "2");
		$(".tabShowDiv").not(tab).removeClass("showNow");
		$(tab).addClass("showNow");
		$(tab2).addClass("showNow");
	});
	
	//$(".adminSidebar > ul > li > ul").parent("li").addClass("sub");
	$(".adminSidebar > ul > li > ul").parent("li").children("a").append("<span class='ico'></span>");
	$("body").on("click",".adminSidebar > ul > li > a", function(){
		if(!$(this).next("ul").is(":visible")){
			$(this).addClass("open");
			$(this).next("ul").slideDown(400);
		}else{
			$(this).removeClass("open");
			$(this).next("ul").slideUp(400);
		}
	});
});
$(window).resize(function(){
   	bodyheightcontrol();
	adminNavHeight();
});
function bodyheightcontrol(){
	var fullscreenH = $(window).height();	
	var bodyheadH = $("header").innerHeight();	
	var bodyfootrH = $("footer").innerHeight();	
	var bodycontH = fullscreenH - (bodyheadH + bodyfootrH);
	$(".dashboardCont").css({"min-height":bodycontH});
}
function adminNavHeight(){
	var fullscreenH = $(window).height();	
	var bodyheadH = $("header").innerHeight();
	var adminLHeight = $(".adminLeftcont").innerHeight();
	var adminLHeightNew = fullscreenH - bodyheadH;
	$(".adminSidebar").css({"height":adminLHeight});
	$(".adminSidebar").css({"min-height":adminLHeightNew});
}