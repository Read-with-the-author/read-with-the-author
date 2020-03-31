<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\RequestAccess\RequestAccess;
use LeanpubBookClub\Infrastructure\Symfony\Form\RequestAccessForm;
use LeanpubBookClub\Infrastructure\Symfony\Form\RequestAccessTokenForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class IndexController extends AbstractController
{
    private ApplicationInterface $application;

    private TranslatorInterface $translator;

    public function __construct(ApplicationInterface $application, TranslatorInterface $translator)
    {
        $this->application = $application;
        $this->translator = $translator;
    }

    /**
     * @Route("/", name="index", methods={"GET"})
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

            $this->application->requestAccess(
                new RequestAccess($formData['leanpubInvoiceId'], $formData['emailAddress'])
            );

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
     * @Route("/request-access-token", name="request_access_token", methods={"POST"})
     */
    public function requestAccessTokenAction(Request $request): Response
    {
        $form = $this->createRequestAccessTokenForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $this->application->generateAccessToken($formData['leanpubInvoiceId']);

            return $this->redirectToRoute('index');
        }

        return $this->render(
            'index.html.twig',
            [
                'requestAccessTokenForm' => $form->createView(),
                'requestAccessForm' => $this->createRequestAccessForm()->createView()
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
        return $this->createForm(
            RequestAccessTokenForm::class,
            null,
            [
                'action' => $this->generateUrl('request_access_token')
            ]
        );
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
