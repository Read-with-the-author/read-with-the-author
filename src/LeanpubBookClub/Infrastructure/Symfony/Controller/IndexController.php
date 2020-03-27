<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"})
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('index.html.twig');
    }
}
