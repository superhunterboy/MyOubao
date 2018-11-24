

<div class="panel-rank-detail" id="J-panel-rank-detail">
    <div id="J-panel-rank-detail-inner"></div>
    <a href="#" class="close"></a>
</div>






@section('end')
@parent

<script>
//查看战绩弹窗操作
(function($, host){
    var Mask = host.Mask.getInstance();
    var UserRank = {
        timer:null,
        dom:$('#J-panel-rank-detail'),
        inner:$('#J-panel-rank-detail-inner'),
        loading:$('#J-panel-loading'),
        init:function(){
            var me = this;
            me.dom.find('.close').click(function(e){
                e.preventDefault();
                me.hide();
            });
        },
        show:function(url){
            var me = this;
            $.ajax({
                url:url,
                beforeSend:function(){
                    /*
                    clearTimeout(me.timer);
                    me.timer = setTimeout(function(){
                        me.showLoading();
                    }, 1000);
                    */ 
                    me.showLoading();
                },
                success:function(data){
                    me.inner.html(data);
                },
                complete:function(){
                    //clearTimeout(me.timer);
                    me.hideLoading();
                },
                error:function(xhr, type){
                    alert('请求失败,请刷新页面重试:' + type);
                }
            });

            //me.inner.html('');
            if(Mask.dom.is(':hidden')){
                Mask.show();
            }
            me.dom.show();
        },
        hide:function(){
            var me = this;
            me.dom.hide();
            Mask.hide();
        },
        showLoading:function(){
            var me = this;
            me.loading.show();
        },
        hideLoading:function(){
            var me = this;
            me.loading.hide();
        },
        linkToAction:function(e){
            var el = $(this),
                url = $.trim(el.attr('href'));
            e.preventDefault();
            if(url.indexOf('javascript') == 0){
                return;
            }
            UserRank.show(url);
        }
    };
    UserRank.init();




    UserRank.dom.on('click', '.ct-rank-detail', function(e){
        e.preventDefault();
    });

    UserRank.dom.on('click', '.ct-update-data', UserRank.linkToAction);
    UserRank.dom.on('click', '.page-right a', UserRank.linkToAction);

    $(document).on('click', '.ct-rank-detail', UserRank.linkToAction);



})(jQuery, bomao);
</script>


@stop
