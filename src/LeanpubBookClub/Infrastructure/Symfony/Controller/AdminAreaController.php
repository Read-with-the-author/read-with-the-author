<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\PlanSession;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Infrastructure\Symfony\Form\PlanSessionForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin-area")
 */
final class AdminAreaController extends AbstractController
{
    private ApplicationInterface $application;

    private TimeZone $authorTimeZone;

    public function __construct(ApplicationInterface $application, TimeZone $authorTimeZone)
    {
        $this->application = $application;
        $this->authorTimeZone = $authorTimeZone;
    }

    /**
     * @Route("/", name="admin_area_index", methods={"GET"})
     */
    public function indexAction(): Response
    {
        return $this->render(
            'admin_area/index.html.twig',
            [
                'form' => $this->createPlanSessionForm()->createView()
            ]
        );
    }

    /**
     * @Route("/plan-session", name="plan_session", methods={"POST"})
     */
    public function planSessionAction(Request $request): Response
    {
        $form = $this->createForm(PlanSessionForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $this->application->planSession(
                new PlanSession(
                    $formData['date']->format('Y-m-d H:i'),
                    $this->authorTimeZone->asString(),
                    $formData['description'],
                    $formData['maximumNumberOfParticipants']
                )
            );
            return $this->redirectToRoute('admin_area_index');
        }

        return $this->render(
            'admin_area/index.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    private function createPlanSessionForm(): FormInterface
    {
        return $this->createForm(
            PlanSessionForm::class,
            null,
            ['action' => $this->generateUrl('plan_session')]
        );
    }
}
