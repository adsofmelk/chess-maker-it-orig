'use strict';
// Vue.config.devtools = true;
window.onload = function () {


    var appGameInit = new window.Vue({
        el: '#app-game-init',
        data: {
            status: 'Esperando',
            mostrar: true,
            suscri: false,
            spinnerUI: true,
            message: '',
            channelUser: '',
            channelGame: '',
            chat: false
        },
        methods: {
            conectar: function () {
                var thisApp = this;
                window.ablyapp = new Ably.Realtime({
                    authUrl: 'authably'
                });
                window.ablyapp.connection.on('connected', function () {
                    appGameInit.status = 'Buscando jugador';
                    appGameInit.mostrar = false;
                    appGameInit.suscri = true;
                    thisApp.$http.get('getchannel').then(function (response) {
                        window.channel = window.ablyapp.channels.get(response.body.nameChannel);
                        appGameInit.channelUser = response.body.nameChannel;
                        window.channel.unsubscribe();
                        window.channel.subscribe('private_user', function (message) {
                            if (message.data.status == 300) {
                                $('#block-ui').show();
                            }
                            if (message.data.status == 201) {
                                window.ablyappGame = new Ably.Realtime({
                                    authUrl: 'authably',
                                    authParams: {
                                        begin: true
                                    }
                                });
                                window.ablyappGame.connection.on('connected', function () {
                                    appGame.ablyChat(message.data.nameChannelGame);
                                    appGameInit.spinnerUI = false;
                                    appGameInit.status = 'Jugando!!!';
                                    appGame.chat = true;
                                    // window.renderTable(message.data.distribution);
                                    window.boardGame.distribution = message.data.distribution;
                                    window.boardChess.asignDistribution();
                                });
                            }
                        });
                        window.channel.presence.enter();
                        appGameInit.findPlayer();
                        $('#block-ui-load').hide();
                        window.boardChess.render();
                    });
                });
            },
            suscribirse: function () {
                window.channelDamos = window.ablyapp.channels.get('damos');
                appGameInit.suscri = false;
                window.channelDamos.subscribe('greeting', function (message) {
                    if (message.data.status == 300) {
                        location.reload();
                    }
                    appGameInit.message = appGameInit.message + 'Nuevo mensaje del servidor: ' + message.data + '<br>';
                });
                window.channelDamos.presence.subscribe('greeting');
                // window.channelDamos.presence.subscribe('enter', function(member) {
                //  alert('Member ' + member.clientId + ' entered');
                //  // appGameInit.message = appGameInit.message + 'Nuevo mensaje del servidor: ' + message.data + '<br>';
                //  // alert("Received a greeting message in realtime: " + message.data);
                // });
                window.channelDamos.presence.enter();
            },
            findPlayer: function () {
                this.$http.get('findplayer').then(function (response) {
                    if (response.body.status == 2) {
                        window.ablyappGame = new Ably.Realtime({
                            authUrl: 'authably',
                            authParams: {
                                begin: true
                            }
                        });
                        window.ablyappGame.connection.on('connected', function (aa, ff) {
                            appGame.ablyChat(response.body.channel);
                            appGameInit.spinnerUI = false;
                            appGame.chat = true;
                            appGameInit.status = 'Jugando!!!';
                            appGameInit.channelGame = response.body.channel;
                            // window.renderTable(response.body.distribution);
                            window.boardGame.distribution = response.body.distribution;
                            window.boardChess.asignDistribution();
                        });
                    }
                });
            },
        }
    });

    appGameInit.conectar();
    window.appGameInit = appGameInit;

    /**
     * app game
     */
    var appGame = new window.Vue({
        el: '#app-game',
        data: {
            distribution: 'Esperando',
            mostrar: true,
            suscri: false,
            spinnerUI: true,
            message: '',
            channelUser: '',
            channelGame: '',
            chat: false,
            nameUser: $('#name-user').data('name')
        },
        methods: {
            writeChat: function () {
                if ($('#text-chat').val() == '') {
                    $('#text-chat').focus();
                    return false;
                }
                window.channelGame.publish('chat', {
                    user: $('#name-user').data('name'),
                    message: $('#text-chat').val()
                });
                $('#text-chat').val('');
            },
            ablyChat: function (name) {
                console.log('se habilita chat');
                window.channelGame = window.ablyappGame.channels.get(name);
                window.channelGame.subscribe('chat', function (message) {
                    var nameUser = (appGame.nameUser == message.data.user) ? 'Yo' : message.data.user;
                    appGame.message = '<p><strong>' + nameUser + '</strong>: ' + message.data.message + appGame.message + '</p>';
                });
                window.channelGame.subscribe('moves', function (message) {
                    var box = message.data.box,
                        piece = message.data.piece;
                    if ($('#' + box).hasClass('ispiece')) {
                        $('#' + box).parent().append(document.getElementById(piece));
                        $('#' + box).remove();
                    } else {
                        $('#' + box).append(document.getElementById(piece));
                    }
                });
            }
        }
    });
    window.appGame = appGame;
    window.renderTable = function (distribution) {
        var rowWhite = [],
            rowBlack = [],
            tableCod = [],
            table = $('#example-table');
        for (var i = 0; i < 8; i++) {
            if (i % 2 == 0) {
                rowWhite.push(boxWhite);
                rowBlack.push(boxBlack);
            } else {
                rowWhite.push(boxBlack);
                rowBlack.push(boxWhite);
            }
        }
        for (var i = 0; i < 8; i++) {
            if (i % 2 == 0) {
                // table.append(rowWhite.join(''));
                tableCod = tableCod.concat(rowWhite);
            } else {
                // table.append(rowBlack.join(''));
                tableCod = tableCod.concat(rowBlack);
            }
            // console.log(i, rowWhite.join(), rowWhite.join())
        }
        table.html('');
        // console.log(tableCod);
        // console.log('uniendo');
        // console.log(tableCod.join(''));
        // console.log('replace');
        // console.log(tableCod.join('').replace(/{/g,''));
        // colocando fichas
        // distribution = distribution || "d5*te4ag6cc6*tf5ae8cc5*b*3can01.01";
        distribution = distribution || "28,37,23,19,30,5,27";
        var fichas = distribution.split(',');
        tableCod[fichas[0]] = boxBall;
        tableCod[fichas[1]] = tableCod[fichas[1]].split('{');
        tableCod[fichas[1]][1] = pieceTowerWhite + '</div>';
        tableCod[fichas[1]] = tableCod[fichas[1]].join('');
        tableCod[fichas[2]] = tableCod[fichas[2]].split('{');
        tableCod[fichas[2]][1] = pieceTowerBlack + '</div>';
        tableCod[fichas[2]] = tableCod[fichas[2]].join('');
        tableCod[fichas[3]] = tableCod[fichas[3]].split('{');
        tableCod[fichas[3]][1] = pieceAlfilWhite + '</div>';
        tableCod[fichas[3]] = tableCod[fichas[3]].join('');
        tableCod[fichas[4]] = tableCod[fichas[4]].split('{');
        tableCod[fichas[4]][1] = pieceAlfilBlack + '</div>';
        tableCod[fichas[4]] = tableCod[fichas[4]].join('');
        tableCod[fichas[5]] = tableCod[fichas[5]].split('{');
        tableCod[fichas[5]][1] = pieceHorseWhite + '</div>';
        tableCod[fichas[5]] = tableCod[fichas[5]].join('');
        tableCod[fichas[6]] = tableCod[fichas[6]].split('{');
        tableCod[fichas[6]][1] = pieceHorseBlack + '</div>';
        tableCod[fichas[6]] = tableCod[fichas[6]].join('');
        window.tableCod = tableCod;
        // table.append(tableCod.join('').replace(/{/g,''));
        // $('#example-table').children().each(function(i, el){
        //  $(el).attr('id', 'piece' + i);
        // });
        window.boardGame = window.boardChess.constructor();
        for (var i in window.boardGame) {
            var className = 'box-white';
            var box = window.boardGame[i];
            if (box.codeColor > 0) {
                className = 'box-black';
            }
            var html = '<div id="' + box.name + '" class="box-chess ' + className + '" ondrop="window.drop(event)" ondragover="window.allowDrop(event)"></div>';
            table.append(html);
            console.log(`Location {i}`, window.boardGame[i]);
        }
    };

    window.allowDrop = function (ev) {
        ev.preventDefault();
    };

    window.drag = function (ev) {
        ev.dataTransfer.setData("text", ev.target.id);
    };

    window.drop = function (ev) {
        ev.preventDefault();
        var data = ev.dataTransfer.getData("text");
        console.log('piece actacker ', window.boardGame.pieces[window.boardGame.piecesMap.indexOf(data)]);
        if ($(ev.target).hasClass('ispiece')) {
            var pieceAttacker = window.boardGame.pieces[window.boardGame.piecesMap.indexOf(data)],
                pieceAttacked = window.boardGame.pieces[window.boardGame.piecesMap.indexOf($(ev.target).attr('id'))],
                posInitial = $('#' + data).parent().data('location').split(','),
                posFinal = $(ev.target).parent().data('location').split(',');
            console.log('element DOM ', $(ev.target));
            console.log('box Final ', $(ev.target).parent().data('location').split(','));
            console.log('box Inicial ', $('#' + data).parent().data('location').split(','));
            console.log('piece attacked ', window.boardGame.pieces[window.boardGame.piecesMap.indexOf($(ev.target).attr('id'))]);
            if (pieceAttacker.color == pieceAttacked.color) {
                console.log('no se puede matar la ficha del mismo color');
                return false;
            }
            var checkmove = window.checkMove({
                'posInitial': posInitial,
                'posFinal': posFinal,
                'type': pieceAttacker.type
            });
            if (!checkmove) {
                return false;
            }
            console.log(posInitial, posFinal);
        } else {
            var pieceAttacker = window.boardGame.pieces[window.boardGame.piecesMap.indexOf(data)],
                posInitial = $('#' + data).parent().data('location').split(','),
                posFinal = $(ev.target).data('location').split(',');
            // console.log('element DOM ',$(ev.target))
            // console.log('box Inicial ',$('#'+data).parent().data('location').split(','))
            // console.log('box Final ',$(ev.target).data('location').split(','));
            console.log(posInitial, posFinal);
            var x = 0,
                y = 0;
            x = posInitial[0] * 1 - posFinal[0] * 1;
            y = posInitial[1] * 1 - posFinal[1] * 1;
            var posDifference = [Math.abs(x), Math.abs(y)];
            // console.log('diferencia', posDifference )
            var checkmove = window.checkMove({
                'posInitial': posInitial,
                'posFinal': posFinal,
                'type': pieceAttacker.type
            });
            if (!checkmove) {
                return false;
            }
        }
        // console.log($(ev.target).attr('id'));
        if ($(ev.target).hasClass('ispiece')) {
            $(ev.target).parent().append(document.getElementById(data));
            $(ev.target).remove();
        } else {
            ev.target.appendChild(document.getElementById(data));
        }
        //casilla y pieza
        window.channelGame.publish('moves', {
            'box': $(ev.target).attr('id'),
            'piece': data
        });
    };

    window.boardChess = {
        'constructor': function (args) {
            args = args || {};
            var bColor = true,
                board = [];
            for (var i = 0; i < 8; i++) {
                if (i % 2 == 0) {
                    bColor = true;
                } else {
                    bColor = false;
                }
                for (var j = 0; j < 8; j++) {
                    var color = 'black',
                        codeColor = 1;
                    if (bColor) {
                        color = 'white',
                            codeColor = 0;
                        bColor = false;
                    } else {
                        bColor = true;
                    }
                    board.push(this.box.create({
                        location: [i, j],
                        color: color,
                        codeColor: codeColor,
                        name: 'box-' + i.toString() + j.toString()
                    }));
                }
            }
            return {
                boxes: board,
                date: new Date().toString(),
                nameGame: 'Test ' + new Date().getTime().toString(),
                distribution: '',
                container: args.container || 'body',
                asignDistribution: this.asignDistribution,
                pieces: []
            };
        },
        'box': {
            'create': function (args) {
                args = args || {};
                args.location = args.location || [0, 0];
                args.color = args.color || 'white';
                args.codeColor = args.codeColor || 0;
                args.name = args.name || 'box-';
                args.piece = args.piece || '';
                return {
                    location: args.location,
                    color: args.color,
                    codeColor: args.codeColor,
                    name: args.name,
                    piece: args.piece
                };
            }
        },
        'piece': {
            'create': function (args) {
                args = args || {};
                args.location = args.location || [0, 0];
                args.color = args.color || 'white';
                args.codeColor = args.codeColor || 0;
                args.name = args.name || 'piece-';
                args.type = args.type || 0;
                return {
                    location: args.location,
                    color: args.color,
                    codeColor: args.codeColor,
                    name: args.name,
                    type: args.type
                };
            }
        },
        'render': function () {
            window.boardGame = this.constructor({
                'container': '#board-game'
            });

            var boxes = window.boardGame.boxes,
                html = '';
            for (var i in boxes) {
                var className = 'box-white';
                var box = boxes[i];
                if (box.codeColor > 0) {
                    className = 'box-black';
                }
                html = html + '<div data-location="' + box.location + '" id="' + box.name + '" class="box-chess ' + className + '" ondrop="window.drop(event)" ondragover="window.allowDrop(event)"></div>';
                // console.log(`Location {i}`,window.boardGame.boxes[i])
            }
            $(window.boardGame.container).append(html);
        },
        'asignDistribution': function () {
            var distribution = window.boardGame.distribution;
            var pieces = distribution.split(',');
            console.log(pieces);
            // var el = document.getElementById(window.boardGame.boxes[pieces[0]].name);
            // el.ondrop=null;
            // el.ondragover=null;
            $('#' + window.boardGame.boxes[pieces[0] - 1].name).html('').addClass('box-main').append($('#piece-ball'));
            $('#piece-ball')[0].draggable = false;
            $('#' + window.boardGame.boxes[pieces[1] - 1].name).html('').append($('#piece-tw'));
            $('#' + window.boardGame.boxes[pieces[2] - 1].name).html('').append($('#piece-aw'));
            $('#' + window.boardGame.boxes[pieces[3] - 1].name).html('').append($('#piece-hw'));
            $('#' + window.boardGame.boxes[pieces[4] - 1].name).html('').append($('#piece-tb'));
            $('#' + window.boardGame.boxes[pieces[5] - 1].name).html('').append($('#piece-ab'));
            $('#' + window.boardGame.boxes[pieces[6] - 1].name).html('').append($('#piece-hb'));
            var mtypes = [4, 1, 2, 3, 1, 2, 3],
                mcolors = ['none', 'white', 'white', 'white', 'black', 'black', 'black'],
                mnames = ['piece-ball', 'piece-tw', 'piece-aw', 'piece-hw', 'piece-tb', 'piece-ab', 'piece-hb'],
                piecesJs = [],
                piecesJsMap = [];
            for (var i = 0; i < 7; i++) {
                piecesJsMap.push(mnames[i]);
                piecesJs.push(this.piece.create({
                    location: window.boardGame.boxes[pieces[i] - 1].location,
                    type: mtypes[i],
                    color: mcolors[i],
                    name: mnames[i]
                }));
            }
            window.boardGame.pieces = piecesJs;
            window.boardGame.piecesMap = piecesJsMap;
        }
    };


    window.checkMove = function (params) {
        // type 0 => torre, 1 => alfil, 2 => caballo
        var posInitial = params.posInitial,
            posFinal = params.posFinal,
            type = params.type,
            x1 = posInitial[0] * 1,
            x2 = posFinal[0] * 1,
            xf = Math.abs(x1 - x2),
            y1 = posInitial[1] * 1,
            y2 = posFinal[1] * 1,
            yf = Math.abs(y1 - y2);
        console.log('location diference', xf, yf);
        switch (type) {
            case 1:
                // tower
                if ((xf == 0 || yf == 0) && xf != yf) {
                    console.log('tower validate correct ', xf, yf);
                    return true;
                } else {
                    alert('tower validate incorrect');
                    console.log('tower validate incorrect ', xf, yf);
                    return false;
                }
                break;
            case 2:
                // alfil
                if (xf == yf) {
                    console.log('alfil validate correct ', xf, yf);
                    return true;
                } else {
                    alert('alfil validate incorrect');
                    console.log('alfil validate incorrect ', xf, yf);
                    return false;
                }
                break;
            case 3:
                // horse
                if ((xf + yf) == 3) {
                    console.log('horse validate correct ', xf, yf);
                    return true;
                } else {
                    alert('horse validate incorrect');
                    console.log('horse validate incorrect ', xf, yf);
                    return false;
                }
                break;
            default:
                return false;
        }
    };
};


