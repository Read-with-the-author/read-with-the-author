<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use LeanpubBookClub\Application\Assets;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/assets")
 */
final class AssetsController extends AbstractController
{
    private Assets $assets;

    public function __construct(Assets $assets)
    {
        $this->assets = $assets;
    }

    /**
     * @Route("/title_page.jpg", methods={"GET"})
     */
    public function titlePageImageAction(): Response
    {
        $titlePageImagePath = $this->assets->titlePageImagePath();

        if (!file_exists($titlePageImagePath)) {
            throw $this->createNotFoundException();
        }

        $response = new BinaryFileResponse($titlePageImagePath);
        $response->headers->set('Content-Type', 'image/jpeg');

        return $response;
    }
}
