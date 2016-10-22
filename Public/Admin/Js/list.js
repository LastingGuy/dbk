// JavaScript Document
$(function(){
	$(".list tr").hover(function(){
		$(this).addClass('trhover');
	},function(){
		$(this).removeClass('trhover');
	});
	$(".list tr").click(function(){
		$(this).toggleClass('trclick').siblings().removeClass('trclick');
	});
	$("table tr:last td").css('border','none');
})