<?php

class StopTrace extends BaseTask {

    protected function doCommand(){
        extract($this->data,EXTR_PREFIX_ALL,'stop');

//        $pid           = posix_getpid();
//        $date          = Carbon::now()->toDateTimeString();
//        $sBasicMsg     = "$date PID: $pid Stop ";
//        $sLogMsg = "$date PID: $pid Start Stop Traces: " . implode(',',$stop_traces);
//        Log::info($sLogMsg);
        $aSuccTraces   = $aFailedTraces = [];
        $aUsers        = [];
        $DB            = DB::connection();
        foreach ($stop_traces as $iTraceId){
            $oTrace = ManTrace::find($iTraceId);
            if ($oTrace->status != ManTrace::STATUS_RUNNING){
                continue;
            }
            $oAccount = Account::lock($oTrace->account_id,$iLocker);
            if (empty($oAccount)){
                Log::info($this->logBase . " $oTrace->id ERROR: Lock Account Failed");
                $aFailedTraces[] = $iTraceId;
                continue;
            }
            $oUser                      = isset($aUsers[ $oTrace->user_id ]) ? $aUsers[ $oTrace->user_id ] : ($aUsers[ $oTrace->user_id ] = User::find($oTrace->user_id));
            $oTrace->setAccount($oAccount);
            $oTrace->setUser($oUser);
            $DB->beginTransaction();
            if (($iReturn                    = $oTrace->terminate(2)) === true){
                $DB->commit();
                $aSuccTraces[] = $iTraceId;
            }
            else{
                $DB->rollback();
                Log::info($this->logBase . " $oTrace->id ERROR: $iReturn");
                $aFailedTraces[] = $iTraceId;
            }
            Account::unlock($oTrace->account_id,$iLocker,false);
        }
        $this->log = count($aSuccTraces) . ' Success, ' . count($aFailedTraces) . ' Failed,';
        if (empty($aFailedTraces)){
            $this->log .= ' Exiting';
//            $this->setFinished($job,$sLogMsg);
            return self::TASK_SUCCESS;
        }
        else{
            $this->log .= ' Failed Trace: ' . implode(',',$aFailedTraces);
//            $this->setRelease($job,$sLogMsg);
            return self::TASK_RESTORE;
//            return self::TASK_KEEP;
        }

    }

}
