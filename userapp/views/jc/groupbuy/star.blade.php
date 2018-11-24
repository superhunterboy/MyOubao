
    @if (isset($oUserGrowth))
        <span class="rank-link">
        <?php $iMaxLevel = 3; ?>
        <?php $iGoldLevel = $iMaxLevel; ?>
        <?php $iGoldGrowth = $oUserGrowth->display_gold_growth; ?>
        @foreach(array_reverse(\JcModel\JcUserGrowth::$aGoldGrowthParams) as $aVal)
        <?php $iGoldStarCount = intval($iGoldGrowth / $aVal['growth']); ?>
        <?php
            if ($iGoldStarCount > 0){
                $iGoldGrowth = $oUserGrowth->display_gold_growth %  $aVal['growth'];
            }
        ?>
        @if ($iGoldStarCount > 5)
        <span class="level level-{{ $iGoldLevel }}"><span class="num num-{{ $iGoldStarCount }}"></span></span>
        @else
        @for($i=0;$i<$iGoldStarCount;$i++)
        <span class="level level-{{ $iGoldLevel }}"></span>
        @endfor
        @endif
        <?php $iGoldLevel--; ?>
        @endforeach
        <?php $iSilverLevel = $iMaxLevel; ?>
        <?php $iSilverGrowth = $oUserGrowth->display_silver_growth; ?>
        @foreach(array_reverse(\JcModel\JcUserGrowth::$aGoldGrowthParams) as $aVal)
        <?php $iSilverStarCount = intval($oUserGrowth->display_silver_growth / $aVal['growth']); ?>
        <?php
            if ($iSilverStarCount > 0){
                $iSilverGrowth = $oUserGrowth->display_silver_growth %  $aVal['growth'];
            }
        ?>
        @if ($iSilverStarCount > 5)
        <span class="level level-{{ $iSilverLevel }} level-g-{{ $iSilverLevel }}"><span class="num num-{{ $iSilverStarCount }}"></span></span>
        @else
        @for($i=0;$i<$iSilverStarCount;$i++)
        <span class="level level-{{ $iSilverLevel }} level-g-{{ $iSilverLevel }}"></span>
        @endfor
        @endif
        <?php $iSilverLevel--; ?>
        @endforeach
        </span>


    @endif

