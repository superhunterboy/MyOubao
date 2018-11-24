<ul class="tab-title">
    @if($isOpenBankDeposit)<li><span class="@if(Route::current()->getName() == 'user-recharges.netbank')top-bg @endif"></span><a href="{{ route('user-recharges.netbank') }}"><span><div class="way-img way-img-quick"></div>银行卡充值</span></a></li>@endif
    @foreach ($oPlatforms as $oNavPlatform)
    <?php $liClass = $oNavPlatform->id == $iPlatformId ? 'current' : ''; ?>
    <?php $spanClass = $oNavPlatform->id == $iPlatformId ? 'top-bg' : ''; ?>
    <li class="{{$liClass}}">
        <span class="{{$spanClass}}"></span>
        <a href="{{ route('user-recharges.quick', $oNavPlatform->id) }}"><span><div class="way-img way-img-{{PaymentPlatform::$aIconTypes[$oNavPlatform->icon]}}"></div>{{ $oNavPlatform->display_name }}</span></a>
    </li>
    @endforeach
</ul>