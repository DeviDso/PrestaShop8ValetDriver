<?php

class PrestaShop8ValetDriver extends ValetDriver
{
    /**
     * Determine if the driver serves the request.
     *
     * @param  string $sitePath
     * @param  string $siteName
     * @param  string $uri
     *
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        return file_exists($sitePath . '/classes/PrestaShopAutoload.php');
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string $sitePath
     * @param  string $siteName
     * @param  string $uri
     *
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        $imageRegex = '/^(?<folder>c|p)\/(?<id>[0-9a-zA-Z_-]+)(?:-(?<suffix>[\.*_a-zA-Z0-9-]*))?(?:-(?<number>[0-9]+))?(?:\/.+\.jpg)$/i';

        if (preg_match($imageRegex, $uri, $matches)) {
            $folder = $matches['folder'];
            $id = $matches['id'];
            $suffix = isset($matches['suffix']) ? $matches['suffix'] : '';
            $number = isset($matches['number']) ? $matches['number'] : '';

            if ($folder === 'c') {
                $staticFilePath = "{$sitePath}/img/c/{$id}{$suffix}{$number}.jpg";
            } else {
                $dirs = str_split($id, 1);
                $staticFilePath = "{$sitePath}/img/p/" . implode('/', $dirs) . "/{$id}{$suffix}{$number}.jpg";
            }

            if (is_file($staticFilePath)) {
                return $staticFilePath;
            }
        }

        if (is_file($staticFilePath = "{$sitePath}/{$uri}")) {
            return $staticFilePath;
        }

        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string $sitePath
     * @param  string $siteName
     * @param  string $uri
     *
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        $parts = explode('/', $uri);
        $adminIdex = $sitePath . '/' . $parts[1] . '/index.php';

        if (isset($parts[1]) && $parts[1] != '' && file_exists($adminIdex)) {
            $_SERVER['SCRIPT_FILENAME'] = $adminIdex;
            $_SERVER['SCRIPT_NAME']     = '/' . $parts[1] . '/index.php';

            if (isset($_GET['controller']) || isset($_GET['tab'])) {
                return $adminIdex;
            }

            return $adminIdex;
        }

        $_SERVER['SCRIPT_NAME']     = '/index.php';
        $_SERVER['SCRIPT_FILENAME'] = $sitePath . '/index.php';

        return $sitePath . '/index.php';
    }
}

