<?php

foreach (['Public', 'Student', 'Advocate', 'Judge', 'TitleAbout'] as $role) {
    $path = "C:/Users/GMS/Downloads/{$role}_full_text.txt";
    if (!file_exists($path)) continue;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    echo "========================================\n";
    echo "ROLE FILE: $role (Total lines: " . count($lines) . ")\n";
    // If lines are single letters or words, let's join words
    $joined = "";
    foreach ($lines as $l) {
        $l = trim($l);
        if (strlen($l) == 1) {
            $joined .= $l;
        } else {
            $joined .= "\n" . $l;
        }
    }
    echo substr($joined, 0, 3000) . "\n";
}
