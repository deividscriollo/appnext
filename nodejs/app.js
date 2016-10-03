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
    // socket.emit('chat:updatelista', 'update'); 

	socket.on('chat:join', function(data){
		if (data!=null) {
		for (var i = 0; i < data.length; i++) {
		// store the username in the socket session for this client
		socket.username = data[i]['para'];
		// store the room name in the socket session for this client
		socket.room = data[i]['chat_id'];
		socket.join(data[i]['chat_id']);
		console.log(' conectado con '+data[i]['para']);
		}
		}
		
	});

	socket.on('chat:updateRooms', function(){
		socket.broadcast.emit('chat:updateRooms');
	});

	socket.on('chat:salir', function(data){
		console.log('salir de la sala'+socket.room);
		socket.leave(socket.room);
	});

	socket.on('chat:sendMensaje', function(data){
		socket.broadcast.to(socket.room).emit('chat:update', data);
		socket.broadcast.to(socket.room).emit('chat:updatelista', data);
	});



});