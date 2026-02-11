<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::paginate(10);
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:255',
            'author' => 'required|min:3|max:100',
            'summary' => 'required|min:10|max:500',
            'isbn' => 'required|size:13|unique:books,isbn'
        ]);

        $book = Book::create($request->all());

        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $cachedBook = Cache::remember('book-' . $book->id, 60, function () use ($book) {
            return $book;
        });
        return new BookResource($cachedBook);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|min:3|max:255',
            'author' => 'required|min:3|max:100',
            'summary' => 'required|min:10|max:500',
            'isbn' => [
                'required',
                'size:13',
                Rule::unique('books')->ignore($book->id),
            ],
        ]);

        $book->update($request->all());

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->noContent();
    }
}
