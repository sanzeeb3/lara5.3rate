//	CUSTOM JS FOR wpdevart OPTIONS
/**
 * On button click show/hide divs with animations
 */
jQuery.noConflict()(function ($) { 
$(".slidein-left-btn").on("click",function(e){
    var effect = 'slide'; 
    var option = { direction: 'left' };    
    var duration = 500;
    $(this).nextAll(".slideIn-left:first").toggle(effect, option, duration);
});
$(".slidein-right-btn").on("click",function(e){
    var effect = 'slide'; 
    var option = { direction: 'right' };    
    var duration = 500;
    $(this).nextAll(".slideIn-right:first").toggle(effect, option, duration);
});
$(".slidein-top-btn").on("click",function(e){
    var effect = 'slide'; 
    var option = { direction: 'up' };    
    var duration = 500;
    $(this).nextAll(".slideIn-top:first").toggle(effect, option, duration);
});
$(".slidein-bottom-btn").on("click",function(e){
    var effect = 'slide'; 
    var option = { direction: 'down' };    
    var duration = 500;
    $(this).nextAll(".slideIn-bottom:first").toggle(effect, option, duration);
});
$(".fadein-btn").on("click",function(e){
	var options = {};
    $(this).nextAll(".fadein:first").effect( 'fade', options, 500);
});
$(".fadein-btn").on("click",function(e){
	var options = {};
    $(this).nextAll(".fadein:first").fadeIn();
});
$(".bounce-btn").on("click",function(e){
	var options = {};
    $(this).nextAll(".bounce:first").effect( 'bounce', options, 500);
});
$(".shake-btn").on("click",function(e){
	var options = {};
    $(this).nextAll(".shake:first").effect( 'shake', options, 500);
});
$(".toggle-btn").on("click",function(e){
    $(this).nextAll(".toggle:first").slideToggle();
});
$('.radio-on').click(function() {
	$(this).nextAll(".toggle:first").slideDown();     
});
$('.radio-off').click(function() {
	$(this).nextAll(".toggle:first").slideUp();   
});
$('.check-on').click(function() {
	$(this).nextAll(".toggle:first").slideToggle();     
}); 
$('.check-on-multiple').click(function() {
	var checkClass4 = $(this).attr("class").split(' ').pop();
	$(".toggle").each(function(){
		if($(this).hasClass(checkClass4)){
			$(this).slideToggle();
		} 
	});   
}); 
$('.radio_1').click(function() {
	var checkClass1 = $(this).attr("class").split(' ').pop();
	$(".toggle").each(function(){
		if($(this).hasClass(checkClass1)){
			$(this).slideDown();
		}      
	});
});
$('.radio_2').click(function() {
	var checkClass1 = $(this).attr("class").split(' ').pop();
	$(".toggle").each(function(){
		if($(this).hasClass(checkClass1)){
			$(this).slideUp();
		}      
	});
});
$('.radio_1.show-hide-message').click(function() {
	var checkClass1 = $(this).attr("class").split(' ').pop();
	$(".toggle").each(function(){
		if($(this).hasClass(checkClass1) && $(this).hasClass("show-hide-message")){
			$(this).slideUp();
		}      
	});
}); 
$('.radio_2.show-hide-message').click(function() {
	var checkClass1 = $(this).attr("class").split(' ').pop();
	$(".toggle").each(function(){
		if($(this).hasClass(checkClass1)  && $(this).hasClass("show-hide-message")){
			$(this).slideDown();
		}      
	});
}); 


$(document).ready(function() {
    $('.radio_1:checked').each(function() {
		var checkClass3 = $(this).attr("class").split(' ').pop();
		
        $(".toggle").each(function(){
			if($(this).hasClass(checkClass3)){
				$(this).slideDown();
			} 
		});
    });
	$('.check-on-multiple:checkbox:checked').each(function() {
		var checkClass2 = $(this).attr("class").split(' ').pop();
        $(".toggle").each(function(){
			if($(this).hasClass(checkClass2)){
				$(this).slideToggle();
			} 
		});
    });
});
});
 
 
