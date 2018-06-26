<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Schedule;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Form\UserType;
use App\Form\VehicleType;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
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
        foreach ($allUsers as $otherUser) {
            if ($user->getId() != $otherUser->getId()) { //Checking that user is not the same
                if (($user->getVehicle() == null && $otherUser->getVehicle() != null) || ($user->getVehicle() != null && $otherUser->getVehicle() == null)) { //Checking that at least one user has a car
                    if ($user->getCampus() == $otherUser->getCampus()) { //Checking that both users go to the same campus
                        if ($this->isScheduleCompatible($user, $otherUser)) { //Checking that the schedules are compatible
                            if ($this->getDistance($user->getOrigin(), $otherUser->getOrigin()) <= $user->getLocationFlex() && $this->getDistance($user->getOrigin(), $otherUser->getOrigin()) <= $otherUser->getLocationFlex()) { //Checking that the distanceFlex is OK
                                array_push($compatibleUsers, $otherUser);
                            }
                        }
                    }
                }
            }
        }

        return new JsonResponse($compatibleUsers);
    }

    private function isScheduleCompatible(User $user1, User $user2)
    {
        $compatible = false;

        foreach ($user1->getSchedule() as $user1Schedule) {
            foreach ($user2->getSchedule() as $user2Schedule) {
                if ($user1Schedule->getDay() == $user2Schedule->getDay()) { //Checking if the days are the same
                    if ($user1->getTimeFlex() > $user2->getTimeFlex()) { //Checking if the timeFlex is compatible
                        if ($user1Schedule->getTime() - $user2Schedule->getTime() <= $user2->getTimeFlex()) {
                            $compatible = true;
                            break 2;
                        }
                    } else {
                        if ($user2Schedule->getTime() - $user1Schedule->getTime() <= $user1->getTimeFlex()) {
                            $compatible = true;
                            break 2;
                        }
                    }
                }
            }
        }

        return $compatible;
    }

    private function getDistance(Location $location1, Location $location2): float
    {
        $R = 6378137; // Earths radius in meters (mean)

        $dLat = $this->getRad($location2->getLat() - $location1->getLat());
        $dLong = $this->getRad($location2->getLon() - $location1->getLon());

        //Harvesine algorithm to calculate distance between two coordinates

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($this->getRad($location1->getLat())) * cos($this->getRad($location2->getLat())) *
            sin($dLong / 2) * sin($dLong / 2);
        $c = 2 * asin(sqrt($a));
        $d = $R * $c;

        //dump('distance: '.$d);

        return $d; // Distance returned in meters
    }

    private function getRad(float $point): float
    {
        return $point * M_PI / 180;
    }

    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {


        //First we load the entity manager
        $em = $this->getDoctrine()->getManager();



        //If a vehicle is defined, creates a new vehicle object and saves it in the DB
        $vehicle = new Vehicle();

        empty($request->get('type'))?: $vehicle->setType($request->get('type'));
        empty($request->get('model'))?: $vehicle->setModel($request->get('model'));
        empty($request->get('seats'))?: $vehicle->setSeats($request->get('seats'));
        empty($request->get('price'))?: $vehicle->setPrice($request->get('price'));

        empty($request->get('price'))?: $em->persist($vehicle);
        $em->flush();
        //If an origin is defined, creates an origin object and saves it in the DB
        $origin = new Location();

        empty($request->get('lat'))?: $origin->setLat($request->get('lat'));
        empty($request->get('lon'))?: $origin->setLon($request->get('lon'));
        empty($request->get('locationName'))?: $origin->setLocationName($request->get('locationName'));
        empty($request->get('lat'))?: $origin->setIsCampus(false);

        empty($request->get('type'))?: $em->persist($origin);
        $em->flush();

        //Finds the selected campus in the DB
        $campus = $em->getRepository(Location::class)->find($request->get('campus'));

        //Creates a new user with all its data (with the car and the locations).
        $user = new User();

        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));
        $user->setPassword($request->get('password'));
        $user->setTimeFlex($request->get('timeFlex'));
        $user->setLocationFlex($request->get('locationFlex'));
        $user->setCampus($campus);
        $user->setOrigin($em->getRepository(Location::class)->find($origin));
        $user->setVehicle($em->getRepository(Vehicle::class)->find($vehicle));

        $em->persist($user);
        $em->flush();

        // $response = new JsonResponse();
        // $response->headers->set('Access-Control-Allow-Origin', '*');
        // return $response;

        return $this->show($user);
    }

    /**
     * @Route("/{id}", name="user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setData(array(
            'user' => $user
        ));
        return $response;
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
