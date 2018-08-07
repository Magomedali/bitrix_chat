$(function(){


	var sendUserFileForm = function(){
      	$("#uploadfile").submit(function(event){
      		event.preventDefault();

	        $("#uploadfile").ajaxForm();
	        var dForm = $(this).serialize();
	        var action = $("#uploadfile").attr("action");
	        if(action){
				$("#uploadfile").ajaxSubmit({
			        url:action,
			        //iframe: true,
			        data: dForm,
			        type:"POST",
			        dataType: 'json',
			        beforeSend:function(){
			        	//console.log("before");
			        },
			        success:function(json){
			            var ava_path = json.hasOwnProperty("ava_path") ? json.ava_path : 0;
			            var res = json.hasOwnProperty("res") ? json.res : 0;
			            
			            if(res && ava_path){
			            	$("#profile_ava").attr("src",ava_path);
			            }else{
			            	$("#message_block").text("error occured while change ava!");
			            }
			            $("#field-ava").val();
			        },
			        error:function(msg){
			        	console.log(msg);
			            console.log("error upload file at profile \n"+msg.statusText + msg.responseText);
			        },
			        complete:function(){
			        	//console.log("completed");
			        }
			    });
	        }
      	})
  }

  	$("#uploadfile").submit(sendUserFileForm());



  	var send_get_topic_form = 0;
  	$("body").on("click","#new_topic",function(event){
  		event.preventDefault();

      var tid = $(this).data("tid");
      var get_data = parseInt(tid) ? "topic_id="+parseInt(tid) : "";
  		var action = $(this).attr("href");
  		if(action && !send_get_topic_form){
  			$.ajax({
  				url: action,
  				data:get_data,
  				dataType:"html",
  				type:"GET",
  				beforeSend:function(){
  					send_get_topic_form = 1;
  				},
  				success:function(html){
  					if(html){
  						$("#new_topic_target").html(html);
  					}
  				},
  				error:function(msg){
  					console.log(msg);
  				},
  				complete:function(){
  					send_get_topic_form = 0;
  				}
  			});	
  		}
  		
  	});

  	$("body").on("click","#topicForm_close",function(event){
  		event.preventDefault();

  		$("#new_topic_target").html("");
  	});



    var send_remove_form = 0;
    $("body").on("click","#delete_topic",function(event){
      event.preventDefault();
      var tid = parseInt($(this).data("tid"));
      var action = $(this).attr("href");
      if(tid && action && !send_remove_form){
        $.ajax({
          url: action,
          data:"topic_id="+tid,
          dataType:"html",
          type:"GET",
          beforeSend:function(){
            send_remove_form = 1;
          },
          success:function(html){
            if(html){
              $("#new_topic_target").html(html);
            }
          },
          error:function(msg){
            console.log(msg);
          },
          complete:function(){
            send_remove_form = 0;
          }
        }); 
      }
    });




    var send_open_albumform = 0;
    $("body").on("click",".open_albumform",function(event){
      event.preventDefault();

      var action = $(this).attr("href");
      if(action && !send_open_albumform){
        $.ajax({
          url:action,
          dataType:"html",
          type:"POST",
          beforeSend:function(){
            send_open_albumform = 1;
          },
          success:function(html){
            if(html){

              $("#modal-content").html(html);
              $("#modal-window").show();
            }
          },
          error:function(msg){
            console.log(msg);
          },
          complete:function(){
            send_open_albumform = 0;
          }
        });
      }
    });




    var send_remove_album_form = 0;

    $("body").on("submit",'#formremovealbum',function(event){
        event.preventDefault();

        var action = $(this).attr("action");
        var formData = $(this).serialize();

        if(action && !send_remove_album_form){
          $.ajax({
            url:action,
            dataType:"html",
            data:formData,
            type:"POST",
            beforeSend:function(){
              send_remove_album_form = 1;
            },
            success:function(html){
              if(html){
                $("#modal-content").html(html);
                $("#modal-window").show();
              }
            },
            error:function(msg){
              console.log(msg);
            },
            complete:function(){
              send_remove_album_form = 0;
            }
          });
        }
    });



    var send_delete_photo = 0;
    $("body").on("click",".photo_delete",function(event){
        event.preventDefault();

        if(!confirm("Accept deleting photo!")){
          return;
        }

        var action = $(this).attr("href");
        var id = parseInt($(this).data("id"));
        
        var photo = $(this).parents(".photo_item");
        if(action && id && !send_delete_photo){
          $.ajax({
            url:action,
            type:"POST",
            data:"photo_id="+id,
            dataType:"json",
            beforeSend:function(){
              send_delete_photo = 1;
            },
            success:function(json){
              if(json.result){
                photo.remove();
              }
            },
            error:function(msg){
              console.log(msg);
            },
            complete:function(){
              send_delete_photo = 0;
            }
          });
        }
    });




    var send_getformaction = 0;
    $("body").on("click",'#getformsettings',function(event){
      event.preventDefault();
      var action = $(this).attr("href");
      if(action && !send_getformaction){
        $.ajax({
            url:action,
            type:"GET",
            dataType:"html",
            beforeSend:function(){
              send_getformaction = 1;
            },
            success:function(html){
              if(html){
                $("#modal-content").html(html);
                $("#modal-window").show();
              }
            },
            error:function(msg){
              console.log(msg);
            },
            complete:function(){
              send_getformaction = 0;
            }
          });
      }
    });




  var send_search_req = 0;
  $("body").on("keyup","#searchKey",function(){
    
    var key = $(this).val();
    if(key.length >=3 && !send_search_req){
      var d = $("#searchTopicForm").serialize();
      var action = $("#searchTopicForm").attr("action");
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



  
  var send_sort_change_req = 0;
  $("body").on("change",".wish_sort_change",function(){
    
    var value = parseInt($(this).val());
    var url = "";
    var wish = parseInt($(this).data("id"));
    if(value && wish && !send_sort_change_req){
      
      $.ajax({
        url:url,
        dataType:'json',
        type:"GET",
        data:{
          action:"changesortwish",
          wish:wish,
          value:value
        },
        beforeSend:function(){
          // console.log('beforeSend');
          send_sort_change_req = 1;
        },
        success: function(res){
          console.log(res);
        },
        error:function(msg){
          console.log(msg);
        },
        complete:function(){
          send_sort_change_req = 0;
        }
      })
    }
    
  });


})