


@if (isset($aSelectorData['aSelectColumn']) && count($aSelectorData['aSelectColumn']))
    @foreach($aSelectorData['aSelectColumn'] as $key => $aSelectColumn)
        @if($key !=1)
        {{ $aSelectColumn['desc'] }}
        @endif
        <select id="J-select-{{ $key + 1 }}" style="display:none;" name="{{ $aSelectColumn['name'] }}">
            <option value="">{{ $aSelectColumn['emptyDesc'] }}</option>
        </select>

    @endforeach
@endif
<!--
<div class="choose-model w-3">
    <div style="visibility: visible; display: none; height: 310px; overflow: hidden;" class="choose-list">
        <div class="choose-list-cont">
            <a data-value="" href="#">所有游戏</a>
            <a data-value="25" href="#">骰宝娱乐1桌</a>

        </div>
    </div>
    <span style="height: 155.25px; display: none; top: 0px;" class="choose-scroll" onselectstart="return false;"></span>
    <span class="info">
        <input data-realvalue="34" class="choose-input choose-input-disabled" disabled="disabled" value="龙虎斗娱乐1桌" type="text">
    </span>
    <i></i>
</div>
<select id="J-select-0" style="display:none;" name="series_type">
    <option value="">所有游戏</option>
    <option value="25" series_id="16">骰宝娱乐1桌</option>
