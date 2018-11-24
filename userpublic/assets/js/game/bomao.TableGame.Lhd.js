//龙虎斗类,
(function(host, TableGame, undefined) {
    var defConfig = {

    };



    var pros = {
        init: function(cfg) {
            var me = this;
            me._areas = {};
            me._methods = {};
        },
        getDeskTopDom: function() {
            return this._deskTop || (this._deskTop = $('#J-desktop'));
        },
        addArea: function(opt) {
            var me = this,
                area = new host.TableGame.Area(opt),
                chips = new host.TableGame.Chips();

            chips.addEvent('delLastChip_after', function(e, chip) {
                area.fireEvent('cancelLastChip', chip);
            });
            area.chips = chips;
            me._areas[opt['name_en']] = area;

        },
        initDeskTop: function(areasConfig) {
            var me = this,
                html = [],
                it;
            $.each(areasConfig, function(i) {
                it = this;
                if (i < 6) {
                    html.push('<div data-action="addchip" data-name="' + it['name_en'] + '" style="width:' + it['width'] + 'px;height:' + it['height'] + 'px;left:' + it['left'] + 'px;top:' + it['top'] + 'px;background-position:' + it['bgPosition'][0] + 'px ' + it['bgPosition'][1] + 'px" class="area area-' + it['name_en'] + '">');
                } else {
                    html.push('<div data-action="addchip" data-name="' + it['name_en'] + '" style="width:' + it['width'] + 'px;height:' + it['height'] + 'px;right:' + it['right'] + 'px;top:' + it['top'] + 'px;background-position:' + it['bgPosition'][0] + 'px ' + it['bgPosition'][1] + 'px" class="area area-' + it['name_en'] + '">');
                }

                if (it['oddsPos']) {
                    html.push('<div style="left:' + it['oddsPos'][0] + 'px;top:' + it['oddsPos'][1] + 'px;" class="odds">1:' + it['prize_odds'] + '</div>');
                }
                html.push('</div>');
                me.addArea(it);
            });
            $(html.join('')).appendTo(me.getDeskTopDom());
        },
        editSubmitData: function(data) {
            var balls = data['balls'];
            data['balls'] = encrypt(JSON.stringify(balls));
            data['is_encoded'] = 1;
            return data;
        },
        getSubmitData: function() {
            var me = this,
                areas = me.getAreas(),
                _area,
                _chips,
                _money = 0,
                i = 0,
                len = areas.length,
                amount = 0,
                num = 1,
                way = "",
                type = "",
                wayId = 1,
                ball = "",
                ball = 0,
                multiple = 1,
                prize_group = 0,
                result = {},
                methods = me.getGameMethods(),
                wayIdAndBall={},
                method = {};

            me._methods = me.getGameMethods();
            result['gameId'] = me.getConfig('gameId');
            result['isTrace'] = 0;
            result['traceWinStop'] = 0;
            result['traceStopValue'] = 0;
            result['balls'] = [];
            $.each(areas, function() {
                var _area = this,
                    _chips = _area.getChipsCase(),
                    _money = 0,
                    wayId = 0;

                // 根据areaName获得这个area的wayId以及ball
                if (_chips.length > 0) {
                    $.each(_chips, function() {
                        _money += this.getMoney();
                    });

                    // 倍数，总额/2分单价
                    multiple = _money * 10 * 10 / 2;

                    // way = _area.getName().split("-")[0];
                    // digit = _area.getName().split("-")[1];
                    
                    wayIdAndBall = me.getWayIdAndBall(_area);

                    wayId = wayIdAndBall.wayId;
                    ball = wayIdAndBall.ball;

                    // 每个区域对应的玩法、数字、单价、注数、模式、倍数
                    result['balls'].push({
                        // 玩法ID
                        'wayId': wayId,
                        // 玩法英文名
                        'type': type,
                        // 数字
                        'ball': ball,
                        // 注数
                        'num': num,
                        // 单价：2分
                        'onePrice': 2,
                        // 模式：分模式
                        'moneyunit': 0.01,
                        // 倍数：1，默认为1
                        'multiple': multiple,
                        // 奖金组
                        'prize_group': prize_group
                    });
                    // 元转换成分
                    amount += _money * 10 * 10;

                }
            });

            //投注期数格式修改为键值对
            result['orders'] = {};
            //获得当前期号，将期号作为键
            result['orders'][me.getCurrNumber()] = 1;
            //总金额
            result['amount'] = amount;

            result['_token'] = me.getConfig('_token');

            var betInfoR = {
                    "balls": result['balls'],
                    'isFinish': true,
                    'issue': me.getCurrNumber()
                },
                betInfo = $.extend({}, betInfoR);
            me.setLastBetInfo(betInfo);
            return result;

        },
        submit: function() {
            var me = this,
                data = me.getSubmitData(),
                url = me.getConfig('submitUrl');
            // data['gameid'] = me.getConfig('gameId');
            // data['_token'] = me.getConfig('_token');
            $.ajax({
                url: url,
                dataType: 'JSON',
                method: 'POST',
                data: me.editSubmitData(data),
                beforeSend: function() {
                    me.fireEvent('submit_before', data);
                },
                success: function(data) {
                    if (Number(data['isSuccess']) == 1) {
                        me.fireEvent('success_after', data);
                    } else {
                        alert(data['Msg']);
                    }
                }
            });

        },

        //将树状数据整理成两级缓存数据
        getGameMethods: function() {
            var me = this,
                nodeCache = {},
                methodCache = {},
                data = me.getConfig("gameMethods"),
                node1,
                node2,
                node3;

            $.each(data, function() {
                node1 = this;
                node1['fullname_en'] = [node1['name_en']];
                node1['fullname_cn'] = [node1['name_cn']];
                nodeCache['' + node1['id']] = node1;
                if (node1['children']) {
                    $.each(node1['children'], function() {
                        node2 = this;
                        node2['fullname_en'] = node1['fullname_en'].concat(node2['name_en']);
                        node2['fullname_cn'] = node1['fullname_cn'].concat(node2['name_cn']);
                        nodeCache['' + node2['id']] = node2;
                        if (node2['children']) {
                            $.each(node2['children'], function() {
                                node3 = this;
                                node3['fullname_en'] = node2['fullname_en'].concat(node3['name_en']);
                                node3['fullname_cn'] = node2['fullname_cn'].concat(node3['name_cn']);
                                methodCache['' + node3['id']] = node3;
                            });
                        }
                    });
                }
            });
            return methodCache;
        },

        // 根据areaName获得area的wayId。
        getWayIdAndBall: function(area) {
            var me = this,
                areaName = area.getName(),
                wayId = 0,
                ball = 0;
                //龙0，虎1，和2
                //龙单3,虎单4，龙双5，虎双6
                //龙红7,虎红8，龙黑9，虎黑10
                switch (areaName) {
                    case 'long-dan':
                        methodName = 'longhudan';
                        ball = 3;
                        break;
                    case 'long-shuang':
                        methodName = 'longhushuang';
                        ball = 5;
                        break;
                    case 'long':
                        methodName = 'longhudaxiao';
                        ball = 0;
                        break;
                    case 'long-hong':
                        methodName = 'longhuhonghei';
                        ball = 7;
                        break;
                    case 'long-hei':
                        methodName = 'longhuhonghei';
                        ball = 9;
                        break;
                    case 'hu-dan':
                        methodName = 'longhudan';
                        ball = 4;
                        break;
                    case 'hu-shuang':
                        methodName = 'longhushuang';
                        ball = 6;
                        break;
                    case 'hu':
                        methodName = 'longhudaxiao';
                        ball = 1;
                        break;
                    case 'hu-hong':
                        methodName = 'longhuhonghei';
                        ball = 8;
                        break;
                    case 'hu-hei':
                        methodName = 'longhuhonghei';
                        ball = 10
                        break;
                    case 'he':
                        methodName = 'longhuhe';
                        ball = 2;
                        break;
                };

                $.each(me._methods,function(){
                    var method = this;
                        if(method.name_en==methodName){
                            wayId = method.id;
                        }
                });
            return {
                wayId: wayId,
                ball: ball
            }
        }
    };




    var Main = host.Class(pros, TableGame);
    Main.defConfig = defConfig;
    TableGame.Lhd = Main;

})(bomao, bomao.TableGame);
