#!/bin/sh

py_arr=('check_otc_order_30mins.py'  'robot.py' )
echo ${py_arr[0]}
echo ${py_arr[1]}
echo ${py_arr[2]}
length=${#py_arr[@]}
echo ${length}

basedir=`cd $(dirname $0); pwd -P`
echo $basedir

time=$(date +"%Y%m%d")
echo $time

for i in ${py_arr[@]};do
echo $i
ps -ef | grep $i | grep -v grep
if [ $? -ne 0 ]
then
echo "$i null" >> $basedir/$i.$time.log
nohup python -u $basedir/$i >> $basedir/$i.$time.log &
else
echo "$i runing..."
fi
done
