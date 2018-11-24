@if (isset($aSelectorData['aHiddenColumns']) && count($aSelectorData['aHiddenColumns']))
    @foreach($aSelectorData['aHiddenColumns'] as $key => $aHiddenColumn)
    <input type="hidden" name="{{ $aHiddenColumn['name'] }}" id="J-input-{{ $key + 1 }}" value="{{ isset($aHiddenColumn['value']) ? $aHiddenColumn['value'] : '' }}" />
    @endforeach
@endif
@if (isset($aSelectorData['aSelectColumn']) && count($aSelectorData['aSelectColumn']))
    @foreach($aSelectorData['aSelectColumn'] as $key => $aSelectColumn)
    <select id="J-select-{{ $key + 1 }}" style="display:none;" name="{{ $aSelectColumn['name'] }}">
        <option value="">{{ $aSelectColumn['emptyDesc'] }}</option>
    </select>
    @endforeach
@endif
@section('end')
@parent

<?php
$sSelectedFirst  = $aSelectorData['sSelectedFirst'] ? $aSelectorData['sSelectedFirst'] : '';
$sSelectedSecond = $aSelectorData['sSelectedSecond'] ? $aSelectorData['sSelectedSecond'] : '';
?>

<script>

(function($){
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
        var selectedFirst  = "<?php echo $sSelectedFirst ?>",
            selectedSecond = "<?php echo $sSelectedSecond ?>",
            firstSelectedId = selectedFirst,
            firstInput   = $('#J-input-1'),
            secondInput  = $('#J-input-2'),
            firstSelect  = null,
            secondSelect = null;
        var renderSelector = function (data, selectedOption, inputObj)
        {
            var options = [];
            if ($.isArray(data)) {
                for (var i = 0, l = data.length; i < l; i++) {
                    var item     = data[i],
                        selected = '';
                    if (selectedOption && item['id'] == selectedOption) {
                        selected = 'selected';
                        inputObj.val(item['name']);
                    }
                    options.push('<option value="' + item['id'] + '" ' + selected + '>' + item['name'] + '</option>');
                }
            } else {
                for (var n in data) {
                    var item = data[n],
                        selected = '';
                    if (selectedOption && item['id'] == selectedOption) {
                        selected = 'selected';
                        inputObj.val(item['name']);
                    }
                    options.push('<option value="' + item['id'] + '" ' + selected + '>' + item['name'] + '</option>');
                }
            }
            return options.join('');

        }
        var renderFirstSelector = function ()
        {
            var optionsHtml = renderSelector(selectorData, selectedFirst, firstInput);
            $('#J-select-1').append(optionsHtml);
            firstSelect  = new bomao.Select({realDom:'#J-select-1',cls:'w-3', valueKey: 'id', textKey: 'name'});
        }
        var renderSecondSelector = function (first_id)
        {
            var secondSelectorData = selectorData[first_id]['children'],
                optionsHtml        = renderSelector(secondSelectorData, selectedSecond, secondInput);

            $('#J-select-2').html(optionsHtml);
            secondSelect = new bomao.Select({realDom:'#J-select-2',cls:'w-3', valueKey: 'id', textKey: 'name'});
        }
        renderFirstSelector();

        if (selectedFirst && selectedSecond) {
            renderSecondSelector(selectedFirst);
        } else {
            secondSelect = new bomao.Select({realDom:'#J-select-2',cls:'w-3', valueKey: 'id', textKey: 'name'});
        }
        firstSelect.addEvent('change', function(e, value, text){
            var id = $.trim(value);
            if (firstSelectedId == id) return false;
            firstSelectedId = id;
            if(!id){
                secondSelect.reBuildSelect([{id:'', name:'请选择城市', checked:true}]);
                return;
            }
            if(selectorData[id]['children']){
                secondSelect.reBuildSelect($.merge([{id:'', name:'请选择城市', checked:true}], selectorData[id]['children']));
                firstInput.val(text);
            }
        });
        secondSelect.addEvent('change', function(e, value, text) {
            var id = $.trim(value);
            if (id)
                secondInput.val(text);
        });
    })(jQuery);

</script>
@stop