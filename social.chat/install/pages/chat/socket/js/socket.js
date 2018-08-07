
var socket = {
	resourse : null,
	waited : 0,
	
	socket: function(){

	},


	listen : function(){
		if(!this.resourse){
			console.log("Клиент запущен!");
			this.resourse = setInterval(this.ping,2000);
		}
	},
	

	stop: function(){
		clearInterval(this.resourse);
		this.resourse = null;
	},

	setChanges : function(params){
		chatState.fixChanges(params);
	},
	

	beforeSend: function(){
		this.waited = true;
	},
	

	success :function(json){
		this.response.json = json;
		this.response.status = 1;
		this.response.submit();
	},
	

	error :function(msg){
		this.response.text = msg;
	},
	

	complete :function(){
		this.waited = false;
	},


	ping: function(){

		if(socket.waited) return;
		
		var ajax = new XMLHttpRequest();

		var params = "task=checkstate&params="+JSON.stringify(chatState.params);
		var host = "/chat/socket/";
		var hostWithParams = host+"?"+params;
		ajax.open("GET",hostWithParams);
		
		
		//ajax.setRequestHeader('Content-type', 'application/json; charset=utf-8');

		ajax.onload = function (e) {
		    if(ajax.readyState != 4) return;

			if(ajax.status != 200){
				if(typeof socket.error == 'function'){
					socket.error(ajax.status + " : "+ajax.statusText);
				}
			}else{
				if(typeof socket.success == 'function'){
					socket.success(JSON.parse(ajax.responseText));
				}
			}

			if(typeof socket.complete == 'function'){
				socket.complete();
			}
		};
		
		if(typeof socket.beforeSend == 'function'){
			socket.beforeSend();
		}
		
		
		ajax.send(null);
	},
	




	request: {
		data:{

		}
	},





	response :{
		text : "",
		json : {},
		status : 0,
		submit : function(){
			//отправляем данные  в основной поток 

			if(this.json.hasOwnProperty("action")){
				socket_controller.exec(this.json.action,this.json);
			}
			
		}
	},
}