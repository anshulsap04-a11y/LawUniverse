<?php

function getPdfObjects($content) {
    $objects = [];
    preg_match_all('/(\d+)\s+0\s+obj\s*(.*?)\s*endobj/s', $content, $matches, PREG_SET_ORDER);
    foreach ($matches as $m) {
        $id = intval($m[1]);
        $body = $m[2];
        $stream = null;
        if (preg_match('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $body, $sMatch)) {
            $stream = $sMatch[1];
        }
        $objects[$id] = [
            'body' => $body,
            'stream' => $stream
        ];
    }
    return $objects;
}

function parseCmapStream($stream) {
    $decompressed = @gzuncompress($stream);
    if (!$decompressed) $decompressed = @gzinflate($stream);
    if (!$decompressed) return [];
    
    $cmap = [];
    if (preg_match_all('/<([0-9a-fA-F]+)>\s+<([0-9a-fA-F]+)>/', $decompressed, $bfchars)) {
        for ($k = 0; $k < count($bfchars[0]); $k++) {
            $cid = hexdec($bfchars[1][$k]);
            $uni = hexdec($bfchars[2][$k]);
            $cmap[$cid] = mb_chr($uni, 'UTF-8');
        }
    }
    if (preg_match_all('/<([0-9a-fA-F]+)>\s+<([0-9a-fA-F]+)>\s+<([0-9a-fA-F]+)>/', $decompressed, $bfranges)) {
        for ($k = 0; $k < count($bfranges[0]); $k++) {
            $startCid = hexdec($bfranges[1][$k]);
            $endCid = hexdec($bfranges[2][$k]);
            $startUni = hexdec($bfranges[3][$k]);
            for ($cid = $startCid; $cid <= $endCid; $cid++) {
                $cmap[$cid] = mb_chr($startUni + ($cid - $startCid), 'UTF-8');
            }
        }
    }
    return $cmap;
}

function extractPdfFullText($filePath) {
    if (!file_exists($filePath)) return "File not found";
    $content = file_get_contents($filePath);
    $objs = getPdfObjects($content);
    
    // Find all fonts and their /ToUnicode obj id
    $universalCmap = [];
    foreach ($objs as $id => $obj) {
        if (preg_match('/\/Type\s*\/Font\b/', $obj['body']) && preg_match('/\/ToUnicode\s+(\d+)\s+0\s+R/', $obj['body'], $toUni)) {
            $uniId = intval($toUni[1]);
            if (isset($objs[$uniId]) && $objs[$uniId]['stream']) {
                $cmap = parseCmapStream($objs[$uniId]['stream']);
                foreach ($cmap as $cid => $ch) {
                    $universalCmap[$cid] = $ch;
                }
            }
        }
    }
    
    // Now decode all content streams
    $fullText = "";
    foreach ($objs as $id => $obj) {
        if (!$obj['stream']) continue;
        $decompressed = @gzuncompress($obj['stream']);
        if (!$decompressed) $decompressed = @gzinflate($obj['stream']);
        if (!$decompressed) continue;
        
        if (strpos($decompressed, 'BT') !== false) {
            // Find hex string arrays in TJ or hex strings in Tj
            preg_match_all('/(<[0-9a-fA-F]+>|\[[^\]]*\])\s*(?:Tj|TJ)/s', $decompressed, $tMatches);
            if (!empty($tMatches[1])) {
                foreach ($tMatches[1] as $item) {
                    preg_match_all('/<([0-9a-fA-F]+)>/', $item, $hexParts);
                    $line = "";
                    if (!empty($hexParts[1])) {
                        foreach ($hexParts[1] as $hexStr) {
                            for ($i = 0; $i < strlen($hexStr); $i += 4) {
                                $cidHex = substr($hexStr, $i, 4);
                                if (strlen($cidHex) === 4) {
                                    $cid = hexdec($cidHex);
                                    $line .= $universalCmap[$cid] ?? '';
                                }
                            }
                        }
                    }
                    if (trim($line) !== '') {
                        $fullText .= $line . "\n";
                    }
                }
            }
        }
    }
    
    return $fullText;
}

$files = [
    'Public' => 'C:/Users/GMS/Downloads/Law Universe Control Panel - Public.pdf',
    'Student' => 'C:/Users/GMS/Downloads/Law Universe Control Panel - Student.pdf',
    'Advocate' => 'C:/Users/GMS/Downloads/Law Universe Login - Advocate.pdf',
    'Judge' => 'C:/Users/GMS/Downloads/Law Universe Control Panel - Judge.pdf',
    'TitleAbout' => 'C:/Users/GMS/Downloads/Law Universe Tilte & About.pdf',
];

foreach ($files as $name => $path) {
    echo "========================================================\n";
    echo "DECODING: $name\n";
    $text = extractPdfFullText($path);
    echo substr($text, 0, 1500) . "\n";
    file_put_contents("C:/Users/GMS/Downloads/{$name}_full_text.txt", $text);
}
