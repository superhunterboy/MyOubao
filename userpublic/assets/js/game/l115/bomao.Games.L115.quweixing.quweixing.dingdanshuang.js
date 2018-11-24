(function(host, Danshi, undefined) {
    var defConfig = {
            name: 'quweixing.quweixing.dingdanshuang',
            //父容器
            UIContainer: '#J-balls-main-panel'
        },
        gameCaseName = 'L115',
        Games = host.Games,
        //游戏类
        gameCase = host.Games[gameCaseName].getInstance();

    //定义方法
    var pros = {
        init: function(cfg) {
            var me = this;

        },
        //复位选球数据
        rebuildData: function() {
            var me = this;
            me.balls = [
                [-1, -1, -1, -1, -1, -1, -1]
            ];
        },
        buildUI: function() {
            var me = this;
            me.container.html(html_all.join(''));
        },
        //检测选球是否完整，是否能形成有效的投注
        //并设置 isBallsComplete 
        checkBallIsComplete: function() {
            var me = this,
                ball = me.getBallData()[0],
                i = 0,
                len = ball.length,
                num = 0;
            for (; i < len; i++) {
                if (ball[i] > 0) {
                    num++;
                }
            }
            if (num >= 1) {
                return me.isBallsComplete = true;
            }
            return me.isBallsComplete = false;
        },
        //获取组合结果
        getLottery: function() {
            var me = this,
                ball = me.getBallData()[0],
                i = 0,
                len = ball.length,
                arr = [];

            for (; i < len; i++) {
                if (ball[i] > 0) {
                    arr.push(i);
                }
            }
            //校验当前的面板
            //获取选中数字
            if (me.checkBallIsComplete()) {
                return me.combine(arr, 1);
            }

            return [];
        },
        makePostParameter: function(original) {
            var me = this,
                result = [],
                len,
                tempArr = [],
                i = 0;
            $.each(original, function(i){
                tempArr[i]  = [];
                $.each(original[i], function(j){
                    tempArr[i][j] = original[i][j];
                });
            });

            len = tempArr.length;
            for (i =0; i < len; i++) {
                result = result.concat(tempArr[i].join(' '));
            }
            return result.join('|');
        },
        formatViewBalls: function(original) {
            var me = this,
                result = [],
                len,
                tempArr = [],
                i = 0,
                names = ['', '5单0双', '4单1双', '3单2双', '2单3双', '1单4双', '0单5双'];
            $.each(original, function(i){
                tempArr[i]  = [];
                $.each(original[i], function(j){
                    tempArr[i][j] = names[original[i][j]];
                });
            });

            len = tempArr.length;
            for (i =0; i < len; i++) {
                result = result.concat(tempArr[i].join(' '));
            }
            return result.join('|');
        },
        //data 该玩法的单注信息
        editSubmitData:function(data){
            var ball_num = {'1':'5','2':'4','3':'3','4':'2','5':'1','6':'0'},
                numArr = data['ball'].split(''),
                result = [];
            $.each(numArr, function(){
                ball_num['' + this] ? result.push(ball_num['' + this]) : result.push(this);
            });
            data['ball'] = result.join('');
        }
    };


    //html模板
    var html_head = [];
    //头部
    html_head.push('<div class="number-select-title balls-type-title clearfix"><div class="number-select-link"><a href="#" class="pick-rule">选号规则</a><a href="#" class="win-info">中奖说明</a></div></div>');
    html_head.push('<div class="number-select-content">');
    html_head.push('<ul class="ball-section">');
    //每行
    var html_row = [];
    html_row.push('<li style="background-image:none;">');
    html_row.push('<div class="ball-title" style="display:none;"><strong><#=title#></strong></div>');
    html_row.push('<ul class="ball-content ball-content-long" style="padding-left:25px;">');
    $.each(['', '5单0双', '4单1双', '3单2双', '2单3双', '1单4双', '0单5双'], function(i) {
        if(i == 0){
            html_row.push('<li style="display:none;"><a class="ball-number" data-param="action=ball&value=' + i + '&row=<#=row#>" href="javascript:void(0);">' + this + '</a></li>');
        }else{
            html_row.push('<li><a class="ball-number ball-number-long" data-param="action=ball&value=' + i + '&row=<#=row#>" href="javascript:void(0);">' + this + '</a></li>');
        }
    });
    html_row.push('</ul>');
    html_row.push('</li>');

    var html_bottom = [];
    html_bottom.push('</ul>');
    html_bottom.push('</div>');
    //拼接所有
    var html_all = [],
        rowStr = html_row.join('');
    html_all.push(html_head.join(''));
    $.each([''], function(i) {
        html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i));
    });
    html_all.push(html_bottom.join(''));


    //继承Danshi
    var Main = host.Class(pros, Danshi);
    Main.defConfig = defConfig;
    //将实例挂在游戏管理器上
    gameCase.setLoadedHas(defConfig.name, new Main());



})(bomao, bomao.GameMethod);