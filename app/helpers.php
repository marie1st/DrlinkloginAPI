<?
if (! function_exists('includeRouteFiles')) {

/**
 * Recursively include/require all PHP files in the given folder
 *
 * @param string  $folder
 */
function includeRouteFiles($folder)
{
    $directory = $folder;
    $handle = opendir($directory);
    $directory_list = [$directory];

    while (false !== ($filename = readdir($handle))) {
        if ($filename != '.' && $filename != '..' && is_dir($directory.$filename)) {
            array_push($directory_list, $directory.$filename.'/');
        }
    }

    foreach ($directory_list as $directory) {
        foreach (glob($directory.'*.php') as $filename) {
            require $filename;
        }
    }
}
}