<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/{_locale}/login", requirements={"_locale": "fr|en"}, name="app_login")
     *
     * @return Response
     */
    public function index(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();

        $lastUsername = $authUtils->getLastUsername();

        $targetPath = $request->getSession()->get('_security.main.target_path');

        if ($targetPath == null) {
            $targetPath = 'article_index';
        }

        return $this->render('security/login.html.twig', [
            'targetPath' => $targetPath,
            'error' => $error,
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * @Route("/{_locale}/login_check", requirements={"_locale": "fr|en"}, name="app_login_check")
     *
     * @return Response
     * @throws \Exception
     */
    public function loginCheckAction()
    {
        throw new \Exception('Unexpected loginCheck action');
    }

    /**
     * @Route("/{_locale}/logout", requirements={"_locale": "fr|en"}, name="app_logout")
     *
     * @return Response
     * @throws \Exception
     */
    public function logoutAction()
    {
        throw new \Exception('Unexpected logout action');
    }
}
