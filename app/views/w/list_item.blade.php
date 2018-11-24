
<table class="table table-striped table-hover table-bordered text-center">
    <thead>
        <tr>
            @if ($bCheckboxenable)
            <th class="text-center"><input type="checkbox" id="allCheckbox" name="allCheckbox"></th>
            @endif
            @foreach( $aColumnForList as $sColumn )
            <th class="text-center">{{ (__($sLangPrev . $sColumn, null, 3)) }}
                @if (!in_array($sColumn, $aNoOrderByColumns))
                {{ order_by($sColumn) }}
                @endif
            </th>
            <?php
            if (isset($aTotalColumns)) {
                in_array($sColumn, $aTotalColumns) or $aTotalColumnMap[$sColumn] = null;
            }
            ?>
            @endforeach
            <th class=" text-center">{{ __('_basic.actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr>
            @if ($bCheckboxenable)
            <td ><input type="checkbox" name="selectFlag" value="{{$data->id}}">
                @endif
                <?php
                foreach ($aColumnForList as $sColumn) {
                    if (isset($aTotalColumns)) {
                        if (!is_null($aTotalColumnMap[$sColumn])) {
                            $fResult = isset($data->is_income) && $data->is_income == 0 ? -$data->$sColumn : $data->$sColumn;
                            $aTotals[$aTotalColumnMap[$sColumn]] += $fResult;
                        }
                    }
                    //    $sDisplayColumn = isset($aColumnDisplayMaps[ $sColumn ]) ? $aColumnDisplayMaps[ $sColumn ] : $sColumn;
                    $sClass = '';
                    $bDisplayRaw = false;
                    if (isset($aListColumnMaps[$sColumn])) {
                        $sDisplayValue = $data->{$aListColumnMaps[$sColumn]};
                    } else {
                        if ($sColumn == 'sequence') {
                            $sDisplayValue = Form::text('sequence[' . $data->id . ']', $data->sequence, ['class' => 'form-control', 'style' => 'width:70px;text-align:right']);
                            $bDisplayRaw = true;
                        } else {
                            if (isset($aColumnSettings[$sColumn]['type'])) {
                                $sDisplayValue = $sColumn . $aColumnSettings[$sColumn]['type'];
                                switch ($aColumnSettings[$sColumn]['type']) {
                                    case 'bool':
                                        $sDisplayValue = !is_null($data->$sColumn) ? ($data->$sColumn ? __('Yes') : __('No')) : null;
                                        break;
                                    case 'select':
                                        //                                        $sDisplayValue = (isset($data->$sColumn) && !is_null($data->$sColumn)) ? ${$aColumnSettings[$sColumn]['options']}[$data->$sColumn] : null;
                                        if (!is_null($data->$sColumn) && isset(${$aColumnSettings[$sColumn]['options']}[$data->$sColumn])) {
                                            $sDisplayValue = ${$aColumnSettings[$sColumn]['options']}[$data->$sColumn];
                                        } else {
                                            $sDisplayValue = null;
                                        }
                                        break;
                                    case 'numeric':
                                        $sClass = 'text-right';
                                        if (!isset($aNumberColumns[$sColumn])) {
                                            $aNumberColumns[$sColumn] = $iDefaultAccuracy;
                                        }
                                    default:
                                        $sDisplayValue = is_array($data->$sColumn) ? implode(',', $data->$sColumn) : $data->$sColumn;
                                }
                            } else {
                                $sDisplayValue = $data->$sColumn;
                            }
                            if (array_key_exists($sColumn, $aNumberColumns)) {
                                $sDisplayValue = number_format($sDisplayValue, $aNumberColumns[$sColumn]);
                            }
                        }
                    }
                    $sTdContent = $bDisplayRaw ? $sDisplayValue : e($sDisplayValue);
                    if (isset($aColumnSettings[$sColumn]['type']) && $aColumnSettings[$sColumn]['type'] == 'numeric') {
                        $sClass = 'text-right';
                    } else {
                        $sClass = 'text-center';
                    }

                    if (in_array($sColumn, $aWeightFields)) {
                        $sClass .= ' text-weight';
                    }
                    if (in_array($sColumn, $aClassGradeFields)) {
                        $sClass .= ' ' . ($data->$sColumn >= 0 ? 'text-red' : 'text-green');
                    }
                    $aClassForColumns[$sColumn] = $sClass;
                    echo "<td class='$sClass'";
                    if (in_array($sColumn, $aFloatDisplayFields)) {
                        echo " title='{$data->$sColumn}'";
                    }
                    echo ">$sDisplayValue</td>";
                }
                ?>
            <td>
                @include('w.item_link')
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        @if (isset($aTotalColumns))
        <tr>
            <td>{{ __('grand-total-per-page') }}</td>
            @for($i = 1; $i < count($aColumnForList); $i++)
            <td class="{{ is_numeric($aTotals[$i]) ? 'text-right' : '' }}">{{  is_null($aTotals[$i]) ? ' ' : number_format($aTotals[$i], (array_key_exists($aColumnForList[$i], $aNumberColumns) ? $aNumberColumns[$aColumnForList[$i]] : 2)) }}</td>
            @endfor
            <td></td>
        </tr>

        @endif
        @if (isset($aTotalColumnsAllPages))
        <tr>
            <td>{{ __('grand-total') }}</td>
            @for($i = 1; $i < count($aColumnForList); $i++)
            <td class="{{ is_numeric($aTotalsAllPages[$aColumnForList[$i]."_sum"]) ? 'text-right' : '' }}">{{  is_null($aTotalsAllPages[$aColumnForList[$i]."_sum"]) ? ' ' : number_format($aTotalsAllPages[$aColumnForList[$i]."_sum"], (array_key_exists($aColumnForList[$i], $aNumberColumns) ? $aNumberColumns[$aColumnForList[$i]] : 2)) }}</td>
            @endfor
            <td></td>
        </tr>
        @endif
    </tfoot>
</table>