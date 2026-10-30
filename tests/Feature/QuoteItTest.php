<?php

use App\Models\Era;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/** Create a quote written by $author (a new user by default). */
function verse(?User $author = null, string $content = 'A verse', array $attributes = []): Quote
{
    return Quote::factory()->for($author ?? User::factory())->create(['content' => $content] + $attributes);
}

function eraId(string $slug = 'abbasid'): int
{
    return Era::where('slug', $slug)->value('id');
}

/*
|--------------------------------------------------------------------------
| FR-01: viewing quotes
|--------------------------------------------------------------------------
*/

it('shows all quotes, not only the latest few, to a guest', function () {
    $author = User::factory()->create();
    foreach (range(1, 30) as $number) {
        verse($author, "Quote number {$number}");
    }

    $response = $this->get('/')->assertOk();

    foreach (range(1, 30) as $number) {
        $response->assertSee("Quote number {$number}");
    }
});

it('shows a guest the quote text, author name and date, without edit or delete', function () {
    $author = User::factory()->create(['name' => 'Lujain']);
    $quote = verse($author, 'Simplicity is the soul of efficiency.');

    $this->get('/')
        ->assertOk()
        ->assertSee('Simplicity is the soul of efficiency.')
        ->assertSee('Lujain')
        ->assertSee($quote->created_at->format('Y-m-d'))
        ->assertSee('سجّل الدخول لمشاركة بيت')
        ->assertDontSee('aria-label="Edit"', false)
        ->assertDontSee('aria-label="Delete"', false);
});

/*
|--------------------------------------------------------------------------
| FR-02 / FR-03 / FR-04: sign up, log in, log out
|--------------------------------------------------------------------------
*/

it('signs a guest up, stores a hashed password and logs them in', function () {
    $this->post('/signup', [
        'name' => 'Sara Ahmed',
        'email' => 'sara@example.com',
        'password' => 'password123',
    ])->assertRedirect(route('home'));

    $user = User::where('email', 'sara@example.com')->firstOrFail();

    expect($user->password)->not->toBe('password123')
        ->and(Hash::check('password123', $user->password))->toBeTrue();
    $this->assertAuthenticatedAs($user);
});

it('accepts an Arabic name when signing up', function () {
    $this->post('/signup', [
        'name' => 'لجين',
        'email' => 'lujain@example.com',
        'password' => 'password123',
    ])->assertRedirect(route('home'));

    expect(User::where('email', 'lujain@example.com')->exists())->toBeTrue();
});

it('rejects invalid sign up data', function (array $data, string $field) {
    $valid = ['name' => 'Sara', 'email' => 'sara@example.com', 'password' => 'password123'];

    $this->from('/signup')
        ->post('/signup', array_merge($valid, $data))
        ->assertRedirect('/signup')
        ->assertSessionHasErrors($field);

    expect(User::count())->toBe(0);
})->with([
    'name with numbers' => [['name' => 'Sara123'], 'name'],
    'name with symbols' => [['name' => 'Sara!'], 'name'],
    'name too short' => [['name' => 'S'], 'name'],
    'name too long' => [['name' => str_repeat('a', 51)], 'name'],
    'invalid email' => [['email' => 'not-an-email'], 'email'],
    'short password' => [['password' => '1234567'], 'password'],
]);

it('rejects an email that is already registered', function () {
    User::factory()->create(['email' => 'sara@example.com']);

    $this->from('/signup')
        ->post('/signup', ['name' => 'Sara', 'email' => 'sara@example.com', 'password' => 'password123'])
        ->assertRedirect('/signup')
        ->assertSessionHasErrors(['email' => 'هذا البريد مسجّل من قبل.']);
});

it('logs a registered user in and shows their name', function () {
    $user = User::factory()->create(['name' => 'Sara', 'password' => 'password123']);

    $this->post('/login', ['email' => $user->email, 'password' => 'password123'])
        ->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($user);

    $this->get('/')->assertSee('Sara');
});

