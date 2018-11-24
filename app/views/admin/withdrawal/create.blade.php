@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Create') . $resourceName }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')
    @include('admin.withdrawal.detailForm')
@stop

@section('end')
    @parent
    {{ script('bootstrap-switch') }}

    <?php
        $bankAccounts = json_encode($aBankAccounts);
        $hiddenColumns = json_encode($aHiddenColumns);
        $readonlyInputs = json_encode($aReadonlyInputs);
        // pr($bankAccounts);
        // pr('---------');
        // pr($readonlyInputs);
        print("<script language=\"javascript\">var bankAccounts = $bankAccounts; var hiddenColumns = $hiddenColumns; var readonlyInputs = $readonlyInputs; </script>\n");

    ?>
    <script>
    jQuery(document).ready(function($) {
        var accountInfo = {};
        function renderAccountSelectorByBank(bank_id)
        {
            var accounts = bankAccounts[bank_id];
            var options = ['<option></option>'];
            for (var i = 0, l = accounts.length; i < l; i++) {
                var item = accounts[i];
                accountInfo[item['account']] = item;
                options.push('<option value="' + item['account'] + '">' + item['account'] + '</option>');
            }
            $('select[name=account]').html(options.join(''));
        }
        $('select').change(function(event) {
            var name = $(this).attr('name');
            var text = $(this).find('option:selected').text();
            // debugger;
            if (name == 'bank_id') {
                var bank_id = $(this).val();
                renderAccountSelectorByBank(bank_id);
            }
            if (name == 'account') {
                for(var i = 0, l = readonlyInputs.length; i < l; i++) {
                    var readonlyName = readonlyInputs[i];
                    $('input[name=' + readonlyName + ']').val(accountInfo[text][readonlyName]).attr('readonly', true);
                }
            }
            // debugger;
            var hiddenName = name.split('_')[0];
            if (name == 'user_id') hiddenName = 'username';
            if ($.inArray(hiddenName, hiddenColumns) > -1) {
                $('input[type=hidden][name=' + hiddenName + ']').val(text);
            }
        });
    });
    </script>

@stop
