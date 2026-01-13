<?php

declare(strict_types=1);

namespace App\Modules\Tasks\Exceptions;

use RuntimeException;

/**
 * Исключение, выбрасываемое когда доска не выбрана
 */
final class BoardNotSelectedException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Доска не выбрана');
    }
}
