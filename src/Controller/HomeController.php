<?php
// src/Controller/BlogController.php
namespace App\Controller;


use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HomeController extends AbstractController

{
    #[Route('/', name: 'blog_index')]
    public function index(SessionInterface $session,int $page = 1): Response
    {
        $dsn = 'mysql:dbname=app;host=127.0.0.1';
        $dbUser = 'app';
        $dbPassword = 'secret';
        $dbn = new \PDO($dsn, $dbUser, $dbPassword);
        $dbn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
        $stmt = $dbn->prepare("SELECT * FROM post ORDER BY published_at DESC LIMIT :offset, :limit");
        $stmt->bindValue(':offset', 15 * ($page - 1), \PDO::PARAM_INT);
        $stmt->bindValue(':limit', 15, \PDO::PARAM_INT);
        $stmt->execute();
    
        $posts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
       
        $totalPostsStmt = $dbn->query("SELECT COUNT(*) FROM post");
        $totalPosts = $totalPostsStmt->fetchColumn();
        $user = $session->get('user');
        
$userName = $user ? $user['name'] : 'Гость';

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => ceil($totalPosts / 15),
            'user' => $userName,
        ]);
    }

    #[Route('/post/new', name: 'post_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if ($user) {
            $form = $this->createForm(PostType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $post = $form->getData();

            $dsn = 'mysql:dbname=app;host=127.0.0.1';
            $dbUser = 'app';
            $dbPassword = 'secret';
            $dbn = new \PDO($dsn, $dbUser, $dbPassword);
            $dbn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            /** @var UploadedFile $file */
            $file = $post['photo'];

            if ($file) {
                $uploadsDirectory = $this->getParameter('uploads_directory');
                $filename = md5(uniqid()) . '.' . $file->guessExtension();

                $file->move(
                    $uploadsDirectory,
                    $filename
                );

               
                $post['photo'] = $filename;

                $stmt = $dbn->prepare("INSERT INTO post (username, title, content, photo, published_at) VALUES (?, ?, ?, ?, ?)");
                if (isset($post['published_at']) && $post['published_at'] instanceof \DateTime) {
                    $formattedDate = $post['published_at']->format('Y-m-d H:i:s');
                } else {
                    $formattedDate = (new \DateTime())->format('Y-m-d H:i:s');
                }
                $stmt->execute([
                    $user['name'],
                    $post['title'],
                    $post['content'],
                    $post['photo'],
                    $formattedDate,
                ]);

                // Uncomment the following lines if needed:
                // $entityManager->persist($post);
                // $entityManager->flush();

                return $this->redirectToRoute('blog_index');
            }
        }

        return $this->render('home/new.html.twig', [
            'postForm' => $form->createView(),
        ]);
    } else {
        return $this->redirectToRoute('login');
    }
}
#[Route('/logout', name: 'logout')]
public function logout(SessionInterface $session): Response
{
    // Clear user session data
    $session->remove('user');

    return $this->redirectToRoute('login');
}
}
