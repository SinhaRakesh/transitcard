$.each({
	//host:  ws://127.0.0.1:8890/
	// uuid : login customer unique id
	/* message = [
				'cmd'=>'' //register,notification,typing, stop typing, user left,
				'message'=>'msg string',
				'file_url'=>,
				'video_link'=>,
				'profile_image'=>
				'name'=>
			]
	*/
	runWebSocketClient: function(host, uu_id,other={}){
		if (window.WebSocket) {
	      console.log("ws is supported");
	    } else {
	      alert('your browser doest not support WebSocket');
	      return;
	    }
	    
		try {
		      socket = new WebSocket(host);
		      socket.onopen = function () {
		      		console.log('ws client user register request'+uu_id);
		      		if(other.length > 0)
		        		socket.send(JSON.stringify({'cmd':'register','uu_id':uu_id,'name':other.name}));
		        	else
		        		socket.send(JSON.stringify({'cmd':'register','uu_id':uu_id}));
		        	return;
		      };

			socket.onmessage = function (msg) {
				console.log('ws client on message');
				if(msg.data.length == 0){
		        	console.log('msg.data.length == 0, returning');
		        	return;
				}

				var $data = JSON.parse(msg.data);

				if($data.cmd == "registered"){
					if($('.apt-chatwith').attr('data-aptchatwith') === $data.register_uu_id){
						$('.apt-chat-status-online').remove();
						$('.apt-chatwith').append('<span class="label bg-green apt-chat-status-online">Online</span>');
					}
				}else if($data.message.length > 0){
					var title = "Notification";
					var type = "notice";
					var desktop = true;
					var sticky = true;
					var icon = undefined;
					if (("title" in $data) !=false) title = $data.title;
					if (("type" in $data) !=false) type = $data.type;
					if (("desktop" in $data) == false)
						desktop = undefined;
					else
						desktop = $data.desktop;
					if (("sticky" in $data) ==false) skicky = undefined;
					if (("icon" in $data) !=false) icon = undefined;
					
					if($data.cmd == "chatmessage"){
						var $send_html = '<div class="direct-chat-msg right">'+
							'<div class="direct-chat-info clearfix">'+
			            		'<span class="direct-chat-name pull-left">'+$data.from+'</span>'+
			            		'<span class="direct-chat-timestamp pull-right">'+$data.send_date+'</span>'+
			          		'</div>'+
			          		'<img src="'+($data.from_image)+'" class="direct-chat-img"/>'+
			          		'<div class="direct-chat-text">'+$data.message+'</div>'+
						'</div>';
						$('.direct-chat-messages').append($send_html);
						$.univ().chatScrollToTop();
					}else{
						$.univ().notify(title, $data.message, type, desktop, undefined, sticky, icon);
					}

				}
				if(("js" in $data) !=false){
					eval($data.js);
				}
				return;
			};

			socket.onclose = function (e) {
				console.log('ws connection is closed '+e.reason);
				setTimeout(function() {
					// console.log('reload auto '+host);
					// console.log('reload '+uu_id);
					$.univ().runWebSocketClient(host,uu_id);
				}, 500);
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

	},
	chatScrollToTop: function(){
		var $messages = $('.direct-chat-messages');
		$messages.animate({'scrollTop':$messages[0].scrollHeight});
	}
}, $.univ._import);

jQuery.widget("ui.eapartment_chatpanel",{
	options:{
		FADE_TIME:150, // ms
  		TYPING_TIMER_LENGTH:400, // ms
  		MESSAGE_AREA_CLASS:'.direct-chat-messages',
  		MESSAGE_INPUT_CLASS:'.msginputbox',
  		username:"",
  		connected:false,
  		lastTypingTime:"",
  		typing:false,
  		lastTypingTime:"",
  		wshost:"",
  		uu_id:"",
  		chat_with:0
	},
	_create: function(){
		// Initialize varibles
		this.messages = $(this.options.MESSAGE_AREA_CLASS); // Messages area
		this.inputMessage = $(this.options.MESSAGE_INPUT_CLASS); // Input message input box
		this.currentInput = this.inputMessage;

		this.keyDownEvent();
		// this.debug();

	},

	runWebSocketClient: function(){
		// var self = this;
		// $.univ().runWebSocketClient(self.options.wshost,self.options.uu_id,{'chat_with':self.options.chat_with});
	},

	keyDownEvent: function(){
		var self = this;
		$window = $(window);
		$window.keydown(function (event) {
		    // Auto-focus the current input when a key is typed
		    if (!(event.ctrlKey || event.metaKey || event.altKey)) {
		      self.currentInput.focus();
		    }

		    //used when chat panel is shifted over js widget
		    // When the client hits ENTER on their keyboard
		    // if (event.which === 13) {
		    //   if (username) {
		        // self.sendMessage();
		        // self.options.typing = false;
		        // event.stopPropagation();
		    //     socket.emit('stop typing');
		    //   } else {
		    //     setUsername();
		    //   }
		    // }
		  });

		self.inputMessage.on('input', function() {
			self.updateTyping();
		});
	},

	updateTyping: function() {
		var self = this;
		if (self.options.connected) {
			if (!self.options.typing) {
				self.options.typing = true;

				console.log('user is typing')
				// send socket message to user is typing
				// try{
					// var socket = self.runWebSocketClient();
				   	// socket.send(JSON.stringify({'cmd':'typing','msg':'user is typing'}));
				// }catch(e){
				// 	console.log('error log is here');
				// 	console.log(e);
				// }
				// socket.emit('typing');

			}
			self.options.lastTypingTime = (new Date()).getTime();

			setTimeout(function () {
				var typingTimer = (new Date()).getTime();
				var timeDiff = typingTimer - self.options.lastTypingTime;
				if (timeDiff >= self.options.TYPING_TIMER_LENGTH && self.options.typing) {
					// send stop typing message to hoa server
					// socket.emit('stop typing');
					self.options.typing = false;
					console.log('stop typing');
				}
			}, self.options.TYPING_TIMER_LENGTH);
	    }
  	},

  	// Adds the visual chat message to the message list
  	addChatMessage:function (data, options) {
  		var self = this;
    	// Don't fade the message in if there is an 'X was typing'
    	var $typingMessages = self.getTypingMessages(data);
    	options = options || {};
    	if ($typingMessages.length !== 0) {
      		options.fade = false;
      		$typingMessages.remove();
    	}

	    var $usernameDiv = $('<span class="username"/>').text(data.username);
	    var $messageBodyDiv = $('<span class="messageBody">').text(data.message);

	    var typingClass = data.typing ? 'typing' : '';
	    var $messageDiv = $('<li class="message"/>')
	      .data('username', data.username)
	      .addClass(typingClass)
	      .append($usernameDiv, $messageBodyDiv);

	    self.addMessageElement($messageDiv, options);
  	},

  	// Adds a message element to the messages and scrolls to the bottom
	// el - The element to add as a message
	// options.fade - If the element should fade-in (default = true)
	// options.prepend - If the element should prepend
	//   all other messages (default = false)
	addMessageElement: function (el, options) {
		var self = this;
		var $el = $(el);
		// Setup default options
		if (!options) {
			options = {};
		}
		if (typeof options.fade === 'undefined') {
			options.fade = true;
		}
		if (typeof options.prepend === 'undefined') {
			options.prepend = false;
		}

		// Apply options
		if (options.fade) {
			$el.hide().fadeIn(self.options.FADE_TIME);
		}
		if (options.prepend) {
			self.messages.prepend($el);
		} else {
			self.messages.append($el);
		}
		self.messages[0].scrollTop = self.messages[0].scrollHeight;

	},

	// Prevents input from having injected markup
	cleanInput: function (input) {
		return $('<div/>').text(input).text();
	},

	sendMessage:function () {
		var self = this;
		var message = self.inputMessage.val();
		// Prevent markup from being injected into the message
		message = self.cleanInput(message);
		// if there is a non-empty message and a socket connection
		if (message && self.options.connected){
			self.inputMessage.val('');
		  	self.addChatMessage({username: self.options.username,message: message});

		  	$.ajax({
					url: 'index.php?page=xepan_commerce_designer_save',
					cache:false,
					async:false,
					type: 'POST',
					datatype: "json",
					data:{}
				}).done(function(ret){

				}).fail(function() {
					console.log("message saved error");

				}).always(function() {
					console.log("message saved complete");
				});
		  	// save message to database
		  	// tell server to execute 'new message' and send along one parameter
		  	// socket.emit('new message', message);
		}
	},
  	getTypingMessages:function (data) {
		return $('.typing.message').filter(function (i) {
			return $(this).data('username') === data.username;
		});
	},

	debug: function(){
		var self = this;
		// console.log(self.messages);
		// console.log(self.inputMessage);
	}
});
