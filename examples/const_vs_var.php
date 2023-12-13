<?php
echo "<h1>It is a simple test to show that variables are faster than constant by around 20%</h1>";
echo "<pre>";
const A1=[1,2,3,4];
$a1=[1,2,3,4];
$t1=microtime(true);
for($i=0;$i<1000000;$i++) {
    $d=A1[1];
}
$t2=microtime(true);
echo $t2-$t1."\n";
$t1=microtime(true);
for($i=0;$i<1000000;$i++) {
    $d=$a1[1];
}
$t2=microtime(true);
echo $t2-$t1."\n";
