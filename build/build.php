<?php

$file_phar = __DIR__.'\SimpleFill.phar';
$exclusions = [
	'.git',
	'README.md',
];
$files = [];
$dir = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'SimpleFill';

if(file_exists($file_phar)){
	echo 'Phar file already exists, overwriting...';
	echo PHP_EOL;
	Phar::unlinkArchive($file_phar);
}
foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)) as $path => $file){
	foreach($exclusions as $exclusion) if(str_contains($path, $exclusion)) continue 2;
	echo 'added '.$path.PHP_EOL;
	if($file->isFile() === false) continue;
	$files[str_replace($dir,'',$path)] = $path;
}

echo 'Compressing...'.PHP_EOL;
$phar = new Phar($file_phar, 0);
$phar->startBuffering();
$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->buildFromIterator(new \ArrayIterator($files));
$phar->setStub('<?php __HALT_COMPILER(); ?>');

if(isset($argv[1]) and $argv[1] === 'enableCompressAll'){
	$phar->compressFiles(Phar::GZ);

}else{
	foreach($phar as $file => $finfo){
		/** @var \PharFileInfo $finfo */
		if($finfo->getSize() > (1024 * 512)){
			$finfo->compress(\Phar::GZ);
		}
	}
}
$phar->setStub(file_get_contents(__DIR__.'\Stub.php'));
$phar->stopBuffering();
echo 'end.'.PHP_EOL;