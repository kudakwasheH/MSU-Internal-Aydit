<?php
$dir = __DIR__ . '/resources';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($files as $file) {
    if ($file->isFile() && (str_ends_with($file->getFilename(), '.blade.php') || str_ends_with($file->getFilename(), '.css'))) {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        $newContent = str_replace(
            ['#800000', '#FFD700', '#660000'],
            ['#00265B', '#F5C735', '#001533'],
            $content
        );
        if ($content !== $newContent) {
            file_put_contents($path, $newContent);
            echo "Updated: $path\n";
        }
    }
}
echo "Done.\n";
