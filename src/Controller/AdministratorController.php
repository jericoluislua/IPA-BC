<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Mainly used for interaction with the Administrator Entity.
 * This whole controller including the login and logout functions was generated with the help of the make bundle.
 * The only thing adjusted for this project is the path and the if inside the login function.
 * Author: Jerico Lua (+help of Make bundle)
 */
class AdministratorController extends AbstractController
{
    /**
     * Not written by Jerico Lua
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route(path: '/administrator', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // On default this was commented out.
        // Comment symbols have been removed to redirect already logged in users to the employee list.
         if ($this->getUser()) {
             return $this->redirectToRoute('employee_list');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Not written by Jerico Lua
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
