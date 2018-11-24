<?php

/*
  |--------------------------------------------------------------------------
  | Register The Artisan Commands
  |--------------------------------------------------------------------------
  |
  | Each available Artisan command must be registered with the console so
  | that it is available to be called. We'll register every command so
  | the console gets access to each of the command object instances.
  |
 */
Artisan::add(new ScheduleDispatcherCommand);
Artisan::add(new GenerateSelectorCommand);
Artisan::add(new UpdateRegisterLinkPrizeGroupCommand);
//Artisan::add(new CalculatePrizeCommand);
Artisan::add(new TestQueuePushCommand);
Artisan::add(new BeanstalkdStatusCommand);
Artisan::add(new ClearBeanstalkdQueueCommand);
Artisan::add(new GetCodeFromEC2);
Artisan::add(new GetCodeFromRng);
Artisan::add(new ManualEncode);
Artisan::add(new SendBonus);
Artisan::add(new CalculateFloat);
Artisan::add(new UpdatePrizeSet);

Artisan::add(new CreateActivityReport);
Artisan::add(new CreateActivityReportData);
Artisan::add(new ActivityCashBackTotal);

Artisan::add(new ReleaseAllDeadAccountLock);


Artisan::add(new GenerateAccountSnapshot);

Artisan::add(new GenerateProfitSnapshot);

Artisan::add(new GenerateIssueSnapshot);

Artisan::add(new GenerateLotterySnapshot);

Artisan::add(new ActivityLoadExcelCommand);
Artisan::add(new GenerateCommissionReport);

//更新用户奖金组
Artisan::add(new UpdateUserPrizeCommand);

Artisan::add(new RestartSendCommissionCommand());

Artisan::add(new GenerateNextIssue);
Artisan::add(new RestartGetTraceIssueCode);
Artisan::add(new RestartSendPrizeCommand);

Artisan::add(new CronGenerateNextIssue);
Artisan::add(new CronGetGetTraceIssueCode);

Artisan::add(new GetCodeFromMmc);
Artisan::add(new ManualEncodeFromMmc);


//生成游戏记录页面下拉菜单所需的json文件
Artisan::add(new GenerateSelectorNewCommand);
Artisan::add(new UpdateUserProfitsCommand);
Artisan::add(new UpdateProfitsCommand);
Artisan::add(new UpdateProjectBetNum);
Artisan::add(new updateCommissionRate);
Artisan::add(new TraceMakeup);

// Stat
Artisan::add( new UpdateProfitCommand);
Artisan::add( new UpdateUserProfitCommand);
Artisan::add( new UpdateTeamProfitCommand);
Artisan::add( new UpdateMonthProfitCommand);

/*===================JC=================*/
//Artisan::add(new GetSportData);
//Artisan::add(new SportMatchResultCommand);
//Artisan::add(new GetTeamIcons);
//Artisan::add(new GetWeatherIcons);
//Artisan::add(new \JcCommand\CheckGroupBuyStatus);
//Artisan::add(new \JcCommand\UpdateUserGrowthCommand);
//Artisan::add(new GenerateSportBonusUser);
/*===================JC=================*/

Artisan::add(new CalculateEachAgentTurnoverNumber);
Artisan::add(new BjAutoCalculateCommand());
Artisan::add(new CheckTongHuiKaWithdrawalStatusCommand);
Artisan::add(new CheckYouFuWithdrawalStatusCommand);
Artisan::add(new ActivityPlayerDailyTurnoverRebate);
Artisan::add(new ActivityDailySignRebate);

// 开奖
Artisan::add(new GenerateNumber);
// 生成奖期
Artisan::add( new GenerateIssue);
Artisan::add(new AddPrizeGrouplForSeries);
Artisan::add(new AddPrizeDetailForSeries);
// 更新每日彩种报表数据
Artisan::add( new UpdateLotteryProfitCommand);
// 更新用户金额
Artisan::add( new SetWithdrawableCommond);
// 更新用户奖金组
Artisan::add(new AddUserPrizeSetForNewLottery);
// 总代日工资
Artisan::add(new CalculateDailySalary);
// 总代月分红
Artisan::add(new TopAgentMonthlyBonusCommand);
// 数据清理
Artisan::add(new DataClearCommand());