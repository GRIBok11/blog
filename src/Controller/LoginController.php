<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\LoginType;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function login(Request $request, SessionInterface $session): Response
    {
        $user = new User();
        $form = $this->createForm(LoginType::class,$user);
       $form->handleRequest($request);
       $userData = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {          
            $email = $userData->getEmail();
            $userPassword = $userData->getPassword();
        
            $dsn = 'mysql:dbname=app;host=127.0.0.1';
            $dbUser = 'app';
            $dbPassword = 'secret';        
            try {
                $dbn = new \PDO($dsn, $dbUser, $dbPassword);
                $dbn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                           
                $stmt = $dbn->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
            
                if ($user && password_verify($userPassword, $user['password'])) {
                    $session->set('user', ['id' => $user['id'], 'name' => $user['name']]);                   
                    return $this->redirectToRoute('blog_index');
                } else {
                    echo "Неверный логин или пароль.";
                }                      
            } catch (\PDOException $e) {
                echo "Ошибка подключения к базе данных: " . $e->getMessage();
            }
        }

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
            'form' => $form,//->createView(),
        ]);
    }

    

   
}
