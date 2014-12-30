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
        //return $this->render('MainLoginBundle:Default:index.html.twig', array('name' => $name));
        //hash('sha512', $pass);

//        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') /* || $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') */) {
//            $url = $this->generateUrl('main_welcome');
//            return $this->redirect($url);
//        }

        return $this->render('MainLoginBundle:Login:index.html.twig', array('name' => 'demo'));
    }

    public function welcomeAction() {
        //return $this->render('MainLoginBundle:Default:index.html.twig', array('name' => $name));
        //hash('sha512', $pass);

        return $this->render('MainLoginBundle:Login:welcome.html.twig', array('name' => 'demo'));
    }

    public function loginCheckAction() {
        $request = $this->getRequest();
        $session = $request->getSession();
        $post = $request->request->all();
        //$App = $this->get('ixpo_configuration')->getApp();

        print_r($post);
        die();
        $Args = array();
        $Args['usuario'] = $post['usuario'];
        $Args['password'] = md5($post['password']);
        //No necesita los apostrofes para poder comparar los textos como cadena
        //ya que en el binding se indica el tipo de dato

        $result = $this->LoginModel->getUser($Args, TRUE);
        if (!$result['status']) {
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        if (count($result['data']) == 0) {
            $lang = $session->get('lang');
            $textosGenerales = $this->Textos->obtenerTextos("", $lang);
            $response = new Response(json_encode(array('status' => FALSE, 'data' => $textosGenerales['lb_incorrect_data'])));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $ROL = ($result['data'][0]['Rol'] != "") ? $result['data'][0]['Rol'] : "USER";
        //Creamos el objeto Profile con los datos presentados por el formulario
        $user = new Profile($result['data'][0]['mail'], $result['data'][0]['password'], $App['salt'], array('ROLE_' . $ROL)); //concatenar el rol
        $user->setData($result['data'][0]);

        //Asignar datos de conexion del evento a consultar en la variable de sesión
//        $con = array("fm_database" => $result['data'][0]['DB'], "fm_user" => $result['data'][0]['Usuario'], "fm_server" => $result['data'][0]["Servidor"], "fm_password" => $result['data'][0]["Password"], "fm_port" => $result['data'][0]["Puerto"], "_id_Edicion" => $result['data'][0]["_id_Edicion"]);
//        $conn = $this->getConn($con);
//        $api = $this->getAPI($con);
        // Creamos el token
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
        $this->container->get('security.context')->setToken($token);

        // Creamos e iniciamos la sesión
        $session->set('_security_main', serialize($token));
//        $session->set('conexion', $conn);
//        $session->set('api', $api);

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
