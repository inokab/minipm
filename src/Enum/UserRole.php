<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum UserRole: string implements TranslatableInterface
{
    case Admin = 'ROLE_ADMIN';
    case Member = 'ROLE_MEMBER';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('user_role.'.$this->value, locale: $locale);
    }
}
