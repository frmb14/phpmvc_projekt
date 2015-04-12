$( document ).ready(function() {
	$('#nav').affix({
		offset: {
			top: $('#header').height()
		}
	});
	
	$("#form-element-answer, #form-element-question").focus(function(){
		$(this).animate({height:200},200);
	});
	
	$(".add-comment").click(function(){
		$(this).next().toggle(200);
	});
});