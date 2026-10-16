<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum TaskStatus: string implements TranslatableInterface
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('task_status.'.$this->value, locale: $locale);
    }
}
