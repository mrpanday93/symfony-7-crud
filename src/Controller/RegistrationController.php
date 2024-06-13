<?php
  
namespace App\Controller;
  
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register', methods: 'post')]
    public function index(ManagerRegistry $doctrine, Request $request,ValidatorInterface $validator , UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): JsonResponse
    {
        $em = $doctrine->getManager();
        $decoded = json_decode($request->getContent());
        $email = $decoded->email;
        $username = $decoded->username;
        $plaintextPassword = $decoded->password;

        if (!$username || !$email || !$plaintextPassword) {
            return $this->json(['code' => 409, 'status' => 'error', 'message' => 'Bad Api call. All parameters required.'], JsonResponse::HTTP_BAD_REQUEST);
        }
 
        $user = $userRepository->findExistingUser($username, $email);

        if (count($user) > 0) {
            return $this->json(['code' => 409, 'status' => 'error', 'message' => 'User already exists'], JsonResponse::HTTP_CONFLICT);
        }
    
        $user = new User();

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setUsername($username);
        
        $em->persist($user);
        $em->flush();
    
        return $this->json(['code' => 200, 'status' => 'success', 'message' => 'Registered Successfully'], JsonResponse::HTTP_OK);
    }
}