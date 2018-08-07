jQuery(function($){
	
	//start page
	var messages = $("#messages");
	var h = messages[0].scrollHeight;
	messages.scrollTop(h);

	messages.scroll(function(event){
		var pos = $(this).scrollTop();

		if(pos == 0){
			loadPreviousMessages();
		}
		
	});

	// Добавление нов. смс
	var send_add_form = 0;
	// Подгрузка предыдущих смс
	var send_loadprevious = 0;
	// Открыть другой топик
	var get_topic = 0;


	
	
	/*
	* Функций для взаимолдействия с воркер
	*/
	var getUserParams = function(){
		return parseInt($("#current_user_id").val());
	};
	var getActiveTopicParams = function(){
		var activeTopic = {
			id:$(".topic_item.topic_item_active").data("id"),
			last_msg:$(".topic_item.topic_item_active").attr("data-last"),
		};

		return activeTopic;
	};
	var getOtherTopicsParams = function(){
		var oTopics = [];
		$(".topic_item").each(function(){

			if(!$(this).hasClass("topic_item_active")){
				oTopics.push({
					id:$(this).data("id"),
					last_msg:$(this).attr("data-last")
				});
			}

		});
		return oTopics;
	}
	var sendChanges = function (){
		var params = {
			activeTopic : getActiveTopicParams(),
			user:getUserParams(),
			otherTopics: getOtherTopicsParams(),
		};
		chatState.sendChanges(params);
	}

	/**
	* Конец
	*/

	var beginParams = {
		activeTopic : getActiveTopicParams(),
		user:getUserParams(),
		otherTopics: getOtherTopicsParams(),
	};
	chatState.startClient(beginParams);


	


	var loadPreviousMessages = function(){

		var count = $("#messages .message").length;
		var topic = parseInt($(".topic_item_active").data("id"));
		var first = $("#messages .message").eq(0);


		if(topic !== "undefined" && !send_loadprevious){

			$.ajax({
				url:"/chat?action=previousmessage",
				data:"offset="+count+"&topic="+topic,
				dataType:'html',
				type:"GET",
				beforeSend:function(){
					send_loadprevious = 1;
				},
				success: function(html){
					if(html.trim().length){

						var cT  = parseInt($(".topic_item_active").data("id"));
						if(cT == topic){
							$("#messages").prepend(html);
							var ofssetFirst = first.offset();
							
							if(typeof ofssetFirst == "object" && ofssetFirst.hasOwnProperty("top")){
								messages.scrollTop(ofssetFirst.top - 150);
							}
						}
					}
				},
				error:function(msg){
					console.log(msg);
				},
				complete:function(){
					send_loadprevious = 0;
				}
			});
		}
	}

	function strip_tags( str ){	// Strip HTML and PHP tags from a string
			// 
			// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

			return str.replace(/<\/?[^img][^>]+>/gi, '');
	}

	//Отправка сообщения при нажатии на enter
	// document.onkeyup = function (e) {
 //        e = e || window.event;
 //        if (e.keyCode === 13 && !e.ctrlKey) {

 //        	var m = strip_tags($("#new_message").html());
 //        	var f = $("#message-file").val();
 //        	if((m.trim().length || f.length) && !send_add_form){
 //        		//$("#btn-add-msg").trigger("click");
 //        		$("form#form-add-msg").trigger("submit");
 //        	}
 //        }

 //        return false;
 //    }



    $('div#new_message').keydown(function (e) {
    	if (e.keyCode === 13 && e.ctrlKey) {
        	console.log("enterKeyDown+ctrl");
        	
    	}
    	
    	return true;

	}).keypress(function(e){
	    if (e.keyCode === 13 && !e.ctrlKey) {
	        
	        var m = strip_tags($("#new_message").html());
        	var f = $("#message-file").val();
        	
        	if((m.trim().length || f.length) && !send_add_form){
        		
        		$("form#form-add-msg").trigger("submit");
        	}

	        return false;  
	    } 
	});
	

	$("form#form-add-msg").submit(function(event){
		event.preventDefault();

		//заменить &nbsp = "";
		var msg = $("#new_message").html();
		
		
		$("#new_message").html(msg.split("&nbsp;").join(" "));

		var d = $(this).serialize();		
		var m = $("#new_message").html();
		d+="&"+$("#new_message").data("name")+"="+m.trim();
		var f = $("#message-file").val();


		if((m.trim().length || f.length) && !send_add_form){

			$.ajax({
				url:$(this).attr("action"),
				data:d,
				dataType:'html',
				type:"POST",
				beforeSend:function(){
					// console.log('beforeSend');
					send_add_form = 1;
				},
				success: function(res){

					if(res){
						$("#messages").html(res);
						var msgBlock = $("#messages");
						var h = msgBlock[0].scrollHeight;
						msgBlock.scrollTop(h);
						$("#new_message").html("");

						//блок добавления файла
						$("#message-file").val("");
      					$(".target_loaded_image").html("");
      					$("#loaded_image").hide();
      					$("#file-methods").hide();
      					$("#message-file").val("");
              			$("#add-file").val("");
					}
				},
				error:function(msg){
					console.log(msg);
				},
				complete:function(){
					send_add_form = 0;
				}
			})
		}else{
			console.log("msg is empty");
		}
	})


	
	$(".topics_menu_block").on("click",".topic_item",function(event){

		event.preventDefault();
		if($(this).hasClass("topic_item_active")) return;

		$(".topic_item_active").removeClass("topic_item_active");
		$(this).addClass("topic_item_active");
		var id = $(this).data("id");
		var action = $(this).find("a").attr("href");
		
		if(action && !get_topic){
			
			$.ajax({
				url:action,
				dataType:'html',
				type:"GET",
				beforeSend:function(){
					// console.log('beforeSend');
					get_topic = 1;
				},
				success: function(res){
						$("#messages").html(res);
						
						var msgBlock = $("#messages");
						var h = msgBlock[0].scrollHeight;
						msgBlock.scrollTop(h);
						
						$("#new_message").val("");

						$("#new_message_topic").val(id);

						var last_msg = $("#messages").find(".message").eq(-1).data("id");
						$(".topic_item_active").attr("data-last",last_msg);

						$(".topic_item_active span.notification_topic").remove();
						//Передаем в сокет смену топика
						sendChanges();
					
				},
				error:function(msg){
					console.log(msg);
				},
				complete:function(){
					get_topic = 0;
				}
			})
		}
	});
	


	var send_removemsg = 0;

	
	$("#messages").on("click",".btn-removemsg",function(event){
		event.preventDefault();

		if(!confirm("Accept you action!")){
			return;
		}
		
		var id = $(this).data("id");
		var action = $(this).attr("href");
		
		if(action && !send_removemsg){
			
			$.ajax({
				url:action,
				dataType:'html',
				type:"GET",
				data:{
					mid:id
				},
				beforeSend:function(){
					// console.log('beforeSend');
					send_removemsg = 1;
				},
				success: function(res){
					
					$("#messages").html(res);
						
				},
				error:function(msg){
					console.log(msg);
				},
				complete:function(){
					send_removemsg = 0;
				}
			})
		}
	});




	//Выбор получателя сообщения
	$("body").on("click",".span_set_member_to",function(event){
		event.preventDefault();

		var id = $(this).siblings("a").data("member-id");
		if(id){
			$("#new_message_to").val(id);
			$("#send_to").find("span").text($(this).siblings("a").text());
			$("#send_to").find("span").append("<span id='remove_send_to'>x</span>");
		}
	})


	//Удаление получателя
	$("#send_to").on("click","#remove_send_to",function(event){
		event.preventDefault();

		$("#send_to").find("span").text("");
		$("#new_message_to").val(0);
	})



	// $("body").on("dblclick",".set_member_to",function(event){
	// 	var href = $(this).attr("href");
		
	// 	if(href){
	// 		window.location = href;
	// 	}
	// });





	$("body").on("click","#open_smiles",function(){
		$("#smiles").toggle();
	});


	$("body").on("click","#smiles ul li",function(){
		var smile = $(this).html();

		$("#new_message").append(smile);
		$("#new_message").focus();
		//$("#smiles").toggle();
	});



	var send_search_req = 0;
	$("body").on("keyup","#searchKey",function(){
		
		var key = $(this).val();
		if(key.length >=3 && !send_search_req){
			var d = $("#searchForm").serialize();
			var action = $("#searchForm").attr("action");
			$.ajax({
				url:action,
				dataType:'html',
				type:"GET",
				data:d,
				beforeSend:function(){
					// console.log('beforeSend');
					send_search_req = 1;
				},
				success: function(res){
					$("#searchResult").html(res);
					$("#clear_search_result").show();
				},
				error:function(msg){
					console.log(msg);
				},
				complete:function(){
					send_search_req = 0;
				}
			})
		}
		
	});
	
	$("body").on("click","#clear_search_result",function(){
		$("#searchResult").html("");
		$(this).hide();
	});


})