<?php

/*
|--------------------------------------------------------------------------
| Tenant developer themes
|--------------------------------------------------------------------------
|
| A tenant's developers work in their own GitHub repository that holds only
| static files (theme.json, CSS, JS, images, fonts). A push to the tracked
| branch fires a signed webhook; the platform downloads that commit, checks
| every file against the rules below and publishes it under
| public/<public_dir>/<company id>/<deployment>/. No file from a theme is
| ever executed on the server.
|
| See docs/TENANT_THEMES.md for the operator guide.
|
*/

return [

    // Folder under public/ that published themes live in. Never committed.
    'public_dir' => env('TENANT_THEMES_PUBLIC_DIR', 'tenant-themes'),

    // Scratch space for downloads and unpacking, under storage/app/.
    'staging_dir' => 'theme-staging',

    // GitHub API. The token needs read access ("Contents: Read-only") to the
    // tenant theme repositories; it is only needed for private repositories.
    'github' => [
        'api'     => env('TENANT_THEMES_GITHUB_API', 'https://api.github.com'),
        'token'   => env('TENANT_THEMES_GITHUB_TOKEN'),
        'timeout' => 30,
    ],

    // Successful deployments kept on disk for rollback (the live one included).
    'keep_deployments' => 5,

    'limits' => [
        'max_files'      => 400,
        'max_file_bytes' => 8 * 1024 * 1024,
        'max_total_bytes'=> 40 * 1024 * 1024,
        'max_zip_bytes'  => 50 * 1024 * 1024,
    ],

    // The only files allowed at the top of a theme.
    'root_files' => ['theme.json', 'theme.css', 'theme.js', 'dashboard.css', 'dashboard.js'],

    // Extensions allowed under assets/ (lower case, no dot).
    'asset_extensions' => [
        'css', 'js', 'json', 'map',
        'png', 'jpg', 'jpeg', 'gif', 'webp', 'avif', 'svg', 'ico',
        'woff', 'woff2', 'ttf', 'otf',
        'mp4', 'webm', 'mp3',
        'txt',
    ],

    // Repository files that are fine to have but are not published.
    'ignored' => [
        'README', 'README.md', 'README.txt', 'LICENSE', 'LICENSE.md', 'CHANGELOG.md',
        '.gitignore', '.gitattributes', '.editorconfig', '.prettierrc', '.DS_Store',
    ],
    'ignored_dirs' => ['.github', '.vscode', '.idea', 'node_modules', 'docs', 'src'],

];
