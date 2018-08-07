var socket_controller = {
	default:function(){
		console.log("default action");
	},
	
	runAction:function(params){
		if(wSocket){
			wSocket.setChanges(params);
			wSocket.listen();
		}
	},

	stopAction:function(params){
		if(wSocket){
			wSocket.stop();
		}
	},

	changeAction:function(params){
		if(wSocket){
			wSocket.setChanges(params);
		}
	},




	commitchangesAction:function(json){

		if(typeof json == "object" && json.hasOwnProperty("params")){
			if(wSocket){
				wSocket.setChanges(json.params);
			}
		}
		postMessage(json);
	},

	exec : function(action,params){
		var method = action+"Action";
		if(socket_controller.hasOwnProperty(method) && typeof socket_controller[method] == "function"){
			return socket_controller[method](params);
		}else{
			return socket_controller.default();
		}

	},
}