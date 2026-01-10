#!/bin/sh
#
# Aditus Development Research Notes - 2
# https://github.com/andreadavanzo/adrn-2
# SPDX-License-Identifier: MPL-2.0
# Copyright (c) Andrea Davanzo

SERVER="$1"
OUTPUT_FOLDER="$2"
TEST_NAME="${3:-test}"
TEST_DURATION="${4:-300}"
REQUEST_DELAY="${5:-0.250}"
BASELINE_DURATION="${6:-300}"

if [ -z "$SERVER" ] || [ -z "$OUTPUT_FOLDER" ]; then
  echo "Usage: $0 <server_user@host> <output_folder> [test_name] [test_duration] [request_delay] [baseline_duration]"
  exit 1
fi

SERVER_HOST="${SERVER#*@}"
mkdir -p "$OUTPUT_FOLDER"
MASTER_REMOTE_PATH="/tmp/raplog_${TEST_NAME}.csv"

# --------------------------------------------------
# [PRE-TEST SETUP]
# --------------------------------------------------
echo "--- Initializing Hardware State on remote server ---"
ssh "$SERVER" "sh /root/performance.sh && sh /root/noturbo.sh && rm -f $MASTER_REMOTE_PATH"

# --------------------------------------------------
# [STEP 0] Baseline Idle Measurement
# --------------------------------------------------
echo ""
echo "======================================"
echo "Step 0: Measuring BASELINE Power (Idle)"
echo "Duration: ${BASELINE_DURATION}s"
echo "======================================"

ssh "$SERVER" 'rc-service php-fpm83 restart && rc-service apache2 restart'
sleep 5

BASE_PID=$(ssh "$SERVER" "nohup sh /root/raplog.sh -o $MASTER_REMOTE_PATH -i 1 -t baseline > /dev/null 2>&1 & echo \$!")
echo "Baseline Logger PID: $BASE_PID. Waiting..."
sleep "$BASELINE_DURATION"
ssh "$SERVER" "kill $BASE_PID"

# --------------------------------------------------
# [FRAMEWORK TESTS]
# --------------------------------------------------
BASE_URL="http://${SERVER_HOST}/phpfs/framework"

urls="
  ${BASE_URL}/ci4/public/
  ${BASE_URL}/fat-free/
  ${BASE_URL}/laminas/public/
  ${BASE_URL}/laravel/public/
  ${BASE_URL}/symfony/public/
  ${BASE_URL}/yii/web/
"
names="ci4 fat-free laminas laravel symfony yii"

count=1
for name in $names; do
  url=$(echo "$urls" | sed -n "$((count + 1))p" | xargs)

  echo ""
  echo "======================================"
  echo "Testing framework: $name"
  echo "URL: $url"
  echo "======================================"

  ssh "$SERVER" 'rc-service php-fpm83 restart && rc-service apache2 restart'
  sleep 3

  RAPL_PID=$(ssh "$SERVER" "nohup sh /root/raplog.sh -o $MASTER_REMOTE_PATH -i 1 -t $name > /dev/null 2>&1 & echo \$!")

  echo "[Load] Sending requests for ${TEST_DURATION}s..."
  start_time=$(date +%s)
  end_time=$((start_time + TEST_DURATION))

  req_count=0
  while [ "$(date +%s)" -le "$end_time" ]; do
    # Perform request and capture status code
    status=$(curl -s -o /dev/null -w "%{http_code}" "$url")
    req_count=$((req_count + 1))
    printf "\rStatus: %s | Request: %d" "$status" "$req_count"
    sleep "$REQUEST_DELAY"
  done
  echo "" # New line after loop finishes

  ssh "$SERVER" "kill $RAPL_PID"

  count=$((count + 1))
  sleep 5
done

echo ""
echo "======================================"
echo "Downloading $MASTER_REMOTE_PATH"
scp "$SERVER:$MASTER_REMOTE_PATH" "$OUTPUT_FOLDER/${TEST_NAME}_rapl.csv"
echo "Done. Final log: $OUTPUT_FOLDER/${TEST_NAME}_rapl.csv"