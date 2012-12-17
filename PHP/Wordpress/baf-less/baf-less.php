<?php
/**
 * Plugin Name: Ben Freke LessCSS auto compiler
 * This still needs a bit of work to convert it to proper WP hooks and stuff
 */

// Include the required files
require 'lessc.inc.php';

/**
 * Automatically compiles the less css
 * @param $less_fname
 * @param $css_fname
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

if (WP_DEBUG) { // A basic check so you don't have to change code between servers. Just change the config value.
    $filePath = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'less' . DIRECTORY_SEPARATOR;
    autoCompileLess($filePath . 'bootstrap.less', $filePath . 'styleCompiled.css');
    add_action('wp_enqueue_scripts', 'bafLessStyles');
}

function bafLessStyles()
{
    wp_register_style( 'baf-lesscss', get_template_directory_uri() . '/less/styleCompiled.css', array(), '1.0', 'all' );
    wp_enqueue_style( 'baf-lesscss' );
}