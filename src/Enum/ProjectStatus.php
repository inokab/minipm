<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ProjectStatus: string implements TranslatableInterface
{
    case Planned = 'planned';
    case Active = 'active';
    case Completed = 'completed';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('project_status.'.$this->value, locale: $locale);
    }
}
