var chatState = {
	
	fixChanges : function(params){
		if(typeof params == "object"){
			//установка актив
			if(params.hasOwnProperty("activeTopic") 
				&& typeof params.activeTopic == "object" 
				&& params.activeTopic.hasOwnProperty("id") 
				&& params.activeTopic.hasOwnProperty("last_msg"))
			{
				chatState.setActiveTopic(params.activeTopic.id,params.activeTopic.last_msg);
			}

			if(params.hasOwnProperty("user")){
				chatState.params.user = params.user;
			}

			if(params.hasOwnProperty("otherTopics")){
				var topics = [];

				for (var i =  0; i < params.otherTopics.length; i++) {
					topics.push(
						{
							id		: params.otherTopics[i].id,
							last_msg: params.otherTopics[i].last_msg
					});
				}
				chatState.params.otherTopics = topics;
			}



		}
	},
	
	setActiveTopic:function(id,last_msg){
		if(id != "undefined")
			this.params.activeTopic.id = id;

		if(last_msg)
			this.params.activeTopic.last_msg = last_msg;
	},
	
	setOtherTopics:function(){

	},

	params:{
		user:0,
		activeTopic:{
			id:0,
			last_msg:0,
		},
		otherTopics:{

		},
	},
};