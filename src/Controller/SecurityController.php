<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use App\Entity\User;

class SecurityController extends Controller
{
    use \App\Traits\ControllerHelper;
    /**
     * @Route("/login", name="login")
     * @Method("POST")
     */
    public function login(Request $request)
    {
        // $userName = $request->getUser();
        $userName = $request->request->get('username');
        $password = $request->request->get('password');
        // $password = $request->getPassword();
        // return $userName;
        $user = $this->getDoctrine()
            ->getRepository(\App\Entity\User::class)
            ->findOneBy(['username' => $userName,'password' => $password]);


        if (!$user) {
            throw $this->createNotFoundException();
        }

    
        $response = new Response(Response::HTTP_OK);

        return $this->setBaseHeaders($response);
    }
}
