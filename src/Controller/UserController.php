<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_index", methods="GET")
     */
    public function index(UserRepository $userRepository): JsonResponse
    {
        //return $this->render('user/index.html.twig', ['users' => $userRepository->findAll()]);
        return new JsonResponse($userRepository->findAll());
    }


    /**
     * @Route("/{id}/compatible", name="match_compatible", methods="GET")
     */
    public function compatibleUsers(User $user, UserRepository $userRepository): JsonResponse
    {

        $allUsers = $userRepository->findAll();
        $compatibleUsers = array();
        foreach($allUsers as $otherUser) {
            if($user->getId() != $otherUser->getId()) //Checking that user is not the same
            {
                if(($user->getVehicle() == null && $otherUser->getVehicle() != null) || ($user->getVehicle() != null && $otherUser->getVehicle() == null)) //Checking that at least one user has a car
                {
                    if($user->getCampus() == $otherUser->getCampus()) //Checking that both users go to the same campus
                    {
                        if($this->getDistance($user->getOrigin(), $otherUser->getOrigin()) <= $user->getLocationFlex()) //Checking that the distanceFlex is OK
                        {
                            array_push($compatibleUsers, $otherUser);
                        }
                    }
                }
            }
        }

        return new JsonResponse($compatibleUsers);
    }


    private function getDistance(Location $p1, Location $p2): float
    {
        $R = 6378137; // Earths radius in meter (mean)

        $dLat = $this->getRad($p2->getLat() - $p1->getLat());
        $dLong = $this->getRad($p2->getLon() - $p1->getLon());

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($this->getRad($p1->getLat())) * cos($this->getRad($p2->getLat())) *
            sin($dLong / 2) * sin($dLong / 2);
        $c = 2 * asin(sqrt($a));
        $d = $R * $c;
        dump('distance: '.$d);
        return $d; // Distance returned in meters
    }

    private function getRad(float $x): float
    {
        return $x * M_PI / 180;
    }

    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
