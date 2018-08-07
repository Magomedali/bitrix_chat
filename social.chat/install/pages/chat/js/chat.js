$(document).ready(function(){

	var body_width = $("body").width();
	var sltoshow = 8;
	if (body_width < 600) sltoshow = 3; 
	$("#chat_members_list").slick({
		infinite: true,
    	dots: false,
    	slidesToShow: sltoshow,
    	slidesToScroll: 1
	});

	$(".view_all_members").click(function(e){
		e.preventDefault();
		$("#all_members").css("display","flex");
		$(".overlay").show();
	})

	$(".overlay").click(function(){
		$(".overlay").hide();
		$("#all_members").hide();
	})

	// $(".search_button").click(function() {
 //        // получаем то, что написал пользователь
 //       var searchString    = $("#search_box").val();
 //        // формируем строку запроса
 //        var data            = 'search='+ searchString;
 //        // если searchString не пустая
 //        if(searchString) {
 //            // делаем ajax запрос
 //            $.ajax({
 //                type: "POST",
 //                url: "/chat/php/do_search.php",
 //                data: data,
 //                beforeSend: function(html) { // запустится до вызова запроса
 //                    $("#results").html('');
 //                    $("#searchresults").show();
 //                    $(".word").html(searchString);
 //               },
 //               success: function(html){ // запустится после получения результатов
 //                    $("#results").show();
 //                    $("#results").append(html);
 //              }
 //            });
 //        }
 //        return false;
 //    });

})