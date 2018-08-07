


if(!!window.Worker && typeof chatState == "object"){
	if(!worker){
		var indicator = document.createElement("div");
		
		document.body.appendChild(indicator);
		var s = document.createElement("span");
		s.innerText = "*";
		indicator.appendChild(s);

		var indicate = function(){
			indicator.style.opacity = parseInt(indicator.style.opacity) == 1 ? 0 : 1;
		}

		var worker = new Worker("/chat/socket/js/worker.js");
		

		worker.onmessage = function (event){
			indicate();
			var data = event.data;
			if(typeof data == "object"){

				var cl_active_topic = parseInt($(".topic_item.topic_item_active").data("id"));

				if(data.hasOwnProperty("params") && data.params.hasOwnProperty("activeTopic") && data.hasOwnProperty("html")){
					
					var serv_active_topic = data.params.activeTopic.id;
					var t_max_id = data.params.activeTopic.last_msg;
					//Фиксируем изменения от сервера
					chatState.fixChanges(data.params);
					
					//Если пока сервер обрабатывал запрос активная тема не изменилась и существует html, то добавляем смс на  страницу
					if(cl_active_topic == serv_active_topic && data.hasOwnProperty("html")){
						//и если новое смс уже не было добавлено другими путями
						if(!$(".message#msg"+t_max_id).length){
							$("#messages").append(data.html);
						}
					}

				}

				if(data.hasOwnProperty("params") &&  data.params.hasOwnProperty("otherTopics")){
					var t = data.params.otherTopics;
					for (var i =0; i < t.length - 1; i++) {
						
						if(t[i].hasOwnProperty("id") && t[i].hasOwnProperty("count")){
							if(cl_active_topic == t[i].id) continue;

							var id = t[i].id;
							var topic = $(".topic_item[data-id="+id+"]");
							if(t[i].count > 0){

								var t_span = topic.find("span.notification_topic");
								
								if(t_span.length){
									t_span.html("!");
								}else{
									var span = $("<span/>").addClass("notification_topic").html("!");
									topic.append(span);
								}

							}else{
								topic.find("span.notification_topic").remove();
							}

						}
					}
					
				}

			}
			// console.log(event.data);
		};

		chatState.sendChanges = function(params){

			if(worker){
				
				if(typeof params == "object"){
					chatState.fixChanges(params);
				}
				

				worker.postMessage({action:"change",params:this.params});
			}
		}

		chatState.startClient = function(params){

			if(worker){
				
				if(typeof params == "object"){
					chatState.fixChanges(params);
				}
				
				worker.postMessage({action:"run",params:this.params});
			}
		}


		chatState.stopClient = function(params){

			if(worker){
				worker.postMessage({action:"stop",params:{}});
			}
		}
	}
}else{
	worker = null;
	console.log('Browser don`t support the Worker');
}
