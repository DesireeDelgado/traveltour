<?php

namespace App\Security;

use App\Entity\Usuario;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Usuario) {
            return;
        }

        if ($user->isBaneado() === true) {
            throw new CustomUserMessageAccountStatusException('Tu cuenta ha sido suspendida por infringir las normas de la comunidad.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof Usuario) {
            return;
        }

        if ($user->isBaneado() === true) {
            throw new CustomUserMessageAccountStatusException('Tu cuenta ha sido suspendida por infringir las normas de la comunidad.');
        }
    }
}
