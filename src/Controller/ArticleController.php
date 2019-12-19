<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Services\AntiSpam;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/{_locale}/blog", defaults={"_locale": "fr"}, requirements={"_locale": "fr|en"})
 * @IsGranted("ROLE_USER")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/list/{page}",
     *     defaults={"page": 1},
     *     name="article_index", methods={"GET"}
     *     )
     */
    public function listAction(int $page, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        if ($page >= 1) {
            $nbPerPage = $this->getParameter('nbPerPage');
            $currentPath = 'article_index';

            $articles = $em->getRepository('App:Article')
                ->findOnlyPublishedWithPaging($page, $nbPerPage);

            $nbPage = intval(ceil(count($articles) / $nbPerPage));

            if ($page > $nbPage) {
                throw new NotFoundHttpException("La page $page n'existe pas");
            }

            $title = $translator->trans('blog.list.title');

            return $this->render('article/list.html.twig',[
                'title' => $title,
                'page' => $page,
                'nbPage' => $nbPage,
                'currentPath' => $currentPath,
                'articles' => $articles
            ]);
        } else {
            throw new NotFoundHttpException("La page $page n'existe pas");
        }
    }

    /**
 * @Route("/articles/mine/{page}",
 *     defaults={"page": 1},
 *     name="article_mine", methods={"GET"}
 *     )
 */
    public function listMineAction(int $page, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        if ($page >= 1) {
            $nbPerPage = $this->getParameter('nbPerPage');
            $currentPath = 'article_mine';

            $articles = $em->getRepository('App:Article')
                ->findOnlyMineWithPaging($page, $nbPerPage, $this->getUser());

            $nbPage = intval(ceil(count($articles) / $nbPerPage));

            if ($page > $nbPage) {
                throw new NotFoundHttpException("La page $page n'existe pas");
            }

            $title = $title = $translator->trans('blog.list.title');

            return $this->render('article/list.html.twig',[
                'title' => $title,
                'page' => $page,
                'nbPage' => $nbPage,
                'currentPath' => $currentPath,
                'articles' => $articles
            ]);
        } else {
            throw new NotFoundHttpException("La page $page n'existe pas");
        }
    }

    /**
     * @Route("/articles/favourite/",
     *     name="favourite_articles", methods={"GET"}
     *     )
     */
    public function listFavouriteAction(TranslatorInterface $translator): Response
    {
            $currentPath = 'favourite_articles';

            $articles = $this->getUser()->getFavouriteArticles();

            $nbFavouriteArticles = count($articles);

            $title = $title = $translator->trans('blog.favourite.title');

            return $this->render('article/list_favourites.html.twig',[
                'title' => $title,
                'nbFavouriteArticles' => $nbFavouriteArticles,
                'currentPath' => $currentPath,
                'articles' => $articles
            ]);
    }

    /**
     * @Route("/category/{id}/{page}",
     *     defaults={"page": 1, "id": 0},
     *     name="category_article_index", methods={"GET"}
     *     )
     */
    public function listCategoryAction(int $id, int $page, EntityManagerInterface $em): Response
    {
        if ($id == 0) {
            $id = $em->getRepository('App:Category')->findOneBy([])->getId();
        }
        if ($id > 0) {
            $nbPerPage = $this->getParameter('nbPerPage');
            $currentPath = 'category_article_index';

            $category = $em->getRepository('App:Category')->find($id);

            $articles = $em->getRepository('App:Article')->findOnlyPublishedByCategory($category, $page, $nbPerPage);

            $nbPage = intval(ceil(count($articles) / $nbPerPage));

            $title = $category->getName();

            return $this->render("article/list.html.twig",[
                'id' => $id,
                'title' => $title,
                'currentPath' => $currentPath,
                'page' => $page,
                'nbPage' => $nbPage,
                'articles' => $articles
            ]);
        } else {
            throw new NotFoundHttpException('Cette page n\'existe pas');
        }
    }

    /**
     * @Route("/article/add", name="article_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function addAction(Request $request, AntiSpam $antiSpam,
                              EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->add('Ajouter', SubmitType::class, ['label' => 'Ajouter un nouvel article']);
        $form->handleRequest($request);
        $title = $translator->trans('blog.add.title');

        if ($form->isSubmitted() && $form->isValid()) {
            if ($antiSpam->isSpam($article->getContent())) {
                $form->get('content')->addError(new FormError('Le contenu est considéré comme un spam ! 
                Encore un comme ça et vous serez dénoncé à la CIA !!'));
                return $this->render('article/add.html.twig', [
                    'title' => $title,
                    'form' => $form->createView()
                ]);
            }
            $this->addFlash('success', $translator->trans('blog.flash.add.success'));
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/add.html.twig', [
            'title' => $title,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/{id}",
     *     defaults={"id": 1},
     *     name="article_show",
     *     methods={"GET"})
     * @ParamConverter("article", class="App:Article")
     */
    public function viewAction(Article $article, EntityManagerInterface $em, Security $security): Response
    {
        if ($article) {
            if (!$security->isGranted('view', $article)) {
                throw new NotFoundHttpException('L\'article est inconnu');
            }

            if ($article && ($article->getPublished() || $article->getUser() == $this->getUser())) {
                $article->setNbViews($article->getNbViews() + 1);
                $em->flush();
                return $this->render('article/view.html.twig', [
                    'article' => $article
                ]);
            }
        }
        $id = $article->getId();
        throw new NotFoundHttpException("L'article $id n'existe pas");
    }

    /**
     * @Route("/article/edit/{id}", name="article_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @ParamConverter("article", class="App:Article")
     */
    public function editAction(Article $article, Request $request, AntiSpam $antiSpam, Security $security,
                               EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        if (!$security->isGranted('edit', $article)) {
            throw new NotFoundHttpException('Vous n\'avez pas les droits de modification sur cet article');
        }

        $title = $translator->trans('blog.edit.title');

        $form = $this->createForm(ArticleType::class, $article);
        $form->add('Ajouter', SubmitType::class, ['label' => 'Modifier l\'article']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($antiSpam->isSpam($article->getContent())) {
                $this->addFlash('danger', "Le contenu est considéré comme un spam ! 
                Encore un comme ça et vous serez dénoncé à la CIA !!");

                return $this->render('article/add.html.twig', [
                    'title' => $title,
                    'form' => $form->createView()
                ]);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $translator->trans('blog.flash.edit.success'));

            if ($article->getPublished()) {
                return $this->redirectToRoute('article_show', ['id' => $article->getId()]);
            }

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/delete/{id}", name="article_delete")
     * @IsGranted("ROLE_ADMIN")
     * @ParamConverter("article", class="App:Article")
     */
    public function deleteAction(Article $article, EntityManagerInterface $em, Security $security,
                                 TranslatorInterface $translator): Response
    {
        if (!$security->isGranted('edit', $article)) {
            throw new NotFoundHttpException('Vous n\'avez pas les droits de modification sur cet article');
        }
        // Suppression
        if ($article) {
            if ($article->getPublished()) {
                $em->remove($article);
                $em->flush();

                $this->addFlash('success', $translator->trans('blog.flash.delete.success'));

                return $this->redirectToRoute('article_index');
            }
        }
        throw new NotFoundHttpException('L\'article ' . $article->getId() . ' n\'existe pas');
    }

    /**
     * @param int $nbArticles
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function recentArticlesAction($nbArticles = 3, EntityManagerInterface $em): Response
    {
        $articles = $em->getRepository('App:Article')->findBy([
            'published' => true
        ], [
            'created_at' => 'DESC'
        ], $nbArticles);

        $categories = $em->getRepository('App:Category')->findAllWithNbArticles();

        $nbArticlesTotal = 0;

        foreach ($categories as $category) {
            $nbArticlesTotal += $category['nbArticles'];
        }

        return $this->render('recent_articles.html.twig', [
            'nbArticlesTotal' => $nbArticlesTotal,
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/like/article/{id}", name="like_article")
     */
    public function likeArticle(int $id, Request $request, EntityManagerInterface $em)
    {
        if ($request->isXmlHttpRequest() && $id > 0) {
             $article = $em->getRepository('App:Article')->find($id);
             $this->getUser()->addFavouriteArticle($article);
             $em->flush();
            return new Response('Article added in favourite');
        }
        throw new NotFoundHttpException('La requête que vous envoyez n\'est pas conforme');
    }

    /**
     * @Route("/unlike/article/{id}", name="unlike_article")
     */
    public function unlikeArticle(int $id, Request $request, EntityManagerInterface $em)
    {
        if ($request->isXmlHttpRequest() && $id > 0) {
            $article = $em->getRepository('App:Article')->find($id);
            $this->getUser()->removeFavouriteArticle($article);
            $em->flush();
            return new Response('Article removed from favourite');
        }
        throw new NotFoundHttpException('La requête que vous envoyez n\'est pas conforme');
    }

}
