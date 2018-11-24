<table width="100%" class="table" id="J-table">
    <thead>
        <tr>
            @foreach( $aColumnForList as $sColumn )
                <th>{{ ucwords(__($sLangPrev . $sColumn)) }} {{ order_by($sColumn) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($datas as $data)
        <tr>
            <?php
            foreach ($aColumnForList as $sColumn){
                if (isset($aColumnSettings[$sColumn]['type'])){
                    $sDisplayValue = $sColumn . $aColumnSettings[$sColumn]['type'];
                    switch($aColumnSettings[$sColumn]['type']){
                        case 'bool':
                            $sDisplayValue = $data->$sColumn ? __('Yes') : __('No');
                            break;
                        case 'select':
                            $sDisplayValue = (isset($data->$sColumn) && !is_null($data->$sColumn)) ? ${$aColumnSettings[$sColumn]['options']}[$data->$sColumn] : null;
                            break;
                        default:
                            $sDisplayValue = $data->$sColumn;
                    }
                }
                else{
                    $sDisplayValue = $data->$sColumn;
                }
                if (array_key_exists($sColumn, $aNumberColumns)){
                    $sDisplayValue = number_format($sDisplayValue, $aNumberColumns[$sColumn]);
                }
                echo "<td>$sDisplayValue</td>";
            }
            ?>

        </tr>
        @endforeach
    </tbody>
    <tfoot>
        @if (isset($bHasSumRow) && $bHasSumRow)
        @foreach( $aColumnForList as $sColumn )
            @if (in_array($sColumn, $aNeedSumColumns))
            <td>{{ $aSum[$sColumn] }}</td>
            @else
            <td>-</td>
            @endif
        @endforeach
        @endif
    </tfoot>
</table>