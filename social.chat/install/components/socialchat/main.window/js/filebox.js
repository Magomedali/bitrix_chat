$(function(){


	$("body").on("click","#showmethods",function(event){
		event.preventDefault();

		$('#file-methods').toggle();
	});



	

	var send_loadalbums = 0;
  	$("body").on("click","#loadalbums,.loadalbums",function(event){
  		event.preventDefault();

      var mid = parseInt($(this).data("mid"));
  	  	
  	  var action = $(this).attr("href");
  		
  		if(action && !send_loadalbums){
  			$.ajax({
  				url: action,
  				dataType:"html",
  				type:"GET",
  				beforeSend:function(){
  					send_loadalbums = 1;
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
  					send_loadalbums = 0;
  				}
  			});	
  		}
  	});

    
    var stick_album_photo = function(photo){
      $("#message-file").val(photo);
      $("#message-file").attr("data-album",1);
      $("#message-file_from_album").val(1);
      $(".target_loaded_image").html("<img src='"+photo+"' width='150px'>");
      $("#loaded_image").show();
    };



    $("body").on("click",".stick-album-photo",function(event){
      event.preventDefault();
      var img = $(this).find("img").attr("src");
      stick_album_photo(img);

      $("#modal-content").html("");
      $("#modal-window").hide();
    })

    var send_openalbum = 0;
    $("body").on("click",".openalbum",function(event){
      event.preventDefault();

      var aid = parseInt($(this).data("aid"));
      var action = $(this).attr("href");
      if(action && aid && !send_openalbum){
        $.ajax({
          url: action,
          data:"aid="+aid,
          dataType:"html",
          type:"GET",
          beforeSend:function(){
            send_openalbum = 1;
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
            send_openalbum = 0;
          }
        }); 
      }
    });

    





  	var sendFileForm = function(){
	        $("#form-add-file").ajaxForm();
	        var dForm = $("#form-add-file").serializeArray();
	        var action = $("#form-add-file").attr("action");
	        
	        
	        if(action){
				    $("#form-add-file").ajaxSubmit({
			        url:action,
			        //iframe: true,
			        data: dForm,
			        type:"POST",
			        dataType: 'json',
			        beforeSend:function(){
			        },
			        success:function(json){
                  if(json.res){
                    var img_path = json.hasOwnProperty("img_path") ? json.img_path : null;
                    printImage(img_path);
                  }
			        },
			        error:function(msg){
			        	console.log(msg);
			            //console.log("error upload file at profile \n"+msg.statusText + msg.responseText);
			        },
			        complete:function(){
			        }
			      });
	      }
  	}

    
    var generateBase64 = function(file){
      var reader = new FileReader();
        reader.readAsDataURL(file);
         
        var base = "";
        reader.onload = function () {
          var b = reader.result;

          var parts = b.split(";");
          if(parts.length && parts[0]){
            var type =  parts[0].split(":");
            if(type.length == 2  && (
              type[1] == "image/jpeg" ||
              type[1] == "image/png" ||
              type[1] == "image/jpg" ||
              type[1] == "image/gif"
            )){
              printImage(reader.result);
            }
          }
        };

        reader.onerror = function (error) {
          console.log('Error: ', error);
          base = false;
        };
    };


    var printImage = function(code){
        $("#message-file").val(code);
        $("#message-file").attr("data-album",0);
        $("#message-file_from_album").val(0);
        $(".target_loaded_image").html("<img src='"+code+"' width='150px'>");
        $("#loaded_image").show();
    }


  	$("body").on("change","#add-file",function(event){
  		
      //method save the file as base64
      // console.log(event);
      // var file = document.getElementById("add-file").files[0];

      // if(file){
      //   generateBase64(file);
      // }
      
      //method save the file in tje filesystem
      sendFileForm();
  	});
  	
    


    var send_cleartmpfile = 0;
    $("body").on("click","#clear_loaded_image",function(event){
      event.preventDefault();
      

      var tempFile = $("#message-file").val();
      var isAlbum = parseInt($("#message-file").attr("data-album"));
      var action = $(this).attr("href");

      if(action && !isAlbum && tempFile && !send_cleartmpfile){
        $.ajax({
          url: action,
          data:"tmpfile="+tempFile,
          dataType:"json",
          type:"GET",
          beforeSend:function(){
            send_cleartmpfile = 1;
          },
          success:function(json){
            if(json.res){
              $("#message-file").val("");
              $(".target_loaded_image").html("");
              $("#loaded_image").hide();
              $("#add-file").val("");
            }
          },
          error:function(msg){
            console.log(msg);
          },
          complete:function(){
            send_cleartmpfile = 0;
          }
        }); 
      }else if(isAlbum){
        $("#message-file").val("");
        $(".target_loaded_image").html("");
        $("#loaded_image").hide();
        $("#add-file").val("");
      }

    });

})