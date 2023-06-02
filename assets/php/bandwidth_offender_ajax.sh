LATESTEPOC=`cat /usr/pbi/bandwidthd-amd64/local/bandwidthd/log.1.0.cdf | grep 1.43 | awk -F , '{print $2}' | tail -1`
echo $LATESTEPOC

TOP=`cat /usr/pbi/bandwidthd-amd64/local/bandwidthd/log.1.0.cdf | grep $LATESTEPOC | sort -t "," -k 10 -r -n | awk -F , '{print $10}' | head -2 | tail -1`

echo $TOP

IP=`cat /usr/pbi/bandwidthd-amd64/local/bandwidthd/log.1.0.cdf | grep $TOP | awk -F , '{print $1}'`

echo $IP

HOSTNAME=`/usr/bin/drill -x $IP | grep PTR | tail -1 | awk '{print $5}' | awk -F . '{print $1"."$2}'`

echo $HOSTNAME






