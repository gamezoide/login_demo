<?php

namespace Main\LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Main\LoginBundle\Model\LoginModel;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LoginController extends Controller {

    protected $LoginModel;

    public function __construct() {
        $this->LoginModel = new LoginModel();
    }

    public function indexAction() {

        return $this->render('MainLoginBundle:Login:index.html.twig', array('name' => 'demo'));
    }

    public function welcomeAction() {
        return $this->render('MainLoginBundle:Login:welcome.html.twig', array('name' => 'demo'));
    }

    public function loginCheckAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $post = $request->request->all();


        $Args = array();
        $Args['usuario'] = $post['user'];
        $Args['pass'] = hash('sha512', $post['pass']);
        //No necesita los apostrofes para poder comparar los textos como cadena
        //ya que en el binding se indica el tipo de dato

        $result = $this->LoginModel->getUser($Args, TRUE);
        print_r($result);
        die(" resultado");
        if (!$result['status']) {
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (count($result['data']) == 0) {
            $response = new Response(json_encode(array('status' => FALSE, 'data' => 'Datos incorrectos')));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        //Creamos el objeto Profile con los datos presentados por el formulario
        $user = new Profile($result['data'][0]['usuario'], $result['data'][0]['pass'], '', array('ROLE_USER')); //concatenar el rol
        $user->setData($result['data'][0]);

        // Creamos el token
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $this->container->get('security.context')->setToken($token);

        // Creamos e iniciamos la sesión
        $session->set('_security_main', serialize($token));

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