it('keeps the user on the log in page when the details are wrong', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $this->from('/login')
        ->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
        ->assertRedirect('/login')
        ->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('shows an error message on the log in page when the details are wrong', function () {
    $user = User::factory()->create(['password' => 'password123']);

    $this->from('/login')
        ->followingRedirects()
        ->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
        ->assertSee('البيانات المدخلة غير صحيحة.');
});

it('logs the user out', function () {
    $this->actingAs(User::factory()->create())
        ->post('/logout')
        ->assertRedirect(route('home'));

    $this->assertGuest();
});

it('keeps logged-in users away from the sign up and log in pages', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/login')->assertRedirect();
    $this->get('/signup')->assertRedirect();
});

/*
|--------------------------------------------------------------------------
| FR-05: share a quote
|--------------------------------------------------------------------------
*/

it('lets a logged-in user share a quote that then appears in the list', function () {
    $user = User::factory()->create(['name' => 'Sara']);

    $this->actingAs($user)
        ->post('/quotes', ['content' => 'Stay curious.', 'era_id' => eraId()])
        ->assertRedirect();

    $this->assertDatabaseHas('quotes', ['user_id' => $user->id, 'content' => 'Stay curious.']);
    $this->get('/')->assertSee('Stay curious.')->assertSee(now()->format('Y-m-d'));
});

it('accepts quotes of exactly 3 and 155 characters', function (int $length) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/quotes', ['content' => str_repeat('a', $length), 'era_id' => eraId()])
        ->assertSessionHasNoErrors();

    expect($user->quotes()->count())->toBe(1);
})->with([3, 155]);

it('rejects quotes shorter than 3 or longer than 155 characters', function (int $length) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/quotes', ['content' => str_repeat('a', $length), 'era_id' => eraId()])
        ->assertSessionHasErrors('content');

    expect(Quote::count())->toBe(0);
})->with([2, 156]);

it('sends a guest who tries to share a quote to the log in page', function () {
    $this->post('/quotes', ['content' => 'Stay curious.'])->assertRedirect(route('login'));

    expect(Quote::count())->toBe(0);
});

/*
|--------------------------------------------------------------------------
| FR-06 / FR-07: edit and delete only my own quotes
|--------------------------------------------------------------------------
*/

it('lets a user edit their own quote, poet and era', function () {
    $user = User::factory()->create();
    $quote = verse($user, 'Old text', ['poet' => 'Old poet', 'era_id' => eraId('abbasid')]);

    $this->actingAs($user)
        ->put("/quotes/{$quote->id}", ['content' => 'New text', 'poet' => 'New poet', 'era_id' => eraId('modern')])
        ->assertRedirectContains("#quote-{$quote->id}");

    expect($quote->fresh())
        ->content->toBe('New text')
        ->poet->toBe('New poet')
        ->era_id->toBe(eraId('modern'));
});

it('shows the edit box only for my own quote', function () {
    $user = User::factory()->create();
    $mine = verse($user, 'My quote');
    $theirs = verse(null, 'Their quote');

    $this->actingAs($user)
        ->get("/?edit={$mine->id}")
        ->assertSee('name="_method" value="PUT"', false);

    $this->actingAs($user)
        ->get("/?edit={$theirs->id}")
        ->assertDontSee('name="_method" value="PUT"', false);
});

it('does not let a user edit someone else\'s quote', function () {
    $quote = verse(null, 'Original');

    $this->actingAs(User::factory()->create())
        ->put("/quotes/{$quote->id}", ['content' => 'Hacked', 'era_id' => eraId()])
        ->assertForbidden();

    expect($quote->fresh()->content)->toBe('Original');
});

it('lets a user delete their own quote immediately', function () {
    $user = User::factory()->create();
    $quote = verse($user, 'Bye');

    $this->actingAs($user)->delete("/quotes/{$quote->id}")->assertRedirect();

    $this->assertModelMissing($quote);
});

it('does not let a user delete someone else\'s quote', function () {
    $quote = verse(null, 'Mine!');

    $this->actingAs(User::factory()->create())
        ->delete("/quotes/{$quote->id}")
        ->assertForbidden();

    $this->assertModelExists($quote);
});

