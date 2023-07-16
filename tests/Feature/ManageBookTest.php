<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageBookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_book_list_in_book_index_page()
    {
        $this->loginAsUser();
        $book = factory(Book::class)->create();

        $this->visitRoute('books.index');
        $this->see($book->name);
    }

    /** @test */
    public function user_can_create_a_book()
    {
        $this->loginAsUser();
        $this->visitRoute('books.index');

        $this->click(__('book.create'));
        $this->seeRouteIs('books.index', ['action' => 'create']);

        $this->submitForm(__('book.create'), [
            'name' => 'Book 1 name',
            'description' => 'Book 1 description',
        ]);

        $this->seeRouteIs('books.index');

        $this->seeInDatabase('books', [
            'name' => 'Book 1 name',
            'description' => 'Book 1 description',
            'status_id' => Book::STATUS_ACTIVE,
        ]);
    }

    /** @test */
    public function user_can_edit_a_book()
    {
        $this->loginAsUser();
        $book = factory(Book::class)->create(['name' => 'Testing 123']);

        $this->visitRoute('books.index');
        $this->click('edit-book-'.$book->id);
        $this->seeRouteIs('books.index', ['action' => 'edit', 'id' => $book->id]);

        $this->submitForm(__('book.update'), [
            'name' => 'Book 1 name',
            'description' => 'Book 1 description',
            'status_id' => Book::STATUS_ACTIVE,
        ]);

        $this->seeRouteIs('books.index');

        $this->seeInDatabase('books', [
            'name' => 'Book 1 name',
            'description' => 'Book 1 description',
            'status_id' => Book::STATUS_ACTIVE,
        ]);
    }

    /** @test */
    public function user_can_delete_a_book()
    {
        $user = $this->loginAsUser();
        $book = factory(Book::class)->create();
        factory(Book::class)->create();

        $this->visitRoute('books.index', ['action' => 'edit', 'id' => $book->id]);
        $this->click('del-book-'.$book->id);
        $this->seeRouteIs('books.index', ['action' => 'delete', 'id' => $book->id]);

        $this->seeInDatabase('books', [
            'id' => $book->id,
        ]);

        $this->press(__('app.delete_confirm_button'));

        $this->dontSeeInDatabase('books', [
            'id' => $book->id,
        ]);
    }
}
