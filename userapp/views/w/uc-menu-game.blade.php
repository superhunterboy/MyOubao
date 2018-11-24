
<style>
    .project-type-color {color: red}
</style>
<ul class="tab-title clearfix">
    <li @if( $projectType == 'lottery')class="current"@endif>
        <span @if( $projectType == 'lottery')class="top-bg"@endif></span>
        <a href="{{ route('projects.index') }}?jc_type=lottery" @if($projectType == 'lottery')class="project-type-color"@endif><span>彩票</span></a>
    </li>
{{--     <li @if($projectType == 'sport')class="current"@endif>
        <span @if($projectType == 'sport')class="top-bg"@endif></span>
        <a href="{{ route('projects.index') }}?jc_type=sport" @if($projectType == 'sport')class="project-type-color"@endif><span>竞彩</span></a>
    </li>--}}
    <li @if($projectType == 'casino')class="current"@endif>
        <span @if($projectType == 'casino')class="top-bg"@endif></span>
        <a href="{{ route('projects.index') }}?jc_type=casino" @if($projectType == 'casino')class="project-type-color"@endif><span>电子娱乐</span></a>
    </li>
</ul>










