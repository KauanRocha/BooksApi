<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;
use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;

use function PHPSTORM_META\type;

class BookController extends AbstractController
{
    #[Route('/books', name: 'bookList', methods: ['GET'])]
    public function index(BookRepository $bookRepository): JsonResponse
    {
        #Estudar sobre symfony serializer
        return $this->json([
            'data'=> $bookRepository->findAll(),
        ]);
    }

    #[Route('/books/{book}', name: 'bookSingle', methods: ['GET'])]
    public function single(int $book, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($book);

        if (!$book) throw $this->createNotFoundException();

        #Estudar sobre symfony serializer
        return $this->json([
            'data'=> $book,
        ]);
    }

    #[Route('/books', name: 'bookCreate', methods: ['POST'])]
    public function create(Request $request, BookRepository $bookRepository): JsonResponse
    {

        if ($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        }else{
            $data = $request->request->all();
        }

        #Estudar sobre symfony serializer
        $book = new Book();
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setCreatedAt(new \DateTimeImmutable ('now', new \DateTimeZone('America/Sao_Paulo')));
        $book->setUpdateAt(new \DateTimeImmutable ('now', new \DateTimeZone('America/Sao_Paulo')));

        $bookRepository->save($book, true);
        

        return $this->json([
            'message' => 'Book created successfully!',
            'data' => $book           
        ], 201);
    }

    #[Route('/books/{book}', name: 'bookUpdate', methods: ['PUT', 'PATCH'])]
    public function update(int $book, Request $request, ManagerRegistry $doctrine, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($book);

        if (!$book) throw $this->createNotFoundException();

        if ($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        }else{
            $data = $request->request->all();
        }
        #Estudar sobre symfony serializer
        
        $book->setTitle($data['title']);
        $book->setIsbn($data['isbn']);
        $book->setUpdateAt(new \DateTimeImmutable ('now', new \DateTimeZone('America/Sao_Paulo')));


        $doctrine->getManager()->flush();

        return $this->json([
            'message' => 'Book updated successfully!',
            'data' => $book           
        ], 201);
    }

    #[Route('/books/{book}', name: 'bookDelete', methods: ['DELETE'])]
    public function delete(int $book, Request $request, BookRepository $bookRepository): JsonResponse
    {

        $book = $bookRepository->find($book);

        $bookRepository->remove($book, true);

        return $this->json([
            'data' => $book
        ]);
    }
}
