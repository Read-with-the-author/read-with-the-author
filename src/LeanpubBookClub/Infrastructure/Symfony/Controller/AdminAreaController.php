<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\PlanSession;
use LeanpubBookClub\Application\UpdateSession;
use LeanpubBookClub\Domain\Model\Common\TimeZone;
use LeanpubBookClub\Infrastructure\Symfony\Form\EditSessionForm;
use LeanpubBookClub\Infrastructure\Symfony\Form\PlanSessionForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
                'form' => $this->createForm(PlanSessionForm::class)->createView(),
                'upcomingSessions' => $this->application->listUpcomingSessionsForAdministrator(),
                'members' => $this->application->listMembersForAdministrator(),
                'purchases' => $this->application->listAllPurchasesForAdministrator(),
                'authorTimeZone' => $this->authorTimeZone->asString()
            ]
        );
    }

    /**
     * @Route("/logout", name="admin_area_logout", methods={"GET"})
     */
    public function logoutAction(): Response
    {
        return new RedirectResponse($this->generateUrl('index'));
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
                    $formData['duration'],
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

    /**
     * @Route("/edit-session/{sessionId}", name="edit_session", methods={"GET", "POST"})
     */
    public function editSessionAction(Request $request, string $sessionId): Response
    {
        $session = $this->application->getSessionForAdministrator($sessionId);

        $form = $this->createForm(
            EditSessionForm::class,
            [
                'description' => $session->description(),
                'urlForCall' => $session->urlForCall()
            ],
            [
                'action' => $this->generateUrl('edit_session', ['sessionId' => $sessionId])
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $this->application->updateSession(
                new UpdateSession($sessionId, $formData['description'], $formData['urlForCall'])
            );

            return $this->redirectToRoute('admin_area_index');
        }

        return $this->render(
            'admin_area/edit_session.html.twig',
            [
                'session' => $session,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/cancel-session/", name="cancel_session", methods={"POST"})
     */
    public function cancelSessionAction(Request $request): Response
    {
        $this->application->cancelSession((string)$request->request->get('sessionId'));

        return $this->redirectToRoute('admin_area_index');
    }
}
