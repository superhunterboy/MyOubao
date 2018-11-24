<?php

use Illuminate\Console\Command;

class UpdateUserPrizeCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'admin-tool:update-user-prize-sets';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'update user prize';


	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        if ($this->confirm('Do you wish to update user prize? [yes|no]'))
        {
            $users  = User::getAllUserArrayByUserType(User::TYPE_USER, null); // 只更新玩家的奖金组数据
            $iPlayerMinPrizeGroup = SysConfig::readValue('player_min_grize_group');
            $iPlayerMaxPrizeGroup = SysConfig::readValue('player_max_grize_group');
            // $users = User::all();
            $countSuccess = $countFail = 0;
            foreach ($users as $key => $user)
            {
                if (($user->prize_group && $user->prize_group >= $iPlayerMinPrizeGroup && $user->prize_group <= $iPlayerMaxPrizeGroup)) continue; // 剔除体验账户
                DB::connection()->beginTransaction();
                try
                {
                    $maxClassicPrize      = $user->getMaxGroupPrize()->prize_group;
                    $defaultMinPrizeGroup = $iPlayerMinPrizeGroup;
                    if ($user->parent_id != Config::get('vagent.user_id')) {
                        $maxClassicPrize      = min($iPlayerMaxPrizeGroup, max($maxClassicPrize, $defaultMinPrizeGroup));
                    }
                    $user->prize_group    = $maxClassicPrize;

                    $bool   = true;
                    if ($user->parent_id != Config::get('vagent.user_id')) {
                        foreach ($user->user_prize_groups()->get() as $oUserPrizeSet)
                        {
                            $prizeGroup = $oUserPrizeSet->lottery()
                                                        ->remember(1)
                                                        ->first()
                                                        ->getGroupByClassicPrize($maxClassicPrize);

                            $oUserPrizeSet->group_id        = $prizeGroup->id;
                            $oUserPrizeSet->prize_group     = $prizeGroup->classic_prize;
                            $oUserPrizeSet->classic_prize   = $prizeGroup->classic_prize;

                            if (!$oUserPrizeSet->save())
                            {
                                $bool   = false;
                                break;
                            }
                        }
                    }

                    if ($bool && $user->save())
                    {
                        DB::connection()->commit();
                        $countSuccess++;
                        // echo "User: {$user->username}  [{$user->id}] [{$key}] updated already!\n";
                    }
                    else
                    {
                        $countFail++;
                        // throw new Exception("user {$user->username}  [{$user->id}] [{$key}] update false!");
                    }
                }
                catch (Exception $e)
                {
                    DB::connection()->rollBack();
                    $countFail++;
                    // $this->error("User: {$user->username}  [{$user->id}] [{$key}] update false! Msg:". $e->getMessage());
                }
            }
            echo "Total $countSuccess Success, $countFail Failed";
        }

    }
}