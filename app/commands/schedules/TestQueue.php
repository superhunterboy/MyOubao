<?php

class TestQueue {
	public function fire($job, $data)
	{
		@file_put_contents('/home/johnny/workspace/firecat/userapp/storage/logs/test.txt', json_encode($data), FILE_APPEND);
		//$job->release(0);
		$job->delete();
	}
}