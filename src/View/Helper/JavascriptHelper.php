<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\View\Helper;
use Cake\View\View;
use SplFileInfo;

/**
 * JavascriptHelper helper
 */
class JavascriptHelper extends Helper
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'cache' => null
    ];

    /**
     * @param \Cake\View\View $View The View this helper is being attached to.
     * @param array $config Configuration settings for the helper.
     */
    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);
        if (Configure::read('debug')) {
            $this->setConfig('cache', null);
        }
    }

    /**
     * Returns an array of script names
     * For example from `main` automatically finds the latest `main.h1w37dsnhdipqhd83hbd831d.min.js`
     *
     * @param string ...$scripts
     * @return string[] Scripts
     */
    public function files(string ...$scripts): array
    {
        $cacheKey = 'rollup_javascript_' . join('_', $scripts);
        $cache = $this->getConfig('cache');
        if ($cache) {
            $script = Cache::read($cacheKey, $cache);
            if (is_array($script)) {

                return $script;
            }
        }

        $files = [];
        foreach ($scripts as $file) {
            $script_file = $this->find($file);
            if ($script_file) {
                array_push($files, $script_file->getFilename());
            }
        }

        if ($cache) {
            Cache::write($cacheKey, $files, $cache);
        }

        return $files;
    }


    /**
     * From a filename, returns the full path of a script file.
     *
     * Automatically searches for JS files in the js directory, in this order:
     *
     * - if a [filename].[hash].min.js is a readable file then the full path will be returned
     * - if there is more readable [filename].[hash].min.js file the last modified will be returned
     * - if the first two step gives no result then the method will make a search for [filename].min.js
     * - if the first three step gives no result then the method will make a search for [filename].js
     * - if the first four step gives no result then null will be returned
     *
     * @param string $filename Name of the file
     * @return null|SplFileInfo The full path/filename of the file or null on failure
     */
    public function find(string $filename): ?SplFileInfo
    {
        if (empty($filename)) {

            return null;
        }
        $clean_filename = preg_replace('/(\.app)?(\.[a-zA-Z0-9]+)?(\.min)?\.js$/', '', $filename);
        $current_file = null; $current_match = 0; $last_modified = 0;
        foreach ((new \DirectoryIterator(WWW_ROOT . 'js')) as $file) {
            $name = $file->getFilename();
            if (
                !$file->isFile()
                || !$file->isReadable()
                || $file->isDot()
                || $clean_filename !== preg_replace('/(\.app)?(\.[a-zA-Z0-9]+)?(\.min)?\.js$/', '', $name)
            ) {
                continue;
            }

            $is_new = false;
            if (preg_match('/\.[a-zA-Z0-9]+\.min.js$/', $name)) {
                if ($current_match < 2) {
                    $current_match = 2;
                    $is_new = true;
                } else if ($last_modified < $file->getMtime()) {
                    $is_new = true;
                }
            } else if (preg_match('/\.min.js$/', $name)) {
                if ($current_match < 1) {
                    $current_match = 1;
                    $is_new = true;
                } else if ($last_modified < $file->getMtime()) {
                    $is_new = true;
                }
            } else if ($last_modified < $file->getMtime()) {
                $is_new = true;
            }

            if ($is_new) {
                $current_file = $file->getPathname();
                $last_modified = $file->getMtime();
            }
        }

        return $current_file ? new SplFileInfo($current_file) : null;
    }

}
