
var socket = {
	resourse : null,
	waited : 0,
	
	socket: function(){

	},

	listen : function(){
		this.resourse = setInterval(this.ping,1000);
	},
	


	stop: function(){
		clearInterval(this.resourse);
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
	
		ajax.open("GET","/chat/socket/");
		
		ajax.setRequestHeader("Content-Type","application/json");
		
		ajax.onload = function (e) {
		    if(ajax.readyState != 4) return;

			if(ajax.status != 200){
				socket.error(ajax.status + " : "+ajax.statusText);
			}else{
				socket.success(JSON.parse(ajax.responseText));
			}
			socket.complete();
		};
		
		socket.beforeSend();

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
			postMessage(
				this.json,
			);
		}
	},
}