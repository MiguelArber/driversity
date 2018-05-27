<?php

namespace App\Controller;

use App\Entity\Match;
use App\Form\MatchType;
use App\Repository\MatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/match")
 */
class MatchController extends Controller
{
    /**
     * @Route("/", name="match_index", methods="GET")
     */
    public function index(MatchRepository $matchRepository): Response
    {
        return $this->render('match/index.html.twig', ['matches' => $matchRepository->findAll()]);
    }

    /**
     * @Route("/new", name="match_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $match = new Match();
        $form = $this->createForm(MatchType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($match);
            $em->flush();

            return $this->redirectToRoute('match_index');
        }

        return $this->render('match/new.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="match_show", methods="GET")
     */
    public function show(Match $match): Response
    {
        return $this->render('match/show.html.twig', ['match' => $match]);
    }

    /**
     * @Route("/{id}/edit", name="match_edit", methods="GET|POST")
     */
    public function edit(Request $request, Match $match): Response
    {
        $form = $this->createForm(MatchType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('match_edit', ['id' => $match->getId()]);
        }

        return $this->render('match/edit.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="match_delete", methods="DELETE")
     */
    public function delete(Request $request, Match $match): Response
    {
        if ($this->isCsrfTokenValid('delete'.$match->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($match);
            $em->flush();
        }

        return $this->redirectToRoute('match_index');
    }
}
