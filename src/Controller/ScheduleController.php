<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Form\ScheduleType;
use App\Repository\ScheduleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/schedule")
 */
class ScheduleController extends Controller
{
    /**
     * @Route("/", name="schedule_index", methods="GET")
     */
    public function index(ScheduleRepository $scheduleRepository): Response
    {
        return $this->render('schedule/index.html.twig', ['schedules' => $scheduleRepository->findAll()]);
    }

    /**
     * @Route("/new", name="schedule_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $schedule = new Schedule();
        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($schedule);
            $em->flush();

            return $this->redirectToRoute('schedule_index');
        }

        return $this->render('schedule/new.html.twig', [
            'schedule' => $schedule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="schedule_show", methods="GET")
     */
    public function show(Schedule $schedule): Response
    {
        return $this->render('schedule/show.html.twig', ['schedule' => $schedule]);
    }

    /**
     * @Route("/{id}/edit", name="schedule_edit", methods="GET|POST")
     */
    public function edit(Request $request, Schedule $schedule): Response
    {
        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('schedule_edit', ['id' => $schedule->getId()]);
        }

        return $this->render('schedule/edit.html.twig', [
            'schedule' => $schedule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="schedule_delete", methods="DELETE")
     */
    public function delete(Request $request, Schedule $schedule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schedule->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($schedule);
            $em->flush();
        }

        return $this->redirectToRoute('schedule_index');
    }
}
