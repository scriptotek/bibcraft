var WebSocketServer = require('../ws').Server,
    _ = require('../underscore'),
    wss = new WebSocketServer({port: 8080}),
    clients = [],
    clientId = 0,
    red = '\033[31m',
    blue = '\033[34m',
    green = '\033[32m',
    reset = '\033[0m';

console.log(red + 'THIS IS BIBCRAFT SERVER RUNNING' + reset);

wss.on('connection', function(ws) {
    var thisId = clientId++;

    clients[thisId] = { socket: ws, role: 'unknown' };
    console.log(green + '# Client connected: ' + thisId + reset);

    ws.on('message', function(message) {
       console.log(green + 'FROM' + reset + ' client %d: %s', thisId, message);
       //try {
            var data = JSON.parse(message);
            if (data.msg == 'hello') {
                if (data.role == 'frontend') {
                    console.log('# Client %d identified as frontendclient', thisId);
                    clients[thisId].role = 'frontend';
                    backends = _.reduce(clients, function(cli, count){ return count + cli.role=='backend' ? 1 : 0; }, 0);
                    //clients[thisId].socket.send('{"msg": "hello"}, "role": "backend"}') // ??
                } else if (data.role == 'backend') {
                    console.log('> Client %d identified as backendclient', thisId);
                    clients[thisId].role = 'backend';
                }
            } else {
                console.log('Forwarding to %s ',data.rcpt)
                _.each(clients, function(idx, client) {
                    if (client.role == data.rcpt) {
                        console.log(blue + 'FORWARD TO' + reset + ' client ' + idx);
                        client.socket.send(message);
                    }
                });
            }
                /*
                case 'lookup-book':
                    console.log('Looking up', data.object_id);
                    var db = new sqlite3.Database('../storage/bibcraft.sqlite'),
                        query = 'SELECT item_id, author, title FROM items WHERE objektid="' + data.object_id + '"';
                    console.log(query);
                    db.each(query, function(err, row) {
                        console.log(row);
                        clients[thisId].send({
                            id: row.id,
                            author: row.author,
                            title: row.title
                        });
                        //console.log(row.id + ": " + row.title);
                    });
                */

        //} catch (e) {
        //    console.log('could not parse msg');
        //}
    });

    ws.on('close', function() {
        clients[thisId].role = 'closed';
        console.log(red + 'Client disconnected: ' + thisId + reset);
    });

});

