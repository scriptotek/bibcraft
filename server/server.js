var WebSocketServer = require('ws').Server,
    wss = new WebSocketServer({ port: 8080 }),
    clients = [],
    clientId = 0,
    red = '\033[31m',
    blue = '\033[34m',
    green = '\033[32m',
    reset = '\033[0m';

console.log(red + 'THIS IS BIBCRAFT SERVER RUNNING' + reset);


function sendStatus() {
    'use strict';

    var backendCount, frontendCount;

    backendCount = clients.reduce(function(count, obj){
        return count + (obj.role === 'backend' ? 1 : 0);
    }, 0);

    frontendCount = clients.reduce(function(count, obj){
        return count + (obj.role === 'frontend' ? 1 : 0);
    }, 0);

    console.log('Backends: ' + backendCount + ', frontends: ' + frontendCount);

    clients.forEach(function(client) {

        if (client.role === 'frontend' || client.role === 'backend') {
            client.socket.send(JSON.stringify({
                msg: 'status',
                backends: backendCount,
                frontends: frontendCount
            }));
        }

    });
}

wss.on('connection', function(ws) {
    'use strict';

    var thisId = clientId++;

    clients[thisId] = {
        socket: ws,
        role: 'unknown'
    };

    console.log(green + '# Client connected: ' + thisId + reset);

    ws.on('message', function(message) {

        var data;

        console.log(green + 'FROM' + reset + ' client %d: %s', thisId, message);
        try {
            data = JSON.parse(message);
        } catch (e) {
            console.log(red + ' Invalid JSON!' + reset);
            return;
        }

        if (data.msg === 'hello') {
            if (data.role === 'frontend') {

                console.log('# Client %d identified as frontend', thisId);
                clients[thisId].role = 'frontend';

            } else if (data.role === 'backend') {

                console.log('> Client %d identified as backend', thisId);
                clients[thisId].role = 'backend';

            }

            sendStatus();

        } else {

            console.log('Forwarding to %s ', data.rcpt);

            clients.forEach(function(client, idx) {
                if (client.role === data.rcpt) {
                    console.log(blue + 'FORWARD TO' + reset + ' client ' + idx);
                    client.socket.send(message);
                }
            });

        }

    });

    ws.on('close', function() {

        clients[thisId].role = 'closed';
        console.log(red + 'Client disconnected: ' + thisId + reset);

        sendStatus();

    });

});

