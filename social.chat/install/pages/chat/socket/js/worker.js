importScripts("./socket.js");
importScripts("./controller.js");
importScripts("./chatState.js");
var wSocket = socket;



//получаем данные из основного потока
onmessage = function(msg)
{	
	var data = msg.data;
	if( typeof data == "object" && data.hasOwnProperty("action")){
		var params = data.hasOwnProperty("params")? data.params : {};
		socket_controller.exec(data.action,params);
	}else{
		console.log("Формат полученных данных не правильный");
	}
};




