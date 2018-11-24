<?php

use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ScheduleDispatcherCommand extends ScheduledCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'custom-schedule:dispatcher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Custom scheduled dispatcher.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * When a command should run
     *
     * @param Scheduler $scheduler
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
    public function schedule(Schedulable $scheduler)
    {
        return $scheduler->everyMinutes(1);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {

        // $this->line('Welcome to the select generator.');
        // @file_put_contents('/home/snowan/Documents/tmp/'.Carbon::now()->toDateTimeString() . '.txt', Carbon::now()->toDateTimeString());
        // $message = "This is a queue test job. We won't really need to pass data for this demo.";
        // $date = Carbon::now()->toDateTimeString()->addMinutes(2)->toTimeString();
        // Queue::later($date, 'QueueDemo', array('message' => Carbon::now()->toDateTimeString()));
        // Queue::later($date, 'QueueDemo2', array('message' => Carbon::now()->toDateTimeString()));
        // $now = date('H:i:s');
        // $aQueueClass = [
        //     'QueueMonitorTest',
        //     'QueuePersonTest' ,
        //     'QueueTesterTest' ,
        //     'QueueAuthorTest' ,
        //     'QueueUserTest'
        // ];
        // foreach($aQueueClass as $key => $sQueueClass) {
        //     $date = date('H:i:s');
        //     Queue::push($sQueueClass, ['date' => $date]);
        //     $dates[] = $date;
        // }

        // $dates[] = $now;
        // $str = implode(',  ', $dates);

        // @file_put_contents('/home/snowan/Documents/laravelQueueTest/startQueueTimes_' . date('H:i:s') . '.txt', $str);

        // Queue::push('QueueMonitorTest', ['message' => 'QueueMonitorTest']);
        // Queue::push( 'QueuePersonTest', ['message' => 'QueuePersonTest']);
        // Queue::push( 'QueueTesterTest', ['message' => 'QueueTesterTest']);
        // Queue::push( 'QueueAuthorTest', ['message' => 'QueueAuthorTest']);
        // Queue::push(   'QueueUserTest', ['message' => 'QueueUserTest']);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    // protected function getArguments()
    // {
    //  return array(
    //      array('example', InputArgument::REQUIRED, 'An example argument.'),
    //  );
    // }

    /**
     * Get the console command options.
     *
     * @return array
     */
    // protected function getOptions()
    // {
    //  return array(
    //      array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
    //  );
    // }

}
