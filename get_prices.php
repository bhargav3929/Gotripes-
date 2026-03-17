<?php
$db = new PDO('sqlite:c:/Users/Pragathi/Desktop/Gotripes--main (1)/Gotripes--main/database/database.sqlite');
$result = $db->query('SELECT activityID, activityPrice, activityChildPrice FROM tbl_UAEActivities WHERE isActive = 1 ORDER BY activityID');
foreach ($result as $row) {
    echo $row['activityID'] . " => ['adult' => " . ($row['activityPrice'] ?: 0) . ", 'child' => " . ($row['activityChildPrice'] ?: 0) . "],\n";
}
