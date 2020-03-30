<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member-area")
 */
final class MemberArea extends AbstractController
{
    /**
     * @Route("/", name="member_area_index", methods={"GET"})
     */
    public function indexAction(): Response
    {
        return $this->render('member_area/index.html.twig');
    }
}
