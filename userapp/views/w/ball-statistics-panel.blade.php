{{--

<div id="J-balls-statistics-panel" class="panel-trace-cont clearfix">
	<ul class="bet-statistics clearfix">
		<li>
			资金模式
			&nbsp;
			<span id="J-bet-statics-tab-moneyunit">
				<span class="bet-statics-tab-moneyunit">
					<span class="item current" data-value="1">元</span><span data-value="0.1" class="item">角</span><span data-value="0.01" class="item">分</span>
				</span>
				<span style="display:none;">
					<span class="bet-statics-moneyunit-cont"></span>
					<span class="bet-statics-moneyunit-cont"></span>
					<span class="bet-statics-moneyunit-cont"></span>
				</span>
			</span>
			&nbsp;&nbsp;
		</li>
		<li class="bet-statics-multiple-cell">
			<span class="front-text">投注倍数</span>
			<span id="J-bet-statics-multiple-reduce" class="bet-statics-multiple-control bet-statics-multiple-reduce"></span>
			<select id="J-balls-statistics-multiple" style="display:none;">
				<option value="1">1</option>
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="50">50</option>
				<option value="100">100</option>
				<option value="200">200</option>
			</select>
			<span id="J-bet-statics-multiple-add" class="bet-statics-multiple-control bet-statics-multiple-add"></span>
			<span id="J-balls-statistics-multiple-text" class="game-statistics-multiple-text">1</span>
		</li>
	</ul>
	<ul class="bet-statics-money-nums">
		<li class="choose-bet">已选<em id="J-balls-statistics-lotteryNum">0</em>注, </li>
		<li class="total-money">共<em id="J-balls-statistics-amount" class="bignum">0.00</em>元&nbsp;&nbsp;</li>
		<li class="total-money panel-text-user-balance">
			可用余额<em id="J-balls-statistics-balance">0.00</em>元&nbsp;&nbsp;
			<a id="J-button-bet-allin" href="#" class="btn btn-bet-allin btn-bet-allin-disable">ALL IN</a>
		</li>
	</ul>
	<div class="bet-rebate-cont">
		<div class="row row-a">投注奖金组-返点</div>
		<div class="row">
			<select id="J-select-rebate" style="display:none;">
			</select>
		</div>
		<div style="display:none;" class="row">对应返点 <span class="num" id="J-rebate-percentage">0%</span></div>
	</div>
	<ul class="bet-button-line">
		<li class="choose-btn choose-btn-submit-fast">
			<input type="button" value="直接投注" id="J-add-fastorder" class="disable" />
		</li>
		<li class="choose-btn">
			<input type="button" value="添加至购彩篮" id="J-add-order" class="disable" />
		</li>

	</ul>

</div>
--}}








			<div id="J-balls-statistics-panel">
				
				<ul class="bet-statistics clearfix">
					<li>
						<span id="J-bet-statics-tab-moneyunit">
							<span class="bet-statics-tab-moneyunit">
								<span class="item current" data-value="1">元</span><span data-value="0.1" class="item">角</span><span data-value="0.01" class="item">分</span>
							</span>
							<span style="display:none;">
								<span class="bet-statics-moneyunit-cont"></span>
								<span class="bet-statics-moneyunit-cont"></span>
								<span class="bet-statics-moneyunit-cont"></span>
							</span>
						</span>
					</li>
					<li class="bet-statics-multiple-cell">
						<span id="J-bet-statics-multiple-reduce" class="bet-statics-multiple-control bet-statics-multiple-reduce"></span>
						<select id="J-balls-statistics-multiple" style="display:none;">
							<option value="1">1</option>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
						<span id="J-bet-statics-multiple-add" class="bet-statics-multiple-control bet-statics-multiple-add"></span>
						<span id="J-balls-statistics-multiple-text" class="game-statistics-multiple-text">1</span>
					</li>
					<li>
						倍
					</li>
					<li class="choose-bet">已选<em id="J-balls-statistics-lotteryNum">0</em>注, </li>
					<li class="total-money">共<em id="J-balls-statistics-amount" class="bignum">0.00</em>元&nbsp;&nbsp;</li>
					<li class="bet-rebate-cont">
						<div class="row">
							返点 
							<select id="J-select-rebate" style="display:none;">
							</select>
						</div>
						<div style="display:none;" class="row">对应返点 <span class="num" id="J-rebate-percentage">0%</span></div>
					</li>
				</ul>





				<ul class="bet-button-line">
					<li class="total-money panel-text-user-balance choose-btn">
						可用余额<em id="J-balls-statistics-balance">0.00</em>元
						<a id="J-button-bet-allin" href="#" class="btn btn-bet-allin btn-bet-allin-disable">ALL IN</a>
					</li>
					<li class="choose-btn choose-btn-submit-fast">
						<input type="button" value=" " id="J-add-fastorder" class="disable" />
					</li>
					<li class="choose-btn">
						<input type="button" value=" " id="J-add-order" class="disable" />
					</li>
				</ul>


			</div>