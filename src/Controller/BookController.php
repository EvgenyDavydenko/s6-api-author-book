<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    #[Route('/api/books', name: 'books', methods: ['GET'])]
    public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository->findAll();

        // ['groups'=>'getbooks'] Allows me to return only the data that interests me in the case of joins
        $jsonBookList = $serializer->serialize($bookList, 'json', ["groups" => "getBooks"]);

        return new JsonResponse(
            $jsonBookList,
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/books/{id}', name: 'detailBook', methods: ['GET'])]
    public function getOneBook(BookRepository $bookRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $bookList = $bookRepository->find($id);
        // dd($list);
        if ($bookList) {
            $jsonBookList = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);
            return new JsonResponse(
                $jsonBookList,
                Response::HTTP_OK,
                [],
                true
            );
        } else {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/api/books', name: 'createBook', methods: ['POST'])]
    public function createBook(EntityManagerInterface $entitymanager, AuthorRepository $authorRepository, Request $request, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, ValidatorInterface $validator)
    {
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');
        
        // data validation
        $errors = $validator->validate($book);
        if (count($errors) > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                JsonResponse::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        // to get the author's ID from the request
        $content = $request->toArray();
        // idAuthor is the keyname I send in Postman when making my request to create the book
        $idAuthor = $content['idAuthor'] ?? -1;

        $book->setAuthor($authorRepository->find($idAuthor));

        $entitymanager->persist($book);
        $entitymanager->flush();
        
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        
        // Calculate the URL of the created element to send it in the header.
        $location = $urlGenerator->generate('detailBook', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(
            $jsonBook,
            Response::HTTP_CREATED,
            ['location' => $location],
            true
        );
    }

    #[Route('api/books/{id}', name: 'updateBook', methods: ['PUT'])]
    public function updateBook(Request $request, SerializerInterface $serializer, Book $currentBook, EntityManagerInterface $entitymanager, AuthorRepository $authorRepository)
    {
        $updateBook = $serializer->deserialize($request->getContent(), Book::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]);

        $content = $request->toArray();

        $idAuthor = $content['idAuthor'] ?? -1;

        $updateBook->setAuthor($authorRepository->find($idAuthor));

        $entitymanager->persist($updateBook);
        $entitymanager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/books/{id}', name: 'deleteBook', methods: ['DELETE'])]
    public function deleteBook(Book $book, EntityManagerInterface $entitymanager): JsonResponse
    {
        $entitymanager->remove($book);
        $entitymanager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}