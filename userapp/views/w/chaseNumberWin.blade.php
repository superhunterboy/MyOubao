<div class="j-ui-miniwindow pop" style="z-index:1001;position:fixed;top:0;width:900px;display:none;">
        <div class="pop-hd">
            <h3 class="pop-title"><i class="pop-title-ico"></i>设置追号</h3>
        </div>
        <div class="pop-bd">
            <div class="chase-tab">
                <div class="chase-tab-title clearfix">
                    <ul class="clearfix">
                        <li class="current">普通追号</li>
                        <li class="">高级追号</li>
                    </ul>
                    <div style="display:none;" class="chase-stop" id="J-trace-iswinstop-panel">
                        <label for="J-trace-iswinstop" class="label"><input type="checkbox" class="checkbox" id="J-trace-iswinstop">累计盈利</label>&gt;<input type="text" value="1000" class="input" disabled="disabled" id="J-trace-iswinstop-money">元时停止追号&nbsp;
                        <i id="J-trace-iswinstop-hover" class="icon-question">玩法提示</i>
                        <div class="chase-stop-tip" id="chase-stop-tip-2">
                                当追号计划中，累计盈利金额（已获奖金扣除已投本金）大于设定值时，即停止继续追号。<br>
                                如果您不考虑追号的盈利情况，<a id="J-chase-stop-switch-2" href="#">点这里</a>。
                            </div>
                    </div>
                    <div class="chase-stop" id="J-trace-iswintimesstop-panel">
                        <label class="label"><input type="checkbox" class="checkbox" id="J-trace-iswintimesstop">中奖后停止追号</label><input type="text" value="1" disabled="disabled" class="input" id="J-trace-iswintimesstop-times" style="display:none;">&nbsp;
                        <i id="J-trace-iswintimesstop-hover" class="icon-question">玩法提示</i><div class="chase-stop-tip" id="chase-stop-tip-1">
                                当追号计划中，一个方案内的任意注单中奖时，即停止继续追号。<br>
                                如果您希望考虑追号的实际盈利，<a id="J-chase-stop-switch-1" href="#">点这里</a>。
                            </div>
                    </div>
                </div>
                <div class="chase-tab-content chase-tab-content-current">
                    <ul class="chase-select-normal clearfix">
                        <li id="J-function-select-tab">
                            连续追：
                            <div class="function-select-title">
                                <a href="javascript:void(0);">5期</a>
                                <a href="javascript:void(0);" class="current">10期</a>
                                <a href="javascript:void(0);">15期</a>
                                <a href="javascript:void(0);">20期</a>
                            </div>
                        </li>
                        <li>
                            <input type="text" value="10" class="input" id="J-trace-normal-times">
                            <span class="choose-model-text">期</span>
                        </li>
                        <li>
                            <div class="choose-model">
                                <div class="choose-list" style="display:none;">
                                    <a data-value="1" href="javascript:void(0);">1</a>
                                    <a data-value="5" href="javascript:void(0);">5</a>
                                    <a data-value="10" href="javascript:void(0);">10</a>
                                    <a data-value="15" href="javascript:void(0);">15</a>
                                </div>
                                <div class="info"><input type="text" data-realvalue="1" class="choose-input" value="1"></div><i></i>
                            </div>
                            <span class="choose-model-text">倍</span>
                        </li>
                    </ul>

                    <div class="chase-table-container">
                    <table class="table chase-table" id="J-trace-table">
                            <tbody id="J-trace-table-body" data-type="trace_normal">
                                <tr>
                                    <th class="text-center">序号</th>
                                    <th><input type="checkbox" checked="checked" class="checkbox" data-action="checkedAll">追号期次</th>
                                    <th>倍数</th>
                                    <th>金额</th>
                                    <th>预计开奖时间</th>
                                </tr>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>
                                        <input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow">
                                        <span class="trace-row-number">201301021215<span class="icon-period-current"></span></span>
                                    </td>
                                    <td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td>
                                    <td>&yen; <span class="trace-row-money">10.00</span> 元</td>
                                    <td><span class="trace-row-time">2013/12/10 08:14:23</span></td></tr><tr><td class="text-center">2</td><td><input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow"> <span class="trace-row-number">201301021216</span></td><td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td><td>&yen; <span class="trace-row-money">10.00</span> 元</td><td><span class="trace-row-time">2013/12/11 08:14:23</span></td></tr><tr><td class="text-center">3</td><td><input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow"> <span class="trace-row-number">201301021217</span></td><td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td><td>&yen; <span class="trace-row-money">10.00</span> 元</td><td><span class="trace-row-time">2013/12/12 08:14:23</span></td></tr><tr><td class="text-center">4</td><td><input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow"> <span class="trace-row-number">201301021218</span></td><td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td><td>&yen; <span class="trace-row-money">10.00</span> 元</td><td><span class="trace-row-time">2013/12/13 08:14:23</span></td></tr><tr><td class="text-center">5</td><td><input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow"> <span class="trace-row-number">201301021219</span></td><td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td><td>&yen; <span class="trace-row-money">10.00</span> 元</td><td><span class="trace-row-time">2013/12/14 08:14:23</span></td></tr><tr><td class="text-center">6</td><td><input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow"> <span class="trace-row-number">201301021220</span></td><td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td><td>&yen; <span class="trace-row-money">10.00</span> 元</td><td><span class="trace-row-time">2013/12/11 08:14:23</span></td></tr><tr><td class="text-center">7</td><td><input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow"> <span class="trace-row-number">201301021221</span></td><td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td><td>&yen; <span class="trace-row-money">10.00</span> 元</td><td><span class="trace-row-time">2013/12/12 08:14:23</span></td></tr><tr><td class="text-center">8</td><td><input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow"> <span class="trace-row-number">201301021222</span></td><td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td><td>&yen; <span class="trace-row-money">10.00</span> 元</td><td><span class="trace-row-time">2013/12/13 08:14:23</span></td></tr><tr><td class="text-center">9</td><td><input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow"> <span class="trace-row-number">201301021223</span></td><td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td><td>&yen; <span class="trace-row-money">10.00</span> 元</td><td><span class="trace-row-time">2013/12/14 08:14:23</span></td></tr><tr><td class="text-center">10</td><td><input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow"> <span class="trace-row-number">201301021224</span></td><td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td><td>&yen; <span class="trace-row-money">10.00</span> 元</td><td><span class="trace-row-time">2013/12/11 08:14:23</span></td></tr></tbody>
                    </table>
                    </div>

                </div>





                <div class="chase-tab-content">
                    <div class="chase-select-high">
                        <ul class="base-parameter">
                            <li>
                                起始期号：

                                <div class="choose-model chase-trace-startNumber-select w-4"><div style="display:none;" class="choose-list"><a href="javascript:void(0);" data-value="201301021215">201301021215(当前期)</a><a href="javascript:void(0);" data-value="201301021216">201301021216</a><a href="javascript:void(0);" data-value="201301021217">201301021217</a><a href="javascript:void(0);" data-value="201301021218">201301021218</a><a href="javascript:void(0);" data-value="201301021219">201301021219</a><a href="javascript:void(0);" data-value="201301021220">201301021220</a><a href="javascript:void(0);" data-value="201301021221">201301021221</a><a href="javascript:void(0);" data-value="201301021222">201301021222</a><a href="javascript:void(0);" data-value="201301021223">201301021223</a><a href="javascript:void(0);" data-value="201301021224">201301021224</a><a href="javascript:void(0);" data-value="201301021225">201301021225</a><a href="javascript:void(0);" data-value="201301021226">201301021226</a><a href="javascript:void(0);" data-value="201301021227">201301021227</a><a href="javascript:void(0);" data-value="201301021228">201301021228</a></div><span class="info"><input type="text" value="201301021215(当前期)" disabled="disabled" class="choose-input" data-realvalue="201301021215"></span><i></i></div><select style="display:none;" id="J-traceStartNumber"><option selected="selected" value="201301021215">201301021215(当前期)</option><option value="201301021216">201301021216</option><option value="201301021217">201301021217</option><option value="201301021218">201301021218</option><option value="201301021219">201301021219</option><option value="201301021220">201301021220</option><option value="201301021221">201301021221</option><option value="201301021222">201301021222</option><option value="201301021223">201301021223</option><option value="201301021224">201301021224</option><option value="201301021225">201301021225</option><option value="201301021226">201301021226</option><option value="201301021227">201301021227</option><option value="201301021228">201301021228</option></select>

                            </li>
                            <li>
                                追号期数：
                                <input type="text" value="10" class="input" id="J-trace-advanced-times">&nbsp;&nbsp;期（最多可以追<span id="J-trace-number-max">14</span>期）
                            </li>
                            <li>
                                起始倍数：
                                <input type="text" value="1" class="input" id="J-trace-advance-multiple">&nbsp;&nbsp;倍
                            </li>
                        </ul>

                        <div class="high-parameter" id="J-trace-advanced-type-panel">
                            <ul class="tab-title">
                                <li class="current">翻倍追号</li>
                                <li>盈利金额追号</li>
                                <li>盈利率追号</li>
                            </ul>
                            <ul class="tab-content">
                                <li class="panel-current">
                                    <p data-type="a">
                                        <input type="radio" checked="checked" name="trace-advanced-type1" class="trace-advanced-type-switch">
                                        每隔&nbsp;<input type="text" value="2" class="input" id="J-trace-advanced-fanbei-a-jiange">&nbsp;期
                                        倍数x<input type="text" value="2" class="input trace-input-multiple" id="J-trace-advanced-fanbei-a-multiple">
                                    </p>
                                    <p data-type="b">
                                        <input type="radio" name="trace-advanced-type1" class="trace-advanced-type-switch">
                                        前&nbsp;<input type="text" disabled="disabled" value="5" class="input" id="J-trace-advanced-fanbei-b-num">&nbsp;期
                                        倍数=起始倍数，之后倍数=<input type="text" disabled="disabled" value="3" class="input trace-input-multiple" id="J-trace-advanced-fanbei-b-multiple">
                                    </p>
                                </li>
                                <li>
                                    <p data-type="a">
                                        <input type="radio" checked="checked" name="trace-advanced-type2" class="trace-advanced-type-switch">
                                        预期盈利金额≥&nbsp;<input type="text" value="100" class="input" id="J-trace-advanced-yingli-a-money">&nbsp;元
                                    </p>
                                    <p data-type="b">
                                        <input type="radio" name="trace-advanced-type2" class="trace-advanced-type-switch">
                                        前&nbsp;<input type="text" disabled="disabled" value="2" class="input" id="J-trace-advanced-yingli-b-num">&nbsp;期
                                        预期盈利金额≥&nbsp;<input type="text" disabled="disabled" value="100" class="input" id="J-trace-advanced-yingli-b-money1">&nbsp;元，之后预期盈利金额≥&nbsp;<input type="text" disabled="disabled" value="50" class="input" id="J-trace-advanced-yingli-b-money2">&nbsp;元
                                    </p>
                                </li>
                                <li>
                                    <p data-type="a">
                                        <input type="radio" checked="checked" name="trace-advanced-type3" class="trace-advanced-type-switch">
                                        预期盈利率≥&nbsp;<input type="text" value="10" class="input" id="J-trace-advanced-yinglilv-a">&nbsp;%
                                    </p>
                                    <p data-type="b">
                                        <input type="radio" name="trace-advanced-type3" class="trace-advanced-type-switch">
                                        前&nbsp;<input type="text" disabled="disabled" value="5" class="input" id="J-trace-advanced-yinglilv-b-num">&nbsp;期
                                        预期盈利率≥&nbsp;<input type="text" disabled="disabled" value="30" class="input" id="J-trace-advanced-yingli-b-yinglilv1">&nbsp;%，之后预期盈利率≥&nbsp;<input type="text" value="10" class="input" disabled="disabled" id="J-trace-advanced-yingli-b-yinglilv2">&nbsp;%
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="chase-btn"><input type="button" value="" id="J-trace-builddetail"></div>


                    <div class="chase-table-container">
                        <table class="table chase-table" id="J-trace-table-advanced">
                            <tr>
                                <th>序号</th>
                                <th><label class="label"><input type="checkbox" class="checkbox">追号期次</label></th>
                                <th>倍数</th>
                                <th>金额</th>
                                <th>预计开奖时间</th>
                            </tr>
                            <tr>
                                <td class="text-center">1</td>
                                <td>
                                    <input type="checkbox" checked="checked" class="checkbox" data-action="checkedRow">
                                    <span class="trace-row-number">201301021215<span class="icon-period-current"></span></span>
                                </td>
                                <td><input type="text" style="width:30px;text-align:center;" value="1" class="input"> 倍</td>
                                <td>&yen;<span class="trace-row-money">10.00</span> 元</td>
                                <td><span class="trace-row-time">2013/12/10 08:14:23</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="pop-btn">
            <a href="javascript:void(0);" class="btn btn-important" style="">确 认</a>
            <a href="javascript:void(0);" class="btn btn-normal" style="">取 消</a>
            <a href="javascript:void(0);" class="btn" style="display: none;">关 闭</a>
        </div>
    </div>