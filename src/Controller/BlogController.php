<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/{_locale}",
     *     requirements={"_locale": "fr|en"},
     *     defaults={"_locale": "fr"},
     *     name="home")
     */
    public function helloWorldAction()
    {
        return $this->render("base.html.twig");
    }

    /**
     * @Route("/changeTheme/{theme}", name="change_theme")
     */
    public function changeTheme(string $theme)
    {
        $session = $this->get('session');
        $session->set('theme', $theme);
        return new Response('theme changed');
    }

    /**
     * @Route("/countArticles", name="count_articles")
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function countArticles(EntityManagerInterface $em)
    {
        $nbArticlesMine = $em->getRepository('App:Article')->countNbArticlesMine($this->getUser());
        $nbFavouriteArticles = count($this->getUser()->getFavouriteArticles());

        return new JsonResponse(['mine' => $nbArticlesMine, 'favourite' => $nbFavouriteArticles]);
    }
}