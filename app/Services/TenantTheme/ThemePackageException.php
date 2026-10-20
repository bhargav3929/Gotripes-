<?php

namespace App\Services\TenantTheme;

use RuntimeException;

/**
 * A theme was refused. The message is written for the tenant's developer:
 * it says which file or field is wrong and what is allowed instead.
 */
class ThemePackageException extends RuntimeException
{
}
