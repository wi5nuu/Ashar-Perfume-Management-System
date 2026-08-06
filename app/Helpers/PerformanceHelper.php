<?php

namespace App\Helpers;

class PerformanceHelper
{
    /**
     * Generate responsive image srcset
     * 
     * @param string $imagePath
     * @param array $sizes [320, 640, 960, 1280]
     * @return string
     */
    public static function responsiveImage(string $imagePath, array $sizes = [320, 640, 960, 1280]): string
    {
        $srcset = [];
        
        foreach ($sizes as $size) {
            // Assuming you have image resizing logic
            $srcset[] = asset($imagePath) . " {$size}w";
        }
        
        return implode(', ', $srcset);
    }

    /**
     * Preload critical resources
     * 
     * @param array $resources
     * @return string
     */
    public static function preloadResources(array $resources): string
    {
        $html = '';
        
        foreach ($resources as $resource) {
            $type = $resource['type'] ?? 'script';
            $href = $resource['href'];
            $as = $resource['as'] ?? $type;
            
            $html .= "<link rel=\"preload\" href=\"{$href}\" as=\"{$as}\">\n";
        }
        
        return $html;
    }

    /**
     * Generate critical CSS inline tag
     * 
     * @param string $cssFile
     * @return string
     */
    public static function inlineCriticalCSS(string $cssFile): string
    {
        if (file_exists(public_path($cssFile))) {
            $css = file_get_contents(public_path($cssFile));
            return "<style>{$css}</style>";
        }
        
        return '';
    }

    /**
     * Lazy load image HTML
     * 
     * @param string $src
     * @param string $alt
     * @param string $class
     * @return string
     */
    public static function lazyImage(string $src, string $alt = '', string $class = ''): string
    {
        return sprintf(
            '<img data-src="%s" alt="%s" class="%s loading-lazy" loading="lazy">',
            $src,
            htmlspecialchars($alt),
            $class
        );
    }

    /**
     * Get optimal image format (WebP if supported)
     * 
     * @param string $imagePath
     * @return string
     */
    public static function optimalImageFormat(string $imagePath): string
    {
        $webpPath = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $imagePath);
        
        if (file_exists(public_path($webpPath))) {
            return asset($webpPath);
        }
        
        return asset($imagePath);
    }

    /**
     * Calculate performance metrics
     * 
     * @return array
     */
    public static function getMetrics(): array
    {
        return [
            'memory_usage' => memory_get_usage(true) / 1024 / 1024, // MB
            'peak_memory' => memory_get_peak_usage(true) / 1024 / 1024, // MB
            'execution_time' => microtime(true) - LARAVEL_START, // seconds
            'queries_count' => \DB::getQueryLog() ? count(\DB::getQueryLog()) : 0,
        ];
    }

    /**
     * Cache wrapper with tags
     * 
     * @param string $key
     * @param int $ttl
     * @param callable $callback
     * @param array $tags
     * @return mixed
     */
    public static function cache(string $key, int $ttl, callable $callback, array $tags = [])
    {
        if (empty($tags)) {
            return cache()->remember($key, $ttl, $callback);
        }
        
        return cache()->tags($tags)->remember($key, $ttl, $callback);
    }

    /**
     * Generate mobile-optimized viewport meta tag
     * 
     * @param bool $userScalable
     * @return string
     */
    public static function viewportMeta(bool $userScalable = false): string
    {
        $content = "width=device-width, initial-scale=1.0";
        
        if (!$userScalable) {
            $content .= ", maximum-scale=1.0, user-scalable=no";
        }
        
        return "<meta name=\"viewport\" content=\"{$content}\">";
    }

    /**
     * Check if request is from mobile device
     * 
     * @return bool
     */
    public static function isMobile(): bool
    {
        $userAgent = request()->userAgent();
        
        return preg_match('/(android|iphone|ipad|mobile|tablet)/i', $userAgent);
    }

    /**
     * Get device type
     * 
     * @return string (desktop|tablet|mobile)
     */
    public static function getDeviceType(): string
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'tablet';
        }
        
        if (preg_match('/android|iphone|mobile/i', $userAgent)) {
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * Minify HTML output
     * 
     * @param string $html
     * @return string
     */
    public static function minifyHTML(string $html): string
    {
        $search = [
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        ];

        $replace = [
            '>',
            '<',
            '\\1',
            ''
        ];

        return preg_replace($search, $replace, $html);
    }

    /**
     * Format stock quantity in ml to a human-readable string.
     * Always shows both ml and liter:
     *   10000 → "10.000 ml / 10 L"
     *   1500  → "1.500 ml / 1,5 L"
     *   500   → "500 ml / 0,5 L"
     *   0     → "0 ml"
     */
    public static function formatMl(float|int $ml): string
    {
        if ($ml <= 0) {
            return '0 ml';
        }

        $mlFormatted = number_format($ml, 0, ',', '.');
        $liter = $ml / 1000;
        $literFormatted = ($liter == floor($liter))
            ? number_format((int)$liter, 0, ',', '.')
            : number_format($liter, 1, ',', '.');

        return $mlFormatted . ' ml / ' . $literFormatted . ' L';
    }
}
