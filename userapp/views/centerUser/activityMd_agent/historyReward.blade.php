<div class="history-list">
    <table class="history-table">
        <thead class="history-table-head">
            <th class="user-name">用户名</th>
            <th class="time">抽奖时间</th>
            <th class="prize-group">奖品组</th>
            <th class="select-number">抽中号码</th>
            <th class="prize-info">中奖情况</th>
            <th>备注</th>
        </thead>
        <tbody>
            @if(!empty($datas))
            @foreach($datas as $k=>$data)
            <tr>
                <td class="user-name">{{$data->user_name}}</td>
                <td class="time">{{$data->created_at}}</td>
                <td class="prize-group">{{$data->hand_name}}</td>
                <td class="select-number">{{$data->level}}</td>
                <td class="prize-info">
                    @if($data->level == 0)
                    未中奖
                    @else
                        @if($data->status == 0)
                        未派奖
                        @else
                        已派奖
                        @endif
                    @endif
                </td>
                <td>
                    @if($data->level != 0 && $data->status == 0)
                    <a class="history-server" href="javascript:javascript:hj5107.openChat();">联系客服</a>
                    @endif
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="6">{{ pagination($datas->appends(Input::except('page')), 'w.pages') }}</td>
            </tr>
            @else
            <tr>
                <td colspan="6">暂无抽奖数据</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $(function(){

        var url = '';

        //历史请求
        function getHistoryFunction(url_str, callback){
            $.ajax({
                type: "get",
                url: url_str,
                dataType: "html",
                success:function(data){
                    if($.isFunction(callback)){
                        callback.call(this,data);
                    }
                },
                error:function(data){
                    // console.log(data);
                }
            });
        }

        function showPrizeHistory(data){
            if(data['isSuccess']==undefined){
                //写入记录
                $(".history-box").html(data);
            }else{
                $(".history-box").hide();
                //非正常状态的错误提示
                var data = {
                    title : '提示',
                    content : data['Msg'],
                    isShowMask : true,
                    closeIsShow : true,
                    closeButtonText: '关闭',
                    closeFun : function() {
                        this.hide();
                    }
                };
                popWindow.hideClose();
                popWindow.show(data);
            }
        }

        $('.page').on('mousedown', 'a', function(event) {
            event.preventDefault();
            /* Act on the event */
            $(this).attr('href', 'javascript:void(0);');

            getHistoryFunction(url , showPrizeHistory);
        }).on('mouseenter', 'a', function(event) {
            event.preventDefault();
            /* Act on the event */
            url = $(this).attr('href');
        }).on('mouseleave', 'a', function(event) {
            event.preventDefault();
            /* Act on the event */
            $(this).attr('href', url);
        });;

    });
</script>


