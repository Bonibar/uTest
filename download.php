<?php

session_start();
$rpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
include_once $rpath.'params.php';
include_once $rpath.'model/utest.php';

if (!isset($_SESSION['download_ids']) || $_SESSION['download_ids'] == '' || !isset($_SESSION['tmp'])) {
    echo '<script>window.close();</script>';
    echo '<script>document.location.href="./home"</script>';
    exit(1);
}

$utests = unserialize($_SESSION['tmp']);
$ids = explode(';', $_SESSION['download_ids']);
if (count($ids) < 1) {
    echo '<script>window.close();</script>';
    echo '<script>document.location.href="./home"</script>';
    exit(1);
}

$str = '';
$sep = PHP_EOL;

foreach ($utests as $utest) {
    
    if (in_array($utest->getId(), $ids)) {
        if ($str != '') {
            $str .= $sep;
        }
        $str .= $utest->toString();
    }
}

header('Content-Disposition: attachment; filename=utests.sh');
header('Content-Type: text/plain'); # Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
header('Content-Length: ' . strlen($str));
header('Connection: close');

echo "#!/bin/bash".PHP_EOL;
echo "# Fichier genere par uTest : http://zwertv.fr/utest/".PHP_EOL;
echo "CRASHES=0".PHP_EOL;
echo "TESTS=0".PHP_EOL;
echo "FAILS=0".PHP_EOL.PHP_EOL;
echo "RED='\033[0;31m'
GRN='\033[0;32m'
OGN='\033[0;33m'
BLU='\033[1;34m'
NC='\033[0m'".PHP_EOL.PHP_EOL;
echo "DATE=$(date +\"%d%S%M%H%m%Y\")".PHP_EOL;
echo 'TMPFILE="/tmp/utest.$DATE"'.PHP_EOL;
echo 'if [ -d "logs" ]; then
    echo "logs directory found ! it will be removed. Continue ? (y/n)"
    read uinput
    if [ $uinput != "y" ]; then
        exit 0
    fi
fi
if [ -d "opts" ]; then
    echo "logs directory found ! it will be removed. Continue ? (y/n)"
    read uinput
    if [ $uinput != "y" ]; then
        exit 0
    fi
fi'.PHP_EOL;
echo "mkdir logs opts 2>/dev/null".PHP_EOL.PHP_EOL;
echo $str.PHP_EOL.PHP_EOL;
echo 'echo -e "\n\n${BLU}==== STATS ====${NC}"'.PHP_EOL;
echo 'echo "Number of tests: ${BLU}$TESTS${NC}"'.PHP_EOL;
echo 'echo "Number of tests passed: ${GRN}$((TESTS-FAILS))${NC}"'.PHP_EOL;
echo 'echo "Number of tests failed: ${OGN}$FAILS${NC}"'.PHP_EOL;
echo 'echo "Number of crashes: ${RED}$CRASHES${NC}"'.PHP_EOL;
echo 'echo "For more details, read $TMPFILE"'.PHP_EOL;
echo 'echo "${BLU}================${NC}"'.PHP_EOL;
echo 'echo -e "\n\n${BLU}==== STATS ====${NC}" >> $TMPFILE'.PHP_EOL;
echo 'echo "Number of tests: ${BLU}$TESTS${NC}" >> $TMPFILE'.PHP_EOL;
echo 'echo "Number of tests passed: ${GRN}$((TESTS-FAILS))${NC}" >> $TMPFILE'.PHP_EOL;
echo 'echo "Number of tests failed: ${OGN}$FAILS${NC}" >> $TMPFILE'.PHP_EOL;
echo 'echo "Number of crashes: ${RED}$CRASHES${NC}" >> $TMPFILE'.PHP_EOL;
echo 'echo "${BLU}================${NC}" >> $TMPFILE'.PHP_EOL;
echo "rm -rf logs opts".PHP_EOL;
