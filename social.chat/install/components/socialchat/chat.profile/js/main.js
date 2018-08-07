$(function(){    

	var send_showalbum = 0;
    $("body").on("click",'.showalbum',function(event){
      event.preventDefault();
      var action = $(this).attr("href");

      var aid = parseInt($(this).data("aid"));
      var mbid = parseInt($(this).data("mbid"));
     
      if(action && aid && mbid && !send_showalbum){
        $.ajax({
            url:action,
            type:"GET",
            data:"aid="+aid+"&mbid="+mbid,
            dataType:"html",
            beforeSend:function(){
              send_showalbum = 1;
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
              send_showalbum = 0;
            }
          });
      }
    });
    
})