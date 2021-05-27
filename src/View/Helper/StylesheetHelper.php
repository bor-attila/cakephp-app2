<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\View\Helper;
use Cake\View\View;
use SplFileInfo;

/**
 * StyleSheetHelper helper
 */
class StylesheetHelper extends Helper
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
     * Returns the global stylesheet's content.
     * Automatically searches for the css/style[.hash]?[.min]?.css
     *
     * @param array $stylesheets Additional CSS files
     * @return string The style tag
     */
    public function global(array $stylesheets = []): string
    {
        $cache = $this->getConfig('cache');
        if ($cache) {
            $style = Cache::read('rollup_stylesheets_global', $cache);
            if (is_string($style)) {

                return $style;
            }
        }

        $files = array_unique(array_merge(['style'], $stylesheets));
        $style = '';
        foreach ($files as $file) {
            $style_path = $this->find($file);
            if ($style_path) {
                $style .= file_get_contents($style_path->getPathname());
            }
        }

        $this->addStyleTag($style);

        if ($cache) {
            Cache::write('rollup_stylesheets_global', $style, $cache);
        }

        return $style;
    }

    /**
     * Returns the local stylesheet's content
     * Automatically searches for the css/{prefix}-{controller}-{action}[.hash]?[.min]?.css or
     * css/{controller}-{action}[.hash]?[.min]?.css
     * if there is no prefix.
     *
     * @return string the style tag
     */
    public function local(): string
    {
        $prefix = $this->request->getParam('prefix');
        if ($prefix) {
            $name = strtolower(
                $prefix . '-' . $this->request->getParam('controller') . '-' .  $this->request->getParam('action')
            );
        } else {
            $name = strtolower(
                $this->request->getParam('controller') . '-' .  $this->request->getParam('action')
            );
        }

        return $this->inline($name);
    }

    /**
     * Returns the given stylesheets content
     * Automatically searches for the css/{name}[.hash]?[.min]?.css
     *
     * @param string $name Name of the CSS file (no extension or .min suffix)
     * @return string the style tag
     */
    public function inline(string $name): string
    {
        $cache = $this->getConfig('cache');
        if ($cache) {
            $style = Cache::read('rollup_stylesheets_inline_' . $name, $cache);
            if (is_string($style)) {
                return $style;
            }
        }
        $style_path = $this->find($name);

        if ($style_path) {
            $style = file_get_contents($style_path->getPathname());
        } else {
            $style = '';
        }

        $this->addStyleTag($style);

        if ($cache) {
            Cache::write('rollup_stylesheets_inline_' . $name, $style, $cache);
        }

        return $style;
    }

    /**
     * Encloses the style into style tags
     *
     * @param string $style
     */
    private function addStyleTag(string &$style): void
    {
        if (!empty($style)) {
            $style = "<style type='text/css'>{$style}</style>";
        } else {
            $style = '';
        }
    }

    /**
     * From a filename, returns the full path of a stylesheet.
     *
     * Automatically searches for CSS files in the css directory, in this order:
     *
     * - if a [filename].[hash].min.css is a readable file then the full path will be returned
     * - if there is more readable [filename].[hash].min.css file the last modified will be returned
     * - if the first two step gives no result then the method will make a search for [filename].min.css
     * - if the first three step gives no result then the method will make a search for [filename].css
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
        $clean_filename = preg_replace('/(\.[a-zA-Z0-9]+)?(\.min)?\.css$/', '', $filename);
        $current_file = null; $current_match = 0; $last_modified = 0;
        foreach ((new \DirectoryIterator(WWW_ROOT . 'css')) as $file) {
            $name = $file->getFilename();
            if (
                !$file->isFile()
                || !$file->isReadable()
                || $file->isDot()
                || $clean_filename !== preg_replace('/(\.[a-zA-Z0-9]+)?(\.min)?\.css$/', '', $name)
            ) {
                continue;
            }

            $is_new = false;
            if (preg_match('/\.[a-zA-Z0-9]+\.min.css$/', $name)) {
                if ($current_match < 2) {
                    $current_match = 2;
                    $is_new = true;
                } else if ($last_modified < $file->getMtime()) {
                    $is_new = true;
                }
            } else if (preg_match('/\.min.css$/', $name)) {
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
