<?php

$files = [
    'Public' => 'C:/Users/GMS/Downloads/Law Universe Control Panel - Public.pdf',
    'Student' => 'C:/Users/GMS/Downloads/Law Universe Control Panel - Student.pdf',
    'Advocate' => 'C:/Users/GMS/Downloads/Law Universe Login - Advocate.pdf',
    'Judge' => 'C:/Users/GMS/Downloads/Law Universe Control Panel - Judge.pdf',
    'TitleAbout' => 'C:/Users/GMS/Downloads/Law Universe Tilte & About.pdf',
];

foreach ($files as $name => $path) {
    echo "========================================\n";
    echo "FILE: $name ($path)\n";
    if (!file_exists($path)) {
        echo "File does not exist!\n";
        continue;
    }
    $raw = file_get_contents($path);
    echo "File size: " . strlen($raw) . "\n";
    
    // Look for streams
    $offset = 0;
    $texts = [];
    while (($start = strpos($raw, "stream", $offset)) !== false) {
        $streamStart = $start + 6;
        while ($streamStart < strlen($raw) && ($raw[$streamStart] === "\r" || $raw[$streamStart] === "\n")) {
            $streamStart++;
        }
        $end = strpos($raw, "endstream", $streamStart);
        if ($end === false) break;
        
        $chunk = substr($raw, $streamStart, $end - $streamStart);
        $offset = $end + 9;
        
        $decompressed = @gzuncompress($chunk);
        if (!$decompressed) $decompressed = @gzinflate($chunk);
        
        if ($decompressed) {
            // Find parentheses strings (PDF strings)
            preg_match_all('/\((.*?)(?<!\\\\)\)/s', $decompressed, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $m) {
                    $m = stripcslashes(trim($m));
                    // filter out binary/garbage
                    if (strlen($m) >= 2 && preg_match('/^[a-zA-Z0-9\s\-_.,:;!?()\/%#&@\'\"+=\[\]{}<>]+$/', $m)) {
                        $texts[] = $m;
                    }
                }
            }
        }
    }
    
    $uniqueTexts = array_values(array_unique($texts));
    echo "Extracted readable strings count: " . count($uniqueTexts) . "\n";
    echo "Sample strings:\n";
    echo implode("\n", array_slice($uniqueTexts, 0, 40)) . "\n";
}

