<?php
// Test PHP upload settings
echo "<h2>PHP Upload Configuration</h2>";
echo "<p><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</p>";
echo "<p><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</p>";
echo "<p><strong>max_execution_time:</strong> " . ini_get('max_execution_time') . "</p>";
echo "<p><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</p>";
echo "<p><strong>max_input_time:</strong> " . ini_get('max_input_time') . "</p>";
echo "<p><strong>max_file_uploads:</strong> " . ini_get('max_file_uploads') . "</p>";

// Hangi php.ini dosyası kullanılıyor?
echo "<h3>Kullanılan php.ini dosyası:</h3>";
echo "<p>" . php_ini_loaded_file() . "</p>";

echo "<h3>Ek yapılandırma dosyaları:</h3>";
$additional_inis = php_ini_scanned_dir();
if ($additional_inis) {
    echo "<p>" . $additional_inis . "</p>";
    $scan_files = glob($additional_inis . "/*.ini");
    if ($scan_files) {
        echo "<ul>";
        foreach ($scan_files as $file) {
            echo "<li>" . $file . "</li>";
        }
        echo "</ul>";
    }
}

// Convert to bytes for clarity
function convertToBytes($val)
{
    $val = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    $val = (float) $val;
    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

$upload_max = convertToBytes(ini_get('upload_max_filesize'));
$post_max = convertToBytes(ini_get('post_max_size'));

echo "<h3>Maximum Upload Size: " . min($upload_max, $post_max) / (1024 * 1024) . " MB</h3>";

if (min($upload_max, $post_max) >= 200 * 1024 * 1024) {
    echo "<p style='color: green;'><strong>✅ Ready for 200MB+ uploads!</strong></p>";
} else {
    echo "<p style='color: red;'><strong>❌ Not ready for large uploads</strong></p>";
}
