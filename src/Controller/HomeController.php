<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Author: Jerico Lua (+help of Make bundle)
 * Only has one route/function where you get redirected to the login page. If
 */
class HomeController extends AbstractController
{
    /**
     * Redirect page to loginpage. If already logged in you will be redirected to the employee list (defined in employee_list Route).
     * @return RedirectResponse
     */
    #[Route('/', name: 'app_home')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_login');
    }
}
