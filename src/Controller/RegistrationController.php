<?php
// src/Controller/RegistrationController.php
namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Exception;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Получаем данные формы
            $userData = $form->getData();
            $dsn = 'mysql:dbname=app;host=127.0.0.1';
            $dbUser = 'app';
            $dbPassword = 'secret';
            try {
                $dbn = new \PDO($dsn, $dbUser, $dbPassword);
                $dbn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                $name = $userData->getName();
                $email = $userData->getEmail();
                $hashed_password = password_hash($userData->getPassword(), PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                $stmt = $dbn->prepare($sql);
                $stmt->execute([$name, $email, $hashed_password]);

              
                return $this->redirectToRoute('login');
            } catch (\PDOException $e) {
               
                $this->addFlash('error', 'Произошла ошибка при регистрации: ' . $e->getMessage());
            }
        }

       
        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form , //->createView(),
        ]);
    }
}
