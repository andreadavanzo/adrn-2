#!/bin/bash
# framework_remote_power_test.sh
# Client-side orchestration for testing PHP frameworks with server-side RAPL logging via SSH
#
# Usage:
#   ./framework_remote_power_test.sh <server_user@host> <output_folder> [test_duration_sec]
#
# Example:
#   ./framework_remote_power_test.sh andrea@192.168.56.50 ./logs 300

SERVER="$1"
OUTPUT_FOLDER="$2"
TEST_DURATION="${3:-300}"   # default 5 minutes per framework
REQUEST_DELAY="0.1"         # delay between requests (seconds)

if [ -z "$SERVER" ] || [ -z "$OUTPUT_FOLDER" ]; then
  echo "Usage: $0 <server_user@host> <output_folder> [test_duration_sec]"
  exit 1
fi

# Extract host/IP from SSH target (user@host → host)
SERVER_HOST="${SERVER#*@}"

mkdir -p "$OUTPUT_FOLDER"

# Base URL built dynamically from SSH host
BASE_URL="http://${SERVER_HOST}/phpfs/framework"

# Framework URLs and names
urls=(
  "${BASE_URL}/ci4/public/"
  "${BASE_URL}/fat-free/"
  "${BASE_URL}/laminas/public/"
  "${BASE_URL}/laravel/public/"
  "${BASE_URL}/symfony/public/"
  "${BASE_URL}/yii/web/"
)

names=(
  "ci4"
  "fat-free"
  "laminas"
  "laravel"
  "symfony"
  "yii"
)

# --------------------------------------------------
# Test loop
# --------------------------------------------------
for i in "${!urls[@]}"; do
  url="${urls[$i]}"
  name="${names[$i]}"

  echo
  echo "======================================"
  echo "Testing framework: $name"
  echo "Target URL: $url"
  echo "======================================"

  # 1) Restart services on Alpine server (cold start)
  echo "[1/5] Restarting PHP-FPM and Apache on server..."
  ssh "$SERVER" 'sudo rc-service php-fpm83 restart && sudo rc-service apache2 restart'
  sleep 3

  # 2) Start RAPL logger remotely
  RAPL_REMOTE_PATH="/tmp/${name}_raplog.csv"
  echo "[2/5] Starting RAPL logger on server..."

  RAPL_PID=$(ssh "$SERVER" \
      "nohup /path/to/rapl_logger.sh -o $RAPL_REMOTE_PATH > /dev/null 2>&1 & echo \$!")

  echo "RAPL logger PID: $RAPL_PID"

  # 3) Run client-side load for TEST_DURATION
  echo "[3/5] Sending HTTP requests for ${TEST_DURATION}s..."
  end_time=$((SECONDS + TEST_DURATION))
  while [ $SECONDS -lt $end_time ]; do
    curl -s "$url" > /dev/null
    sleep "$REQUEST_DELAY"
  done

  # 4) Stop RAPL logger
  echo "[4/5] Stopping RAPL logger..."
  ssh "$SERVER" "kill $RAPL_PID"

  # 5) Copy RAPL CSV to client
  echo "[5/5] Retrieving RAPL log..."
  scp "$SERVER:$RAPL_REMOTE_PATH" "$OUTPUT_FOLDER/${name}_rapl.csv"

  echo "Saved: $OUTPUT_FOLDER/${name}_rapl.csv"

  sleep 5
done

echo
echo "======================================"
echo "All frameworks tested."
echo "Logs available in: $OUTPUT_FOLDER"
echo "======================================"
