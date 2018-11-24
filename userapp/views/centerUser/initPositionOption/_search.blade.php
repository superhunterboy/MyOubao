<div class="area-search">
    @if($reportName=='transaction')<form action="{{ route('user-transactions.index') }}" class="form-inline" method="get">@endif
        @if($reportName=='deposit')<form action="{{ route('user-transactions.mydeposit',Session::get('user_id')) }}" class="form-inline" method="get">@endif
            @if($reportName=='withdraw')<form action="{{ route('user-transactions.mywithdraw',Session::get('user_id')) }}" class="form-inline" method="get">@endif
            @if($reportName=='transfer')<form action="{{ route('user-transactions.mytransfer',Session::get('user_id')) }}" class="form-inline" method="get">@endif
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <p class="row" style="position:relative">
                    时间：<input id="J-date-start" class="input w-3" type="text" name="created_at_from" value="{{ Input::get('created_at_from') }}" /> 至 <input id="J-date-end" class="input w-3" type="text" name="created_at_to" value="{{ Input::get('created_at_to') }}" />
                    &nbsp;&nbsp;
                    @if($reportName=='transaction')
                        <select id="J-select-issue" style="display:none;" name="number_type">
                            <option value="serial_number" {{ Input::get('number_type') == 'serial_number' ? 'selected' : '' }}>账变编号</option>
                            <option value="project_no" {{ Input::get('number_type') == 'project_no' ? 'selected' : '' }}>注单编号</option>
                            <option value="trace_id" {{ Input::get('number_type') == 'trace_id' ? 'selected' : '' }}>追号编号</option>
                            <option value="issue" {{ Input::get('number_type') == 'issue' ? 'selected' : '' }}>奖期编号</option>
                        </select>
                        <input class="input w-3" type="text" name="number_value" value="{{ Input::get('number_value') }}" />
                    @endif
                    &nbsp;&nbsp;
                    @if (Session::get('is_agent') && ($reportName=='transaction' || $reportName=='transfer'))
                        用户：<input class="input w-3" type="text" name="username" value="{{ isset($sJumpUsername) ? $sJumpUsername : Input::get('username')  }}" />
                        &nbsp;&nbsp;
                    @endif
                    <input type="submit" value="搜 索" class="btn" id="J-submit">

                    @if($reportName=='transaction')
                    <a id="J-button-showdetail" href="javascript:;" style="position:absolute;right:0;top:25px;">高级搜索</a>
                    @endif
                </p>




                
                <div style="display:none;" id="J-panel-search-ad">
                    <p class="row">
                        @include('widgets.lottery-group-ways')
                        &nbsp;&nbsp;
                        类型：
                        <select id="J-select-bill-type" style="display:none;" name="type_id">
                            <option value="">所有类型</option>
                            @foreach ($aTransactionTypes as $oTransactionType)
                                @if($reportName=='transaction' || in_array($oTransactionType->id, $depositTransactionType))
                                    <option value="{{ $oTransactionType->id }}" {{ Input::get('type_id') == $oTransactionType->id ? 'selected' : '' }}>{{ $oTransactionType->friendly_description }}</option>
                                @endif
                            @endforeach
                        </select>

                        @if($reportName != 'transaction')
                            &nbsp;&nbsp;
                            <input type="submit" value="搜 索" class="btn" id="J-submit">
                        @endif

                    </p>

                     {{--
                    <p class="row">
                        游戏模式：
                            <select id="J-select-game-mode" style="display:none;" name="coefficient">
                            <option value="">所有</option>
                            @foreach ($aCoefficients as $key => $desc)
                                <option value="{{ $key }}" {{ Input::get('coefficient') == $key ? 'selected' : '' }}>{{ $desc }}</option>
                            @endforeach
                            </select>
                    </p>
                    --}}
                </div>
                


                


            </form>
</div>