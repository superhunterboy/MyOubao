(function(host, Danshi, undefined) {
    var defConfig = {
            name: 'dingweidan.dingweidan.dingweidan',
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
                [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
                [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
                [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1]
            ];
        },
        buildUI: function() {
            var me = this;
            me.container.html(html_all.join(''));
        },
        //设置选球数据
        //x y value   x y 为选球数据二维数组的坐标 value 为-1 或1
        setBallData: function(x, y, value) {
            var me = this,
                data = me.getBallData();
            if(y == 0){
                return;
            }
            me.fireEvent('beforeSetBallData', x, y, value);
            if (x >= 0 && x < data.length && y >= 0) {
                data[x][y] = value;
            }
            me.fireEvent('afterSetBallData', x, y, value);
        },
        //检测选球是否完整，是否能形成有效的投注
        //并设置 isBallsComplete 
        checkBallIsComplete: function() {
            var me = this,
                ball = me.getBallData(),
                i = 0,
                len = ball.length,
                j = 0,
                len2 = 0;
            for (i = 0; i < len; i++) {
                len2 = ball[i].length;
                for(j =0;j < len2;j++){
                    if(ball[i][j] > 0){
                        return me.isBallsComplete = true;
                    }
                }
            }
            return me.isBallsComplete = false;
        },
        //获取组合结果
        getLottery: function() {
            var me = this,
                ball = me.getBallData(),
                i = 0,
                len = ball.length,
                j = 0,
                tempNum,
                len2 = 0,
                num = 0,
                result = [];
            for (i = 0; i < len; i++) {
                len2 = ball[i].length;
                //result[i] = [];
                for(j = 0;j < len2;j++){
                    if(ball[i][j] > 0){
                        result.push(j);
                    }
                }
            }
            //console.log(me.checkBallIsComplete());
            //校验当前的面板
            //获取选中数字
            if (me.checkBallIsComplete()) {
                return result;
            }

            return [];
        },
        makePostParameter: function(original) {
            var me = this,
                result = [],
                len,
                tempArr = [],
                i = 0;
            $.each(original, function(i) {
                tempArr[i] = [];
                $.each(original[i], function(j) {
                    tempArr[i][j] = original[i][j] < 10 ? '0' + original[i][j] : '' + original[i][j];
                });
            });

            len = tempArr.length;
            for (i = 0; i < len; i++) {
                result = result.concat(tempArr[i].join(' '));
            }
            return result.join('|');
        },
        formatViewBalls: function(original) {
            return this.makePostParameter(original);
        },
    };


    //html模板
    var html_head = [];
    //头部
    html_head.push('<div class="number-select-title balls-type-title clearfix"><div class="number-select-link"><a href="#" class="pick-rule">选号规则</a><a href="#" class="win-info">中奖说明</a></div></div>');
    html_head.push('<div class="number-select-content">');
    html_head.push('<ul class="ball-section">');
    //每行
    var html_row = [];
    html_row.push('<li>');
    html_row.push('<div class="ball-title"><strong><#=title#>位</strong></div>');
    html_row.push('<ul class="ball-content">');
    $.each([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], function(i) {
        if (i == 0) {
            html_row.push('<li style="display:none;"><a class="ball-number" data-param="action=ball&value=' + this + '&row=<#=row#>" href="javascript:void(0);">' + this + '</a></li>');
        } else {
            html_row.push('<li><a class="ball-number" data-param="action=ball&value=' + this + '&row=<#=row#>" href="javascript:void(0);">' + (i < 10 ? '0' + this : this) + '</a></li>');
        }
    });
    html_row.push('</ul>');
    html_row.push('<div class="ball-control">');
    html_row.push('<a href="javascript:void(0);" class="circle"></a>');
    html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=all" class="all">全</a>');
    html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=big" class="big">大</a>');
    html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=small" class="small">小</a>');
    html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=odd" class="odd">奇</a>');
    html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=even" class="even">偶</a>');
    html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=none" class="none">清</a>');
    html_row.push('</div>');
    html_row.push('</li>');

    var html_bottom = [];
    html_bottom.push('</ul>');
    html_bottom.push('</div>');
    //拼接所有
    var html_all = [],
        rowStr = html_row.join('');
    html_all.push(html_head.join(''));
    $.each(['一', '二', '三'], function(i) {
        html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i));
    });
    html_all.push(html_bottom.join(''));


    //继承Danshi
    var Main = host.Class(pros, Danshi);
    Main.defConfig = defConfig;
    //将实例挂在游戏管理器上
    gameCase.setLoadedHas(defConfig.name, new Main());



})(bomao, bomao.GameMethod);