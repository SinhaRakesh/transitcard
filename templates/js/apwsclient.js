$.each({
	//host:  ws://127.0.0.1:8890/
	// uuid : login customer unique id

	runWebSocketClient: function(host, uu_id){
		if (window.WebSocket) {
	      console.log("ws is supported");
	    } else {
	      alert('your browser doest not support WebSocket');
	      return;
	    }
	    
		try {
		      socket = new WebSocket(host);
		      socket.onopen = function () {
		          // console.log('ws connection is open successfully');
		          socket.send(JSON.stringify({'cmd':'register','uu_id':uu_id}));
		          // console.log('ws registere '+uu_id);
		          return;
		      };

		      socket.onmessage = function (msg) {
		      	console.log('ws on message');
		      	
		        // console.log(msg);
		        if(msg.data.length == 0){
		        	// console.log('msg.data.length == 0, returning');
		        	return;
		        }

		        var $data = JSON.parse(msg.data);
		        // console.log('ws Parsed data');
		        // console.log($data);
		        // console.log($data.message.length);
				if($data.message.length > 0){
						
						var title= "Notification";
						var type= "notice";
						var desktop = true;
						var sticky = true;
						var icon = undefined;

						if (("title" in $data) !=false) title = $data.title;
						if (("type" in $data) !=false) type = $data.type;
						if (("desktop" in $data) ==false) 
							desktop = undefined;	
						else
							desktop = $data.desktop;	

						if (("sticky" in $data) ==false) skicky = undefined;						
						if (("icon" in $data) !=false) icon = undefined;						

						// $.univ().notify(title, $data.message, type, desktop, undefined, sticky, icon);
				  }
				  if (("js" in $data) !=false){
				  	eval($data.js);
				  }
				return;
		      };
		      socket.onclose = function (e) {
		          // console.log('ws connection is closed '+e.reason);
		          setTimeout(function() {
		          		// console.log('reload auto '+host);
		          		// console.log('reload '+uu_id);
				      	$.univ().runWebSocketClient(host,uu_id);
				    }, 5000);
		          return;
		      };
		      socket.onerror = function(err){
		      	console.log('ws connection on error');
		      	console.log(err);
		      	return;
		      };
		  } catch (e) {
		      console.log(e);
		  }
	}
}, $.univ._import);