it('does not let a guest edit or delete quotes', function () {
    $quote = verse(null, 'Original');

    $this->put("/quotes/{$quote->id}", ['content' => 'Hacked'])->assertRedirect(route('login'));
    $this->delete("/quotes/{$quote->id}")->assertRedirect(route('login'));

    $this->assertModelExists($quote);
});

/*
|--------------------------------------------------------------------------
| New feature: likes
|--------------------------------------------------------------------------
*/

it('lets a logged-in user like a quote', function () {
    $user = User::factory()->create();
    $quote = verse(null, 'Worth a like');

    $this->actingAs($user)
        ->post("/quotes/{$quote->id}/like")
        ->assertRedirect();

    $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'quote_id' => $quote->id]);
});

it('shows the like count to guests without a like button', function () {
    $quote = verse(null, 'Worth a like');
    User::factory()->count(2)->create()->each(fn (User $fan) => $fan->likedQuotes()->attach($quote));

    $this->get('/')
        ->assertSee('title="سجّل الدخول للإعجاب"', false)
        ->assertDontSee('aria-pressed', false)
        ->assertSeeInOrder(['سجّل الدخول للإعجاب', '>2</span>'], false);
});

it('removes the like when a user likes the same quote again', function () {
    $user = User::factory()->create();
    $quote = verse(null, 'Toggle me');

    $this->actingAs($user)->post("/quotes/{$quote->id}/like");
    $this->actingAs($user)->post("/quotes/{$quote->id}/like");

    expect($quote->likedBy()->count())->toBe(0);
});

it('counts one like per user', function () {
    $quote = verse(null, 'Popular');
    $fans = User::factory()->count(3)->create();

    foreach ($fans as $fan) {
        $this->actingAs($fan)->post("/quotes/{$quote->id}/like");
    }

    expect($quote->likedBy()->count())->toBe(3);
});

it('shows a filled heart only on quotes the current user liked', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $liked = verse($author, 'Liked one');
    verse($author, 'Not liked');
    $user->likedQuotes()->attach($liked);

    $html = $this->actingAs($user)->get('/')->getContent();

    // The liked quote is also the quote of the day, so its heart appears twice (box + card).
    expect(substr_count($html, 'aria-pressed="true"'))->toBe(2)
        ->and(substr_count($html, 'aria-pressed="false"'))->toBe(1);
});

it('sends a guest who tries to like a quote to the log in page', function () {
    $quote = verse(null, 'Nope');

    $this->post("/quotes/{$quote->id}/like")->assertRedirect(route('login'));

    expect($quote->likedBy()->count())->toBe(0);
});

it('returns the user to the liked quote after liking it', function () {
    $quote = verse(null, 'Anchor');

    $this->actingAs(User::factory()->create())
        ->from('/')
        ->post("/quotes/{$quote->id}/like")
        ->assertRedirectContains("#quote-{$quote->id}");
});

it('removes the likes of a quote when the quote is deleted', function () {
    $author = User::factory()->create();
    $quote = verse($author, 'Short lived');
    User::factory()->create()->likedQuotes()->attach($quote);

    $this->actingAs($author)->delete("/quotes/{$quote->id}");

    $this->assertDatabaseCount('likes', 0);
});

/*
|--------------------------------------------------------------------------
| New feature: quote of the day
|--------------------------------------------------------------------------
*/

it('hides the quote of the day section when there are no quotes', function () {
    $this->get('/')->assertOk()->assertDontSee('بيت اليوم');
});

it('picks the most liked quote as the quote of the day', function () {
    $author = User::factory()->create();
    $quiet = verse($author, 'Barely noticed');
    $favourite = verse($author, 'Everybody loves this');
    $fans = User::factory()->count(2)->create();

    $fans->each(fn (User $fan) => $fan->likedQuotes()->attach($favourite));
    $fans->first()->likedQuotes()->attach($quiet);

    $this->get('/')
        ->assertSee('بيت اليوم')
        ->assertSeeInOrder(['بيت اليوم', 'Everybody loves this', 'كل الأبيات'])
        ->assertViewHas('quoteOfTheDay', fn (Quote $quote) => $quote->is($favourite));
});

