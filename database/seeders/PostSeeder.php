<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\Concerns\CopiesSiteImages;
use Illuminate\Database\Seeder;

/**
 * News posts are reports of past events, so run EventSeeder first.
 */
class PostSeeder extends Seeder
{
    use CopiesSiteImages;

    public function run(): void
    {
        $author = User::where('email', UserSeeder::AUTHOR_EMAIL)->first() ?? User::where('is_admin', true)->first();

        foreach ($this->posts() as $data) {
            $data['event_id'] = Event::where('slug', $data['event'])->value('id');
            unset($data['event']);
            $data['image'] = $this->copyImage($data['image'], 'posts');

            Post::updateOrCreate(['slug' => $data['slug']], $data)
                ->user()->associate($author)->save();
        }
    }

    private function posts(): array
    {
        return [
            [
                'event' => 'inauguration-gekrafs-dpln-netherlands',
                'slug' => 'gekrafs-installs-representative-council-in-the-netherlands',
                'title' => 'GEKRAFS installs its Representative Council in the Netherlands',
                'excerpt' => 'GEKRAFS has officially inaugurated its Overseas Representative Council (DPLN) in the Netherlands, connecting Indonesia\'s creative economy with the creative community in the Netherlands.',
                'image' => 'pelantikan.jpg',
                'published_at' => '2024-05-17 10:00:00',
                'body' => <<<'HTML'
                    <p><strong>GEKRAFS</strong> (Gerakan Ekonomi Kreatif Nasional) has officially inaugurated its Overseas Representative Council in the Netherlands: <strong>DPLN Belanda</strong>. The members of the new council came together for the inauguration and proudly presented the DPLN Belanda flag.</p>
                    <p>GEKRAFS is a movement of the community and creative economy actors who share a common vision: to learn, synergize, and empower the creative economy as the future, towards Golden Indonesia 2045. With DPLN Belanda, GEKRAFS strengthens its presence abroad as one of its six overseas representative councils.</p>
                    <blockquote><p>DPLN Belanda will build bridges between Indonesian creative economy players, the Indonesian diaspora and partners in the Netherlands – through events, knowledge sharing and collaboration across the 17 creative economy sub-sectors.</p></blockquote>
                    <p><strong>#EkrafBangkitIndonesiaMaju</strong></p>
                    HTML,
            ],
            [
                'event' => 'business-seminar-investing-in-wonderful-indonesia',
                'slug' => 'business-seminar-investing-in-wonderful-indonesia-report',
                'title' => 'Business Seminar "Investing in Wonderful Indonesia" at the Embassy in The Hague',
                'excerpt' => 'On 19 July 2024, GEKRAFS DPLN Belanda hosted the Business Seminar "Investing in Wonderful Indonesia" at the Indonesian Embassy in The Hague.',
                'image' => 'members1.png',
                'published_at' => '2024-07-20 10:00:00',
                'body' => <<<'HTML'
                    <p>On Friday 19 July 2024, GEKRAFS DPLN Belanda hosted the Business Seminar <strong>"Investing in Wonderful Indonesia"</strong> at the Indonesian Embassy in The Hague (Tobias Asserlaan 8).</p>
                    <p>The evening was dedicated to the business and investment opportunities of Indonesia's creative economy, and brought together entrepreneurs, investors and members of the Indonesian community in the Netherlands.</p>
                    <h3>Speakers</h3>
                    <p>The program featured two speakers from GEKRAFS Indonesia:</p>
                    <ul>
                        <li><p><strong>Kawendra Lukistian</strong>, General chairman of the GEKRAFS Central Board</p></li>
                        <li><p><strong>Osco Olfriady Letunggamu</strong>, member of the GEKRAFS INDONESIA Expert Council</p></li>
                    </ul>
                    <p>GEKRAFS DPLN Belanda thanks the Indonesian Embassy in The Hague, the speakers and all guests for their participation.</p>
                    <p><strong>#EkrafBangkitIndonesiaMaju</strong></p>
                    HTML,
            ],
        ];
    }
}
