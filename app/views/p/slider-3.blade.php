<?php
	$presenter = new Illuminate\Pagination\BootstrapPresenter($paginator);
?>

<ul class="pagination pagination-sm pull-right" style="margin-top: 0px;">
<li class=""><span>{{ __('Page ') . $paginator->getCurrentPage() }}, {{ __('Per Page ') . $paginator->getPerPage() }}, {{ __('Total ') . $paginator->getTotal() }}<span></li>
    {{ $presenter->render() }}
</ul>
