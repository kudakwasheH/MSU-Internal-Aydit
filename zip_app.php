<?php
$zip = new ZipArchive();
$filename = "internal_audit.zip";
if ($zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    exit("Cannot open <$filename>\n");
}
$folders = ['app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes', 'tests', 'artisan', 'composer.json', 'composer.lock', 'package.json', 'vite.config.js', '.env.example', 'deploy_app.sh', 'update_app.sh', 'internal_audit.conf'];
foreach ($folders as $folder) {
    if (is_dir($folder)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder));
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen(__DIR__) + 1);
                $zip->addFile($filePath, str_replace('\\', '/', $relativePath));
            }
        }
    } else if (is_file($folder)) {
        $zip->addFile($folder, $folder);
    }
}
$zip->close();
echo "Zipped successfully\n";
