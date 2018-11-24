(function (host, Event, undefined) {

    var pros;
    var defConfig = {
        name: 'miniHistory',
        //父类容器
        UIContainer: '.trend-panel',
        //自身游戏容器
        container: '',
        parentGame: null,
    };

    pros = {
        init: function (cfg) {
            var me = this;
            me.UIContainer = cfg.UIContainer;
            me.parentGame = cfg.parentGame;
            me.container = $('<div class="r"></div>').appendTo(me.parentGame.container.find(me.UIContainer));
            me.container.html(html_all);
            me.data = null,
                me._dxds = 0,
                me._lushu = 0,
                me._move = 0,
                me._move1 = 0,
                // me.arrNumber = [],
                me.newnum = 0;
            //历史开奖数据源
            me.sourceData = null;

            // me.buildUI(c,1,'1');
        },
        //生成二维坐标,参数是数据
        changeArr: function (aa) {

            var me = this;
            var x = 0,
                y = 0,
                ls = [],
                csz = 0,
                a = [
                    [0, 0]
                ],
                _first = aa[0],
                _length = aa.length;

            for (var i = 1; i < aa.length; i++) {

                x = a[a.length - 1][0];   //最后一个数组的X值
                y = a[a.length - 1][1];    //最后一个数组的Y值


                if (aa[i] === _first|| aa[i] === '/' || aa[i] === '?' || aa[i] === '') {
                    if (me.every([x + 1, y], a) && y === csz) {   //判断下一个X轴生成的数组是否有重复，没有就生成它；
                        x += 1;
                        ls = [x, y];
                        if (x > 4) {           //如果超过了4，就让它轴等于前面一个值得X轴；
                            x = a[a.length - 1][0];
                            y += 1;
                            ls = [x, y];
                        }

                    } else {                 //如果下一个X轴生成的有重复，则变化Y轴生成它；
                        y += 1;
                        ls = [x, y];
                        if (x === 0) {
                            csz = y;
                        }
                    }
                    a.push(ls);

                    ls = [];


                } else {
                    csz++;
                    ls = [0, csz];
                    if (!(me.every(ls, a))) {
                        csz++;
                        ls = [0, csz];

                    }
                    a.push(ls);
                    ls = [];
                    _first = aa[i];


                }


            }

            return {_x: x, _y: y, _csz: csz, _a: a, length: _length};
        },
        //用于检验数组是否存在，已经存在返回false，不存在返回True；
        every: function (a, b) {
            var me = this;
            var _a = a,
                _b = b,
                c = true;

            for (var j = 0; j < _b.length; j++) {
                if (me.contrastArr(_b[j], _a) === false) {

                    c = true;//如果对比完了后，发现没有重复，返回true；

                } else {

                    c = false;
                    break;


                }

            }
            return c;
        },
        //判断两个数组是否相等
        contrastArr: function (a, b) {
            var _c = a,
                _d = b,
                c = false;

            for (var k = 0; k < _c.length; k++) {
                if (_c[k] === _d[k]) {
                    c = true;
                } else {
                    c = false;
                    break;
                }
            }
            if (_c.length != _d.length) {
                c = false;
            }
            return c;
        },
        //和值转换成文字,_a参数0是大小，1是单双;_b参数是需要处理的数字，返回大小单双_c;
        changeText: function (_a, _b) {
            var _c = '';
            switch (_a) {
                case 0:
                    if (_b <= 13 && _b >= 0) {
                        _c = '小';
                    } else if (_b > 13) {
                        _c = '大';
                    } else if (_b === '') {
                        _c = '?';
                    } else if (_b === '/') {
                        _c = '/';
                    }
                    break;
                case 1:
                    if (_b % 2 === 0 && _b >= 0) {
                        _c = '双';
                    } else if (_b % 2 != 0) {
                        _c = '单';
                    } else if (_b === '') {
                        _c = '?';
                    } else if (_b === '/') {
                        _c = '/';
                    }
                    break;
            }
            return _c;
        },
        //字符串处理成数字并且相加得出 和值
        changeHezhi: function (_a) {
            var _b = [],
                _c = 0;
            if (_a === '') {
                _c = '';
            } else if (_a == '/') {
                _c = '/';
            } else {
                for (var i = 1; i < 4; i++) {
                    _b.push(parseInt(_a.substring(i, i - 1)));
                }

                for (var j = 0; j < _b.length; j++) {
                    _c = _c + _b[j];
                }
            }

            return _c;
        },
        //生成一维数组，_a代表模式0是大小，1是单双；_b是需要处理的jSON(后端传入);
        creatArr: function (_a, _b) {
            var _c = [''];
            for (var i = 0; i < _b.length; i++) {
                // if(i==_b.length+1){
                // 	_c.push('');
                // }

                if (_b[i].code === '') {
                    _c.push('?');
                } else if (_b[i].code === '/') {
                    _c.push('/');
                } else {
                    var _d = this.changeHezhi(_b[i].code);
                    _c.push(this.changeText(_a, _d));

                }


            }
            _c = _c.reverse();
            return _c;
        },
        //根据数据判断创建多少个li标签,返回数字
        addLi: function (_a, json) {
            var _e = this.creatArr(_a, json),
                _f = this.changeArr(_e)._a,
                _h = _f[0][1];
            for (var i = 1; i < _f.length; i++) {
                if (_h < _f[i][1]) {
                    _h = _f[i][1]
                }
            }
            return _h + 4;
        },
        //创建LI标签,_a参数父层dIV,_b是否路数，_z大小单双,_json是数据
        creatLi: function (_a, _b, _z, json) {
            var _c = '',
                _e = 0,
                _f = '',
                me = this;

            switch (_b) {

                case 0:
                    var _d = this.addLi(_z, json);
                    if (_d < 17) {
                        _d = 17;
                    }
                    for (var i = 0; i < _d; i++) {
                        _e += 1;
                        _c += '<li></li>'
                    }

                    _f = (_d ) * 25 + 'px';
                    me.container.find(_a).find('ul').prepend(_c);
                    me.container.find(_a).css('width', _f);
                    break;
                case 1:

                    var oArr = Math.ceil((this.creatArr(_z, json).length) / 5);
                    if (oArr < 17) {
                        oArr = 17;
                    }
                    for (var i = 0; i < oArr; i++) {
                        _e += 1;
                        _c += '<li></li>'
                    }
                    _f = (oArr) * 25 + 'px';
                    me.container.find(_a).find('ul').prepend(_c);
                    me.container.find(_a).css('width', _f);
                    break;
            }
        },
        getHistory: function (dxds, position, json, l) {

            var me = this;
            me.historyArr = json;
            me.container.find(position).find('ul').empty();
            me.container.find('.ul2-main').css('right','');
            this.creatLi(position, l, dxds, json);

            switch (l) {
                case 0:
                    var _g = this.creatArr(dxds, json),
                        _d = this.changeArr(_g)._a;

                    for (var i = 0; i < _d.length; i++) {
                        var _e = _d[i][0],
                            _f = _d[i][1];

                        if (_g[i] === '小' || _g[i] === '单') {
                            me.container.find(position).find('ul').eq(_e).find('li').eq(_f).addClass('green');
                        } else if (_g[i] === '大' || _g[i] === '双') {
                            me.container.find(position).find('ul').eq(_e).find('li').eq(_f).addClass('red');
                        } else if (_g[i] === '?' || _g[i] === '') {
                            me.container.find(position).find('ul').eq(_e).find('li').eq(_f).addClass('wh');
                        }
                        me.container.find(position).find('ul').eq(_e).find('li').eq(_f).text(_g[i]);
                        // .attr('data-num', me.arrNumber[i])


                    }
                    break;
                case 1:
                    var _csz = 0,
                        _a = this.creatArr(dxds, json),
                        _b = 0;

                    for (var i = 0; i < _a.length; i++) {

                        if (_a[i] === '小' || _a[i] === '单') {
                            me.container.find(position).find('ul').eq(_b).find('li').eq(_csz).addClass('green');
                        } else if (_a[i] === '大' || _a[i] === '双') {

                            me.container.find(position).find('ul').eq(_b).find('li').eq(_csz).addClass('red');
                        } else if (_a[i] === '?' || _a[i] === '') {
                            me.container.find(position).find('ul').eq(_b).find('li').eq(_csz).addClass('wh');
                        }
                        me.container.find(position).find('ul').eq(_b).find('li').eq(_csz).text(_a[i]);
                        _b += 1;
                        if (_b > 4) {
                            _csz += 1;
                            _b = 0;
                        }

                    }

                    break;
            }
            //scroll -floatright
            me.container.find('.ul-2').scrollLeft(parseInt(me.container.find('.ul2-main').css('width')) - 425);


        },
        getInitData: function (data) {
            var me = this;

            me.sourceData = data;
            me.getHistory(me._dxds, me.container.find('.ul2-main'), me.sourceData, me._lushu);
        },
        //更新开奖数据
        updataSourceData: function (newResult) {
            var me = this;

            switch (me.sourceData.length) {
                case 0:

                    me.sourceData.push(newResult);

                    break;
                case 1:

                    if (newResult.code == ''&&me.sourceData[0].number!=newResult.number) {
                        me.sourceData.unshift(newResult);
                    }else if (me.sourceData[0].number == newResult.number) {

                        me.sourceData[0].code = newResult.code;

                    }

                    break;
                default:

                    if (newResult.code == ''&&me.sourceData[0].number!=newResult.number&&me.sourceData[1].number!=newResult.number) {
                        me.sourceData.unshift(newResult);
                    } else {

                        $.each([0, 1], function (i) {
                            if (me.sourceData[i].number == newResult.number) {
                                me.sourceData[i].code = newResult.code;
                            }
                        });
                    }

                    break;
            }

            me.getHistory(me._dxds, me.container.find('.ul2-main'), me.sourceData, me._lushu);

        },

        //跳转官网
        linkOfficePage: function () {
            var me = this;
            me.officeUrl = '';
            switch (me.parentGame.id) {
                case 54:
                    me.officeUrl = 'http://www.cqcp.net';
                    break;
                case 55:
                    me.officeUrl = 'http://www.lottost.cn';
                    break;
                case 56:
                    me.officeUrl = 'http://www.xjflcp.com';
                    break;
                case 57:
                    me.officeUrl = 'http://www.tjflcpw.com';
                    break;
                case 58:
                    me.officeUrl = 'http://www.bwlc.net';
                    break;
                case 59:
                    me.officeUrl = 'http://www.swlc.gov.cn';
                    break;
                default:
                    break;
            }

            window.open(me.officeUrl);
        },
        //刷新走势图
        updataModel: function () {
            var me = this;

            var service = new bomao.Lucky28.DataService();

            service.getAllIssueByGameID(me.parentGame.id, function (data) {
                if (data) {
                    me.sourceData = data;
                    me.getHistory(me._dxds, me.container.find('.ul2-main'), me.sourceData, me._lushu);
                }
            });
        }
    };
    var html_all = '';
    html_all +=
        '<div class="r-a">' +
        '<ul class="ul-1">' +
        '<li class="dx">大小</li>' +
        '<li class="ds">单双</li>' +
        '</ul>' +
        '<div class="ul-2">' +
        '<div class="ul2-main">' +
        '<div class="ul2-l"></div><ul></ul><ul></ul><ul></ul><ul></ul><ul></ul><div class="ul2-r"></div>' +
        '</div>' +
        '</div>' +
        '<ul class="ul-3">' +
        '<li class="ico1"></li>' +
        '<li class="ico2"></li>' +
        '<li class="ico3"></li>' +
        '</ul>' +
        '</div>';


    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;

    host.Lucky28.list[defConfig.name] = Main;
})(bomao, bomao.Event);