<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(): View
    {
        $authors = Author::withCount('books')->latest()->paginate(15);

        return view('admin.authors.index', compact('authors'));
    }

    public function create(): View
    {
        return view('admin.authors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Author::create($this->validatedData($request));

        return redirect()->route('admin.authors.index')->with('success', 'Auteur ajoute avec succes.');
    }

    public function show(Author $author): View
    {
        $author->load('books');

        return view('admin.authors.show', compact('author'));
    }

    public function edit(Author $author): View
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author): RedirectResponse
    {
        $author->update($this->validatedData($request, $author));

        return redirect()->route('admin.authors.index')->with('success', 'Auteur modifie avec succes.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        $author->delete();

        return redirect()->route('admin.authors.index')->with('success', 'Auteur supprime avec succes.');
    }

    private function validatedData(Request $request, ?Author $author = null): array
    {
        $authorId = $author?->id ?? 'NULL';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:authors,slug,'.$authorId],
            'bio' => ['nullable', 'string'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        return $data;
    }
}
