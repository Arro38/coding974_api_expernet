<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'app_registration',methods: ['POST'])]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;
        $plaintextPassword = $data['password'] ?? null;
        
        if (!$username || !$plaintextPassword) {
            return new JsonResponse(['error' => 'Invalid input'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $existingUser = $em->getRepository(User::class)->findOneBy(['username' => $username]);
            if ($existingUser) {
                return new JsonResponse(['error' => 'Username already taken'], Response::HTTP_CONFLICT);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $user = new User();
        $user->setUsername($username);
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();
        return new JsonResponse(['status' => 'User registered'], Response::HTTP_CREATED);
    }
}
