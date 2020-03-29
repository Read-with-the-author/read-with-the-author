<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Infrastructure\Symfony\Form\RequestAccessForm;
use LeanpubBookClub\Infrastructure\Symfony\Form\RequestAccessTokenForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * @Route("/", methods={"GET"})
     */
    public function indexAction(): Response
    {
        return $this->render(
            'index.html.twig',
            [
                'requestAccessTokenForm' => $this->createRequestAccessTokenForm()->createView(),
                'requestAccessForm' => $this->createRequestAccessForm()->createView()
            ]
        );
    }

    /**
     * @Route("/request-access", name="request_access", methods={"POST"})
     */
    public function requestAccessAction(Request $request): Response
    {
        $form = $this->createRequestAccessForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $command = new RequestAccess($formData['leanpubInvoiceId'], $formData['emailAddress']);
            $this->application->requestAccess($command);

            return $this->redirectToRoute('access_requested');
        }

        return $this->render(
            'index.html.twig',
            [
                'requestAccessTokenForm' => $this->createRequestAccessTokenForm()->createView(),
                'requestAccessForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/access-requested", name="access_requested", methods={"GET"})
     */
    public function accessRequestedAction(): Response
    {
        return $this->render('access_requested.html.twig');
    }

    private function createRequestAccessTokenForm(): FormInterface
    {
        return $this->createForm(RequestAccessTokenForm::class);
    }

    private function createRequestAccessForm(): FormInterface
    {
        return $this->createForm(
            RequestAccessForm::class,
            null,
            [
                'action' => $this->generateUrl('request_access')
            ]
        );
    }
}
