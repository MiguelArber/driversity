<?php

namespace App\Controller;

use App\Entity\Position;
use App\Form\PositionType;
use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/position")
 */
class PositionController extends Controller
{
    /**
     * @Route("/", name="position_index", methods="GET")
     */
    public function index(PositionRepository $positionRepository): Response
    {
        return $this->render('position/index.html.twig', ['positions' => $positionRepository->findAll()]);
    }

    /**
     * @Route("/new", name="position_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $position = new Position();
        $form = $this->createForm(PositionType::class, $position);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($position);
            $em->flush();

            return $this->redirectToRoute('position_index');
        }

        return $this->render('position/new.html.twig', [
            'position' => $position,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="position_show", methods="GET")
     */
    public function show(Position $position): Response
    {
        return $this->render('position/show.html.twig', ['position' => $position]);
    }

    /**
     * @Route("/{id}/edit", name="position_edit", methods="GET|POST")
     */
    public function edit(Request $request, Position $position): Response
    {
        $form = $this->createForm(PositionType::class, $position);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('position_edit', ['id' => $position->getId()]);
        }

        return $this->render('position/edit.html.twig', [
            'position' => $position,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="position_delete", methods="DELETE")
     */
    public function delete(Request $request, Position $position): Response
    {
        if ($this->isCsrfTokenValid('delete'.$position->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($position);
            $em->flush();
        }

        return $this->redirectToRoute('position_index');
    }
}