function calLocationsAlfil(loc1, loc2) {
    let rows = calPoints(loc1[0], loc2[0]),
        cols = calPoints(loc1[1], loc2[1])

    for (let i in rows) {
        console.log(`Location Box ${i*1+1*1} => ${rows[i]}, ${cols[i]}`)
    }

}

function calLocationsTower(loc, n1, isrow) {
    let points = calPoints(loc[0], loc[1])

    if (isrow) {
        for (let i in points) {
            console.log(`Location Box ${i*1+1*1} => ${n1}, ${points[i]}`)
        }
    } else {
        for (let i in points) {
            console.log(`Location Box ${i * 1 + 1 * 1} => ${points[i]}, ${n1}`)
        }
    }
}

function calPoints(n1, n2) {
    let points = [];

    if (n1 > n2) {
        for (let i = n1 - 1; i > n2; i--) {
            points.push(i)
        }
    } else {
        for (let i = n1 + 1; i < n2; i++) {
            points.push(i)
        }
    }

    return points;
}

console.log('Alfil 2,6 => 5,3')
calLocationsAlfil([2, 6], [5, 3])

console.log('Alfil 5,3 => 2,0')
calLocationsAlfil([5, 3], [2, 0])

console.log('Alfil 2,0 => 6,4')
calLocationsAlfil([2, 0], [6, 4])

console.log('Tower 3,5 => 3,1')
calLocationsTower([5, 1], 3, 1)

console.log('Tower 3,1 => 3,7')
calLocationsTower([1, 7], 3, 1)

console.log('Tower 0,4 => 7,4')
calLocationsTower([0, 7], 4, 0)