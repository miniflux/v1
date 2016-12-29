<?php

$reference_lang = 'fr_FR';
$reference_file = __DIR__.'/../app/locales/'.$reference_lang.'/translations.php';
$reference = include $reference_file;


function update_missing_locales(array $reference, $outdated_file)
{
    $outdated = include $outdated_file;

    $output = "<?php\n\n";
    $output .= "return array(\n";

    foreach (array_keys($reference) as $key) {
        $outputKey = str_replace("'", "\'", $key);
        $outputValue = "    // '".$outputKey."' => ''";

        if (isset($outdated[$key])) {
            if ($key === 'plural') {
                $outputValue = $outputKey."' => ".getFunctionCode($outdated[$key]);
            }
            elseif (is_array($outdated[$key])) {
                foreach($outdated[$key] as &$value) {
                    $value = str_replace("'", "\'", $value);
                }

                $outputValue = $outputKey."' => array('".join("', '", $outdated[$key])."')";
            }
            else {
                $outputValue = $outputKey."' => '".str_replace("'", "\'", $outdated[$key])."'";
            }

            $outputValue = "    '".$outputValue;
        }

        $output .= $outputValue.",\n";
    }

    $output .= ");\n";
    return $output;
}


foreach (new DirectoryIterator('app/locales') as $fileInfo) {
    if (! $fileInfo->isDot() && $fileInfo->isDir() && $fileInfo->getFilename() !== $reference_lang && $fileInfo->getFilename() !== 'en_US') {
        $filename = 'app/locales/'.$fileInfo->getFilename().'/translations.php';

        echo $fileInfo->getFilename(), ' (', $filename, ')', PHP_EOL;

        file_put_contents($filename, update_missing_locales($reference, $filename));
    }
}

function getFunctionCode($name)
{
    $reflector = new ReflectionFunction($name);

    $file = new SplFileObject($reflector->getFileName());
    $file->seek($reflector->getStartLine()-1);

    $sourcecode = '';
    while ($file->key() < $reflector->getEndLine())
    {
        $sourcecode .= $file->current();
        $file->next();
    }

    $begin = strpos($sourcecode, 'function');
    $end = strrpos($sourcecode, '}');

    return substr($sourcecode, $begin, $end - $begin + 1);
}
