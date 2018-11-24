
(function(host, Danshi, undefined){
    var defConfig = {
        name:'sanma.zuxuan.danshi',
        //玩法提示
        tips: '',
        //选号实例
        exampleTip: '',
        //标准格式样本
        exampleText: '03 04 06<br />04 05 06<br />01 04 05 <br />01 06 08 <br />01 03 06'       
    },
        gameCaseName = 'L115',
        Games = host.Games,
        //游戏类
        gameCase = host.Games[gameCaseName].getInstance();
    
    
    //定义方法
    var pros = {
        init:function(cfg){
            var me = this;
            //建立编辑器DOM
            //防止绑定事件失败加入定时器
            setTimeout(function(){
                me.initFrame();
            },25);
        },
        rebuildData:function(){
            var me = this;
            me.balls = [
                        [-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]
                        ];
        },
        //检测结果重复
        checkResult: function(data, array) {
            //检查重复
            for (var i = array.length - 1; i >= 0; i--) {
                if (array[i].sort().join('') == data.sort().join('')) {
                    return false;
                }
            };
            return true;
        },
        //检测单注号码是否通过
        checkSingleNum: function(lotteryNum) {
            var me = this,
                isPass = true;
            if(lotteryNum.length != 3){
                return false;
            }
            if(lotteryNum[0] == lotteryNum[1] || lotteryNum[0] == lotteryNum[2] || lotteryNum[1] == lotteryNum[2] ){
                return false;
            }
            $.each(lotteryNum, function() {
                if (!me.defConfig.checkNum.test(this)  || Number(this) < 1 || Number(this) > 11) {
                    isPass = false;
                    return false;
                }
            });
            return isPass;
        }
        
    };
    
    //继承Danshi
    var Main = host.Class(pros, Danshi);
    Main.defConfig = defConfig;
    //将实例挂在游戏管理器上
    gameCase.setLoadedHas(defConfig.name, new Main());
    
    
    
})(bomao, bomao.Games.L115.Danshi);