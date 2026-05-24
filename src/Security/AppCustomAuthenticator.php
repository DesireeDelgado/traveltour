<?php

namespace App\Security;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private UsuarioRepository $usuarioRepository, private EntityManagerInterface $entityManager)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {
                // Buscamos al usuario por email manualmente para comprobar el Soft Delete antes de verificar credenciales
                $usuario = $this->usuarioRepository->findOneBy(['email' => $userIdentifier]);
                
                if (!$usuario) {
                    return null; // Devolverá UserNotFoundException interno
                }

                if ($usuario->getDeletedAt() !== null) {
                    $diasRestantes = 30 - $usuario->getDeletedAt()->diff(new \DateTimeImmutable())->days;
                    
                    if ($diasRestantes <= 0) {
                        // Han pasado más de 30 días, la cuenta es "irrecuperable" bajo este proceso manual
                        throw new CustomUserMessageAuthenticationException('Ya han pasado 30 días desde que borraste tu cuenta. Si quieres volver a usar la aplicación, regístrate de nuevo.');
                    }
                }
                
                return $usuario;
            }),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var Usuario $usuario */
        $usuario = $token->getUser();

        // Manejo de la reactivación si estaba borrado temporalmente y está bajo los 30 días
        if ($usuario->getDeletedAt() !== null) {
            $diasRestantes = 30 - $usuario->getDeletedAt()->diff(new \DateTimeImmutable())->days;
            $request->getSession()->getFlashBag()->add('warning', sprintf('Tu cuenta estaba programada para borrarse en %d días y ha sido reactivada. ¡Nos alegra tenerte de vuelta!', $diasRestantes));
            
            $usuario->setDeletedAt(null);
            $this->entityManager->flush();
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Si el usuario es administrador, redirigir al panel de administración
        if (in_array('ROLE_ADMIN', $usuario->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('admin'));
        }

        // Ruta al home privado una vez logueado
        return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
