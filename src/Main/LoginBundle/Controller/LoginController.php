<?php

namespace Main\LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller
{
    public function indexAction()
    {
        //return $this->render('MainLoginBundle:Default:index.html.twig', array('name' => $name));
       
        //hash('sha512', $pass);
        
        return $this->render('MainLoginBundle:Login:index.html.twig', array('name' => 'demo'));
    }
}
