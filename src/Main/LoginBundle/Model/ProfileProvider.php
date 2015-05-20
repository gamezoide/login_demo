<?php

namespace Main\LoginBundle\Model;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Main\LoginBundle\Model\LoginModel;

class ProfileProvider implements UserProviderInterface {
    private $LoginModel, $container;
    
    public function __construct( ContainerInterface $container = null) {
        $this->container = $container;
        $this->LoginModel = new LoginModel();
    }

    public function loadUserByUsername($username = "none") {
        // make a call to your webservice here
        if ($username == "" || $username == "*") {
            $username = "_none_username";
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }
        
        $request = Request::createFromGlobals();
        $_id_Visitante = $request->get('_port');
        
        $session = $this->container->get('session');
        $session->getFlashBag()->add('TMP_port', $_id_Visitante);
        $session->getFlashBag()->add('TMP_pr', $request->get('_pr'));
        
        $Args = Array('Email' => "'" . $username . "'");
        $userData = $this->LoginModel->getVisitante($Args, TRUE);
        $visitor = array();
        $email = $request->get('_username');
        $pass = sha1($request->get('_password') . '*;7/SjqjVjIsI*');
        
        if (COUNT($userData) > 1) {
            if ($pass != "") {
                foreach ($userData as $key => $value) {
                    if ($value['Email'] == $email && $value['Password'] == $pass && $value['_id_Visitante'] == $_id_Visitante) {
                        $visitor = $value;
                        break;
                    }
                }
                
            } else {
                $email = $_SESSION['_sf2_attributes']['MM_Email'];
                $nombre = $_SESSION['_sf2_attributes']['MM_Nombre'];
                $apellidoPaterno = $_SESSION['_sf2_attributes']['MM_ApellidoPaterno'];

                foreach ($userData as $key => $value) {
                    if ($value['Email'] == $email && $value['Nombre'] == $nombre && $value['ApellidoPaterno'] == $apellidoPaterno && $value['_id_Visitante'] == $_id_Visitante) {
                        $visitor = $value;
                        break;
                    }
                }
            }
        } else {
            $visitor = $userData[0];
        }
        // pretend it returns an array on success, false if there is no user
        if ($visitor) {
            $username = $visitor['Email'];
            $password = $visitor['Password'];
            $salt = '*;7/SjqjVjIsI*';
            $roles = array('ROLE_VISITANTE');
            $user = new Profile($username, $password, $salt, $roles);
            $user->setData($visitor);
            return $user;
        }
        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof Profile) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        
        return $user;
    }

    public function supportsClass($class) {
        return $class === 'Login\LoginBundle\Model\Profile';
    }

    private function isEmail($email) {
        if (!ereg("^([a-zA-Z0-9._]+)@([a-zA-Z0-9.-]+).([a-zA-Z]{2,4})$", $email))
            return FALSE;
        return TRUE;
    }

}