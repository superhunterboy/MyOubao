#!/bin/bash
# 本程序调用 e1m_check_send_task.php ,实现一分钟内多次运行
# 调用方法： proc php_path script_name times_per_minute [arg1...]
# 例如：check_send_task.bash /usr/bin/php e1m_check_send_task.php 3 1 
# 即在循环调用3次e1m_check_send_task.php，游戏ID为1，PHP路径是/usr/bin/php
# 注：最多支持5个参数

# set repeat times and program performance time
cmd="$2 $3"
pg_time=5

# set php path
php=$1


# set times
if [ -z "$4" ]; then
  times=5
else
  times=$4
  if [ $times -lt 1 ]; then 
    times=1
  fi
fi

# echo $times

if [ $times -gt 1 ]; then
  let "sleeptime=(60-$pg_time*$times)/$times"
else
  sleeptime=0
fi
if [ $sleeptime -lt 0 ]; then
  sleeptime=0
fi
# echo $sleeptime

# make cmd
# get script path
path=${0%/*}
fcmd="$php $cmd $5 $6 $7 $8"
echo $fcmd
# echo $fcmd

# repeat
i=0
#sleep 5
while [ "$i" -lt $times ]
#echo $sleeptime
do
  $fcmd
  let real_pg_time=$?
  let real_sleep_time=$sleeptime-$real_pg_time+$pg_time
# echo $real_sleep_time
# sleep $sleeptime
  sleep $real_sleep_time
  let "i=$i+1"
# echo $i
done
