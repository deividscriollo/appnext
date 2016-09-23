var express = require('express');
var app = express();
var server = require('http').Server(app);
var io = require('socket.io')(server);

app.use(express.static(__dirname));

server.listen(8080, function(){
    console.log('Listening at port 8080');
});

io.sockets.on('connection', function (socket) {
    console.log('cliente Conectado');
	socket.on('chat:join', function(data){
		// store the username in the socket session for this client
		socket.username = data.user_id;
		// store the room name in the socket session for this client
		socket.room = data.chat_id;
		socket.join(data.chat_id);
		console.log('conectado a'+socket.room);
	});

	socket.on('chat:salir', function(data){
		socket.leave(socket.room);
	});

	socket.on('chat:sendMensaje', function(data){
		socket.broadcast.to(socket.room).emit('chat:update', data);
	});

});