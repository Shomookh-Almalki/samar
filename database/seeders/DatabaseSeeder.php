<?php

namespace Database\Seeders;

use App\Models\Era;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Demo data: three users and famous classical verses (all accounts use the password "password").
     */
    public function run(): void
    {
        $layla = User::factory()->create(['name' => 'Layla Ahmed', 'email' => 'layla@example.com']);
        $omar = User::factory()->create(['name' => 'Omar Khalid', 'email' => 'omar@example.com']);
        $noura = User::factory()->create(['name' => 'Noura Saad', 'email' => 'noura@example.com']);

        $eras = Era::pluck('id', 'slug');

        $verses = [
            [$layla, 'pre-islamic', 'امرؤ القيس', 'قفا نبك من ذكرى حبيب ومنزل بسقط اللوى بين الدخول فحومل'],
            [$omar, 'pre-islamic', 'عنترة بن شداد', 'ولقد ذكرتك والرماح نواهل مني وبيض الهند تقطر من دمي'],
            [$noura, 'early-islamic', 'حسان بن ثابت', 'وأحسن منك لم تر قط عيني وأجمل منك لم تلد النساء'],
            [$layla, 'umayyad', 'جميل بثينة', 'ألا ليت ريعان الشباب جديد ودهر تولى يا بثين يعود'],
            [$omar, 'abbasid', 'المتنبي', 'على قدر أهل العزم تأتي العزائم وتأتي على قدر الكرام المكارم'],
            [$noura, 'abbasid', 'أبو نواس', 'دع عنك لومي فإن اللوم إغراء وداوني بالتي كانت هي الداء'],
            [$layla, 'andalusian', 'ابن زيدون', 'أضحى التنائي بديلا من تدانينا وناب عن طيب لقيانا تجافينا'],
            [$omar, 'mamluk-ottoman', 'صفي الدين الحلي', 'سلي الرماح العوالي عن معالينا واستشهدي البيض هل خاب الرجا فينا'],
            [$noura, 'modern', 'أحمد شوقي', 'وما نيل المطالب بالتمني ولكن تؤخذ الدنيا غلابا'],
            [$layla, 'modern', 'حافظ إبراهيم', 'أنا البحر في أحشائه الدر كامن فهل سألوا الغواص عن صدفاتي'],
        ];

        $quotes = collect($verses)->map(fn (array $verse) => Quote::factory()->for($verse[0])->create([
            'era_id' => $eras[$verse[1]],
            'poet' => $verse[2],
            'content' => $verse[3],
        ]));

        // A few likes so the "Quote of the Day" has a clear winner.
        $quotes[4]->likedBy()->attach([$layla->id, $noura->id, $omar->id]);   // Al-Mutanabbi
        $quotes[6]->likedBy()->attach([$layla->id, $omar->id]);               // Ibn Zaydun
        $quotes[9]->likedBy()->attach([$noura->id]);                          // Hafez Ibrahim
    }
}
