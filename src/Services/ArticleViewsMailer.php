<?php
/**
 * Created by PhpStorm.
 * User: Steven
 * Date: 15/12/2019
 * Time: 17:20
 */

namespace App\Services;

use App\Entity\Article;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleViewsMailer implements EventSubscriber
{
    private $mailer;

    private $translator;

    private const NB_VIEWS_TO_SEND_AN_EMAIL = [10, 50, 100, 200, 400, 1000];

    public function __construct(\Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    public function getSubscribedEvents()
    {
        return ['preUpdate'];
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Article) {
            return;
        }

        if (!in_array(self::NB_VIEWS_TO_SEND_AN_EMAIL, $entity->getNbViews()) ) {
            return;
        }

        $message = new \Swift_Message($this->translator->trans('mailer.article.subject'),
            $this->translator->trans('mailer.article.nb_views', ['nb_views' => $entity->getNbViews()])
        );

        $message->addTo('steven45.sg@gmail.com')
                ->addFrom('steven45.sg@gmail.com');

        $this->mailer->send($message);
    }
}