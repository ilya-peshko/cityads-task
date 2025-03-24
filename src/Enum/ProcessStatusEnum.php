<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Перечисление статусов процесса запуска.
 */
enum ProcessStatusEnum: string
{
    case PENDING = 'pending';

    case IN_PROGRESS = 'in_progress';

    case COMPLETED = 'completed';

    case FAILED = 'failed';
}