it('still shows a quote of the day when nothing has been liked yet', function () {
    verse(null, 'Only one');

    $this->get('/')
        ->assertSee('بيت اليوم')
        ->assertViewHas('quoteOfTheDay', fn (Quote $quote) => $quote->content === 'Only one');
});

it('keeps the same quote of the day for the whole day when quotes are tied', function () {
    $author = User::factory()->create();
    foreach (range(1, 5) as $number) {
        verse($author, "Tied quote {$number}");
    }

    Carbon::setTestNow('2026-10-23 08:00:00');
    $morning = $this->get('/')->viewData('quoteOfTheDay')->id;

    Carbon::setTestNow('2026-10-23 22:30:00');
    $evening = $this->get('/')->viewData('quoteOfTheDay')->id;

    expect($evening)->toBe($morning);
});

it('can pick a different tied quote on a different day', function () {
    $author = User::factory()->create();
    foreach (range(1, 5) as $number) {
        verse($author, "Tied quote {$number}");
    }

    $picked = collect(range(1, 10))->map(function (int $day) {
        Carbon::setTestNow(Carbon::create(2026, 11, $day, 12));

        return $this->get('/')->viewData('quoteOfTheDay')->id;
    });

    expect($picked->unique()->count())->toBeGreaterThan(1);
});

it('keeps the quote of the day in the normal list too', function () {
    $author = User::factory()->create();
    $favourite = verse($author, 'Everybody loves this');
    User::factory()->create()->likedQuotes()->attach($favourite);

    expect(substr_count($this->get('/')->getContent(), 'Everybody loves this'))->toBe(2);
});

/*
|--------------------------------------------------------------------------
| New feature: poetic quotes with a poet and an era
|--------------------------------------------------------------------------
*/

it('stores the poet and the era chosen when sharing', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/quotes', ['content' => 'على قدر أهل العزم تأتي العزائم', 'poet' => 'المتنبي', 'era_id' => eraId('abbasid')])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('quotes', [
        'user_id' => $user->id,
        'poet' => 'المتنبي',
        'era_id' => eraId('abbasid'),
    ]);
});

it('shares a quote without a poet and shows "شاعر مجهول"', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/quotes', ['content' => 'A verse without a poet', 'era_id' => eraId()])
        ->assertSessionHasNoErrors();

    $this->get('/')->assertSee('شاعر مجهول');
});

it('requires an era when sharing a quote', function () {
    $this->actingAs(User::factory()->create())
        ->post('/quotes', ['content' => 'No era chosen'])
        ->assertSessionHasErrors(['era_id' => 'اختر عصر البيت.']);

    expect(Quote::count())->toBe(0);
});

it('rejects an era that does not exist', function () {
    $this->actingAs(User::factory()->create())
        ->post('/quotes', ['content' => 'Wrong era', 'era_id' => 9999])
        ->assertSessionHasErrors('era_id');
});

it('rejects a poet name longer than 60 characters', function () {
    $this->actingAs(User::factory()->create())
        ->post('/quotes', ['content' => 'A verse', 'poet' => str_repeat('a', 61), 'era_id' => eraId()])
        ->assertSessionHasErrors('poet');
});

it('has the seven eras, in order', function () {
    expect(Era::orderBy('id')->pluck('slug')->all())->toBe([
        'pre-islamic', 'early-islamic', 'umayyad', 'abbasid', 'andalusian', 'mamluk-ottoman', 'modern',
    ]);
});

it('shows the era and the poet on each quote', function () {
    verse(null, 'Some verse', ['poet' => 'Ibn Zaydun', 'era_id' => eraId('andalusian')]);

    $this->get('/')
        ->assertSee('Ibn Zaydun')
        ->assertSee('العصر الأندلسي');
});

/*
|--------------------------------------------------------------------------
| New feature: sections (browse by era)
|--------------------------------------------------------------------------
*/

