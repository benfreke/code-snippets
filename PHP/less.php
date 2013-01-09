<?php
/**
 * Basic PHP to auto compile LESS on the server with no thinking required.
 * Once it is setup it works perfectly. If there are any issues (especially when moving between servers),
 *  delete the cached file and try again.
 *
 * I suppose technically everything can go in the one file, but for neatness
 *  I think it's better to separate the two
 *
 * Far more information, and the full code, is available here: http://leafo.net/lessphp
 */


/**
 * Put this code at the header of your template. Update paths as required
 */
if ($this->debug) { // A basic check so you don't have to change code between servers. Just change the config value.
    $filePath = $_SERVER['DOCUMENT_ROOT'];
    include($filePath . '/includes/autoCompileLess.php');
    autoCompileLess($filePath . '/css/less/style.less', $filePath . '/css/styleCompiled.css');
}

/**
 * This is the entirety of the autoCompileLess.php file
 */
// Our helper class, http://leafo.net/lessphp
// https://github.com/leafo/lessphp/blob/master/lessc.inc.php
require 'lessc.inc.php';

/**
 * Creates our less file, either from the cache or programatticaly
 *
 * @param string $less_fname The main less file which includes everything
 * @param string $css_fname  The final compiled css filename.
 */
function autoCompileLess($less_fname, $css_fname)
{
    $cache = $less_fname;

    // We may want to load from the cache
    $cache_fname = $less_fname . ".cache";
    if (file_exists($cache_fname)) {
        // Use the cache if the filetime is newer
        $cacheTime = filemtime($cache_fname);
        $fileTime  = filemtime($less_fname);
        // If I managed to read both and the cache is still relevant, use it
        if ($cacheTime && $fileTime && $fileTime < $cacheTime) {
            $cache = unserialize(file_get_contents($cache_fname));
        }
    }

    $new_cache = lessc::cexecute($cache);
    if (!is_array($cache) || $new_cache['updated'] > $cache['updated']) {
        // Our final, compiled styles
        file_put_contents($css_fname, $new_cache['compiled']);
        // Cache it, so we load faster!
        file_put_contents($cache_fname, serialize($new_cache));
    }
}       