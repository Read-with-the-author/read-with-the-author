<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
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
        return $this->render(
            'index.html.twig',
            [
                'requestAccessTokenForm' => $this->createRequestAccessTokenForm()->createView(),
                'requestAccessForm' => $this->createRequestAccessForm()->createView()
            ]
        );
    }

    private function createRequestAccessTokenForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add(
                'emailAddress',
                EmailType::class,
                [
                    'label' => 'request_access_token_form.email_address.label',
                    'help' => 'request_access_token_form.email_address.help'
                ]
            )
            ->add(
                'request_access_token',
                SubmitType::class,
                [
                    'label' => 'request_access_token_form.request_access_token.label'
                ]
            )
            ->getForm();
    }

    private function createRequestAccessForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->add(
                'emailAddress',
                EmailType::class,
                [
                    'label' => 'request_access_form.email_address.label',
                    'help' => 'request_access_form.email_address.help'
                ]
            )
            ->add(
                'leanpubInvoiceId',
                TextType::class,
                [
                    'label' => 'request_access_form.leanpub_invoice_id.label',
                    'help' => 'request_access_form.leanpub_invoice_id.help'
                ]
            )
            ->add(
                'request_access',
                SubmitType::class,
                [
                    'label' => 'request_access_form.request_access.label'
                ]
            )
            ->getForm();
    }
}