it('shows only the quotes of the chosen era', function () {
    verse(null, 'Abbasid verse', ['era_id' => eraId('abbasid')]);
    verse(null, 'Modern verse', ['era_id' => eraId('modern')]);

    $this->get('/?era=abbasid')
        ->assertOk()
        ->assertSee('Abbasid verse')
        ->assertDontSee('Modern verse');
});

it('shows every quote on the home page without a filter', function () {
    verse(null, 'Abbasid verse', ['era_id' => eraId('abbasid')]);
    verse(null, 'Modern verse', ['era_id' => eraId('modern')]);

    $this->get('/')->assertSee('Abbasid verse')->assertSee('Modern verse');
});

it('returns 404 for an unknown era', function () {
    $this->get('/?era=stone-age')->assertNotFound();
});

it('shows how many quotes each era has', function () {
    verse(null, 'One', ['era_id' => eraId('modern')]);
    verse(null, 'Two', ['era_id' => eraId('modern')]);
    verse(null, 'Three', ['era_id' => eraId('umayyad')]);

    $this->get('/')
        ->assertViewHas('totalQuotes', 3)
        ->assertViewHas('eras', fn ($eras) => $eras->firstWhere('slug', 'modern')->quotes_count === 2
            && $eras->firstWhere('slug', 'umayyad')->quotes_count === 1
            && $eras->firstWhere('slug', 'abbasid')->quotes_count === 0);
});

it('shows the quote of the day on the home page only, not inside an era section', function () {
    verse(null, 'Abbasid verse', ['era_id' => eraId('abbasid')]);

    $this->get('/')->assertSee('بيت اليوم');
    $this->get('/?era=abbasid')->assertDontSee('بيت اليوم');
});

it('keeps the quote of the day global even if its era is not the one being browsed', function () {
    $favourite = verse(null, 'Loved verse', ['era_id' => eraId('modern')]);
    User::factory()->create()->likedQuotes()->attach($favourite);

    $this->get('/')->assertViewHas('quoteOfTheDay', fn (Quote $quote) => $quote->is($favourite));
});

it('keeps the era filter when editing', function () {
    $user = User::factory()->create();
    $quote = verse($user, 'Mine', ['era_id' => eraId('modern')]);

    $this->actingAs($user)
        ->get('/?era=modern')
        ->assertSee('era=modern&amp;edit='.$quote->id, false);
});

/*
|--------------------------------------------------------------------------
| Arabic interface, guests and the quote of the day
|--------------------------------------------------------------------------
*/

it('is an Arabic page displayed right to left', function () {
    $this->get('/')->assertSee('<html lang="ar" dir="rtl">', false);
});

it('sends a guest who presses the heart to the log in page', function () {
    verse(null, 'Some verse');

    $this->get('/')->assertSee('href="'.route('login').'" title="سجّل الدخول للإعجاب"', false);
});

it('lets a logged-in user like the quote of the day from its box', function () {
    $user = User::factory()->create();
    $quote = verse(null, 'Verse of the day');

    $html = $this->actingAs($user)->get('/')->getContent();
    $box = substr($html, strpos($html, 'id="quote-of-the-day"'), 2500);

    expect($box)->toContain('action="'.route('quotes.like', $quote).'"')
        ->and($box)->toContain('name="anchor" value="quote-of-the-day"');

    $this->actingAs($user)
        ->from('/')
        ->post("/quotes/{$quote->id}/like", ['anchor' => 'quote-of-the-day'])
        ->assertRedirectContains('#quote-of-the-day');

    expect($quote->likedBy()->count())->toBe(1);
});

it('ignores an unknown anchor when liking', function () {
    $quote = verse(null, 'Anchor');

    $this->actingAs(User::factory()->create())
        ->from('/')
        ->post("/quotes/{$quote->id}/like", ['anchor' => 'something-else'])
        ->assertRedirectContains("#quote-{$quote->id}");
});

it('builds https links when the site is opened through an https proxy or tunnel', function () {
    $this->withHeader('X-Forwarded-Proto', 'https')
        ->get('/')
        ->assertSee('href="https://localhost', false)
        ->assertDontSee('href="http://localhost', false);
});