</select>
-->
@section('end')
    @parent

    <?php
    $sSelectedFirst  = $aSelectorData['sSelectedFirst']  ? $aSelectorData['sSelectedFirst']  : '';
    $sSelectedSecond = $aSelectorData['sSelectedSecond'] ? $aSelectorData['sSelectedSecond'] : '';
    $sSelectedThird  = $aSelectorData['sSelectedThird']  ? $aSelectorData['sSelectedThird']  : '';

    ?>

    <script>

        (function($){
// die(realpath(app_path() . '/../widgets/data'));
// include(realpath(app_path() . '/../widgets/data') . '/province_city.json');


                    @include('widgets.data.casinoSelector');
                    @include('widgets.data.' . $aSelectorData['sDataFile']);

            /**
             * json数据结构
             * {
         *   'id': {
         *       id: 'id',
         *       name: 'name',
         *       children: [
         *           {id: 'id', name:'name'},
         *           ...
         *       ]
         *   },
         *   ...
         * }
             */

            var onlyOpenLottery = {{ intval(! Session::get('is_tester')) }};
            var selectedFirst  = "<?php echo $sSelectedFirst ?>";
            var selectedSecond = "<?php echo $sSelectedSecond ?>";
            var selectedThird  = "<?php echo $sSelectedThird ?>";
            var wayGroupData = null,
                    firstSelectedId  = selectedFirst,
                    secondSelectedId = selectedSecond,
                    firstSelect  = null,
                    secondSelect = null,
                    thirdSelect  = null,
                    firstIdPre   = 'series_id_',
                    secondIdPre  = 'parent_id_';

            /**
             * [generateJsonToArray 转换json对象为数组]
             * @param  {[Json]} data  [json对象]
             * @return {[Array]}      [对象数组]
             */
            var generateJsonToArray = function(data) {
                var generatedData = [];
                for (var n in data) {
                    var item = data[n];
                    generatedData.push(item);
                }
                return generatedData;
            }
            /**
             * [generateAllWayIds 生成所有玩法id以','隔开的字符串]
             * @param  {[Array]} data  [数据]
             * @return {[String]}      [所有玩法id以','隔开的字符串]
             */
            var generateAllWayIds = function (data) {
                var allWayIds = $.map(data, function(item, index) {
                    return item.id;
                });
                return allWayIds.join(',');
            }

            var renderSelector = function (data, selectedOption, extraParam)
            {
                var options = [];
                if ($.isArray(data)) {
                    for (var i = 0, l = data.length; i < l; i++) {
                        var item     = data[i],
                                selected = (selectedOption && item['id'] == selectedOption) ? 'selected' : '';
                        if (item['open'] && onlyOpenLottery && item['open'] != onlyOpenLottery) continue;
                        options.push('<option value="' + item['id'] + '"' + (extraParam ? ' series_id=' + item[extraParam] : '') + ' ' + selected + '>' + item['name'] + '</option>');
                    }
                } else {
                    for (var n in data) {
                        var item = data[n],
                                selected = (selectedOption && item['id'] == selectedOption) ? 'selected' : '';
                        if (item['open'] && onlyOpenLottery && item['open'] != onlyOpenLottery) continue;
                        options.push('<option value="' + item['id'] + '"' + (extraParam ? ' series_id=' + item[extraParam] : '') + ' ' + selected + '>' + item['name'] + '</option>');
                    }
                }
                return options.join('');

            }
            /**
             * [renderFirstSelector 渲染第一个下拉框]
             * @return {[type]} [description]
             */
            var renderFirstSelector = function ()
            {

                var optionsHtml = renderSelector(lotteriesWithSeriesId, selectedFirst, 'series_id');

                $('#J-select-1').append(optionsHtml);
                firstSelect  = new bomao.Select({realDom:'#J-select-1',cls:'w-3'});

            }
            /**
             * [renderFirstSelector 渲染第二个下拉框]
             * @return {[type]} [description]
             */
            var renderSecondSelector = function (first_id)
            {
               // first_id = first_id[0];


                wayGroupData    = selectorData[firstIdPre + first_id]['children'];

                var transferedData = generateJsonToArray(wayGroupData);

                var compiledData = $.merge([{id: '', name:'所有玩法群'}], transferedData);
                var optionsHtml = renderSelector(compiledData, selectedSecond);
                $('#J-select-2').html(optionsHtml);
                secondSelect = new bomao.Select({realDom:'#J-select-2',cls:'w-3 select-method-2', valueKey: 'id', textKey: 'name'});
            }
            /**
             * [renderFirstSelector 渲染第三个下拉框]
             * @return {[type]} [description]
             */
            var renderThirdSelector = function (second_id)
            {
                var data = wayGroupData[secondIdPre + second_id]['children'],
                        allWayIds = generateAllWayIds(data),
                        thirdSelectorData = $.merge([{id: allWayIds, name:'所有玩法'}], data),
                        optionsHtml       = renderSelector(thirdSelectorData, selectedThird);

                $('#J-select-3').html(optionsHtml);
                thirdSelect = new bomao.Select({realDom:'#J-select-3',cls:'w-3', valueKey: 'id', textKey: 'name'});
            }
            // 初始化彩种下拉框
            renderFirstSelector();
            // 初始化时，如果有彩种初始值，则填充玩法群下拉框，否则初始化bomao.Select对象
            if (selectedFirst) {

                var series_id = $('#J-select-1').find('option:selected').attr('series_id');
                renderSecondSelector(series_id);
            } else {
               secondSelect = new bomao.Select({realDom:'#J-select-2',cls:'w-3 select-method-2', valueKey: 'id', textKey: 'name'});
                //secondSelect='';
            }
            // 初始化时，如果有玩法群初始值，则填充玩法下拉框，否则初始化bomao.Select对象
            if (selectedSecond) {
                renderThirdSelector(selectedSecond);
            } else {
                thirdSelect = new bomao.Select({realDom:'#J-select-3',cls:'w-3', valueKey: 'id', textKey: 'name'});
            }

            firstSelect.addEvent('change', function(e, value, text){
                var seriesId = $(firstSelect.getRealDom()).find('option:selected').attr('series_id'),
                        id = $.trim(seriesId);

                if (firstSelectedId === id) return false;
                firstSelectedId = id;
                if(!id){
                    secondSelect.reBuildSelect([{id: '', name:'所有玩法群', checked:true}]);
                    return;
                }
                wayGroupData = selectorData[firstIdPre + id]['children'];

                if(wayGroupData){
                    var newData = generateJsonToArray(wayGroupData);
                    //alert(newData.length)
                    newData = $.merge([{id: '', name:'所有玩法群', checked:true}], newData);
                    $.each(wayGroupData,function(){ html='<option value="'+this.id+'" selected>'+this.name+'</option>';c=this.children})
                    $('#J-select-2').html(html);
                    var allWayIds = generateAllWayIds(c);
                    var newData = $.merge([{id: allWayIds, name:'所有玩法', checked:true}], c);
                    thirdSelect.reBuildSelect(newData);
                   // html = '<option value="89">龙虎斗</option>';
                    //t = document.getElementById('J-select-2').firstElementChild.selected=true;
                    //alert(t);
                   // secondSelect.reBuildSelect(newData);
                }
            });
            secondSelect.addEvent('change', function(e, value, text) {

                var id = $.trim(value);
                if (secondSelectedId === id) return false;
                secondSelectedId = id;
                if(!id){
                    thirdSelect.reBuildSelect([{id: '', name:'所有玩法', checked:true}]);
                    return;
                }

                var data = wayGroupData[secondIdPre + id]['children'];
                if(data){
                    var allWayIds = generateAllWayIds(data);
                    var newData = $.merge([{id: allWayIds, name:'所有玩法', checked:true}], data);
                    thirdSelect.reBuildSelect(newData);
                }
            });


        })(jQuery);

    </script>
@stop
