$(document).ready(function(){
	$("#nav ul li").hover(function(){
		$(this).find("ul").stop().slideToggle(400);

	})

});