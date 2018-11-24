@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('javascripts')
@parent
{{ script('jquery-ui') }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')

    @include('link.detailForm')


@stop

@section('end')
@parent

{{ script('bootstrap-switch') }}

<?php
// TODO 实际奖金组区间应根据当前代理用户所在奖金组来决定
$sliderCfg = [
    'min'            => (int)$aDefaultPrizeGroups['classic_prize'],
    'max'            => (int)$aDefaultMaxPrizeGroups['classic_prize'],
    'step'           => 1,
    'bonus'          => (int)$aDefaultPrizeGroups['classic_prize'],
    //当前操作用户(代理)的奖金组
    'proxyBonus'     => (int)$aDefaultPrizeGroups['classic_prize'],
    //当前彩系最低奖金玩法的奖金组
    'minMethodBonus' => 300,
    //当前彩系最高奖金玩法的奖金组
    'maxMethodBonus' => (int)$aDefaultMaxPrizeGroups['classic_prize'],
];
$sliderCfg = json_encode($sliderCfg);
// $aDefaultPrizeGroups = json_encode($aDefaultPrizeGroups);
// pr($aDefaultPrizeGroups);exit;
$sDefaultPrizeGroup  = $aDefaultPrizeGroups['classic_prize'];
// $sCurrentPrizeGroup = null;
$isEdit = (int)$isEdit;
// $detailUrl = '"' . route('user-user-prize-sets.prize-set-detail') . '"';
print("<script language=\"javascript\">var sliderCfg = $sliderCfg; var defaultPrizeGroup = $sDefaultPrizeGroup; var currentPrizeGroup = '0'; var isEdit = $isEdit; var currentUserPrize = $sCurrentUserPrize ? $sCurrentUserPrize : 0;</script>\n");
?>

{{ script('create-user-link') }}
@stop