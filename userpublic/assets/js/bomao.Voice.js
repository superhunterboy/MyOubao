(function(host,name){
    var aaa= {
        voiceOff:'0',
        voice_class:'',
        _a:'',
        zhongjiang: function () {
            if($.support.msie && $.support.version=='8.0'){
                $('body').append('<embed id="betsvoice1" src="/assets/images/win-prize-activity/winner.mp3"/>');
            }else{
                $('body').append('<audio id="betsvoice1" autoplay="autoplay"><source src="/assets/images/win-prize-activity/winner.mp3" type="audio/mpeg"/></audio>');
            }
            setTimeout(function () {
                $('#betsvoice1').remove();
            },4000)
        },
        kaijiang:function () {
            if($.support.msie && $.support.version=='8.0'){
                $('body').append('<embed id="betsvoice1" src="/assets/images/betsvoice/betsvoice.mp3"/>');
            }else{
                $('body').append('<audio id="betsvoice1" autoplay="autoplay"><source src="/assets/images/betsvoice/betsvoice.mp3" type="audio/mpeg"/></audio>');
            }
            setTimeout(function () {
                $('#betsvoice1').remove();
            },4000)
        },
        voicestart:function () {
            var me=this;
            if(bomao.voice.voiceOff==='0'){
                me._a='close';
            }else{
                me._a='open';
            }
            $('#betsvoice span').attr('class',me._a);

            $('#betsvoice').on('click',function () {
                if(bomao.voice.voiceOff==='0'){
                    me.voice_class='open';
                    bomao.voice.voiceOff='1';
                }else {
                    me.voice_class='close';
                    bomao.voice.voiceOff='0';
                }
                $('#betsvoice span').attr('class',me.voice_class);
            });
        }
    };
    host[name]=aaa;
})(bomao,'voice');