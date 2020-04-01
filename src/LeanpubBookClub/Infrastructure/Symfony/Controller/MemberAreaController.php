<?php
declare(strict_types=1);

namespace LeanpubBookClub\Infrastructure\Symfony\Controller;

use Assert\Assert;
use LeanpubBookClub\Application\ApplicationInterface;
use LeanpubBookClub\Application\AttendSession;
use LeanpubBookClub\Application\CancelAttendance;
use LeanpubBookClub\Application\Members\Member;
use LeanpubBookClub\Application\UpdateTimeZone;
use LeanpubBookClub\Infrastructure\Symfony\Form\UpdateTimeZoneForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/member-area")
 */
final class MemberAreaController extends AbstractController
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * @Route("/login", name="login", methods={"GET"})
     */
    public function loginAction(): Response
    {
        return new RedirectResponse($this->generateUrl('index'));
    }

    /**
     * @Route("/", name="member_area_index", methods={"GET"})
     */
    public function indexAction(UserInterface $member): Response
    {
        Assert::that($member)->isInstanceOf(Member::class);
        /** @var Member $member */

        return $this->render(
            'member_area/index.html.twig',
            [
                'upcomingSessions' => $this->application->listUpcomingSessions($member->memberId()),
                'memberTimeZone' => $member->timeZone(),
                'updateTimeZoneForm' => $this->createUpdateTimeZoneForm($member)->createView()
            ]
        );
    }

    /**
     * @Route("/update-time-zone", name="update_time_zone", methods={"POST"})
     */
    public function updateTimeZoneAction(UserInterface $member, Request $request): Response
    {
        Assert::that($member)->isInstanceOf(Member::class);
        /** @var Member $member */

        $form = $this->createUpdateTimeZoneForm($member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $this->application->updateTimeZone(
                new UpdateTimeZone($member->getUsername(), $formData['timeZone'])
            );

            return $this->redirectToRoute('member_area_index');
        }

        // The user shouldn't need any assistance, so we don't render the form again to show form errors

        return $this->redirectToRoute('member_area_index');
    }

    /**
     * @Route("/attend-session", name="attend_session", methods={"POST"})
     */
    public function attendSessionAction(Request $request, UserInterface $user): Response
    {
        $this->application->attendSession(
            new AttendSession($request->request->get('sessionId'), $user->getUsername())
        );

        return $this->redirectToRoute('member_area_index');
    }

    /**
     * @Route("/cancel-attendance", name="cancel_attendance", methods={"POST"})
     */
    public function cancelAttendanceAction(Request $request, UserInterface $user): Response
    {
        $this->application->cancelAttendance(
            new CancelAttendance($request->request->get('sessionId'), $user->getUsername())
        );

        return $this->redirectToRoute('member_area_index');
    }

    private function createUpdateTimeZoneForm(Member $member): FormInterface
    {
        return $this->createForm(
            UpdateTimeZoneForm::class,
            [
                'timeZone' => $member->timeZone()
            ]
        );
    }
}
