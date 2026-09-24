<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Database\Seeders\Concerns\CopiesSiteImages;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    use CopiesSiteImages;

    public function run(): void
    {
        $author = User::where('email', UserSeeder::AUTHOR_EMAIL)->first() ?? User::where('is_admin', true)->first();

        foreach ($this->events() as $data) {
            $data['image'] = $this->copyImage($data['image'], 'events');

            Event::updateOrCreate(['slug' => $data['slug']], $data)
                ->user()->associate($author)->save();
        }
    }

    private function events(): array
    {
        return [
            [
                'slug' => 'inauguration-gekrafs-dpln-netherlands',
                'title' => 'Inauguration of the GEKRAFS Representative Council in the Netherlands',
                'location' => 'The Netherlands',
                'starts_at' => '2024-05-16 18:00:00',
                'ends_at' => null,
                'image' => 'members3.png',
                'is_published' => true,
                'description' => <<<'HTML'
                    <p><strong>GEKRAFS</strong> (Gerakan Ekonomi Kreatif Nasional) installs its Overseas Representative Council (Dewan Perwakilan Luar Negeri) in the Netherlands: <strong>DPLN Belanda</strong>.</p>
                    <p>With DPLN Belanda, GEKRAFS strengthens its presence abroad and builds bridges between Indonesian creative economy players, the Indonesian diaspora and partners in the Netherlands.</p>
                    <p><strong>#EkrafBangkitIndonesiaMaju</strong></p>
                    HTML,
            ],
            [
                'slug' => 'business-seminar-investing-in-wonderful-indonesia',
                'title' => 'Business Seminar: Investing in Wonderful Indonesia',
                'location' => 'Indonesian Embassy, Tobias Asserlaan 8, 2517 KC Den Haag',
                'starts_at' => '2024-07-19 19:00:00',
                'ends_at' => '2024-07-19 21:00:00',
                'image' => 'boards.jpg',
                'is_published' => true,
                'description' => <<<'HTML'
                    <p>GEKRAFS DPLN Belanda invites you to the Business Seminar <strong>"Investing in Wonderful Indonesia"</strong> at the Indonesian Embassy in The Hague.</p>
                    <p>The seminar introduces the business and investment opportunities of Indonesia's creative economy to entrepreneurs, investors and the Indonesian community in the Netherlands.</p>
                    <h3>Speakers</h3>
                    <ul>
                        <li><p><strong>Kawendra Lukistian</strong> – General chairman, GEKRAFS Central Board</p></li>
                        <li><p><strong>Osco Olfriady Letunggamu</strong> – GEKRAFS INDONESIA Expert Council</p></li>
                    </ul>
                    HTML,
            ],
            [
                'slug' => 'indonesian-culinary-creative-fest-2026',
                'title' => 'Indonesian Culinary & Creative Fest 2026',
                'location' => 'Embassy of the Republic of Indonesia, Tobias Asserlaan 8, 2517 KC Den Haag',
                'starts_at' => '2026-10-24 13:00:00',
                'ends_at' => '2026-10-24 17:30:00',
                'image' => 'members2.png',
                'is_published' => true,
                'description' => <<<'HTML'
                    <p>GEKRAFS DPLN Netherlands (Indonesian Movement for Creative Economy – Netherlands Chapter), in collaboration with the Embassy of the Republic of Indonesia in The Hague, proudly presents the <strong>Indonesian Culinary &amp; Creative Fest 2026</strong>.</p>
                    <p>This special event marks Indonesia's <strong>National Creative Economy Day</strong> by celebrating the rich cultural heritage, culinary arts, and creative industries of the Indonesian Archipelago.</p>
                    <h3>Program highlights</h3>
                    <ul>
                        <li><p><strong>Panel Discussion:</strong> <em>"Unlocking Cultural Heritage: Fostering Indonesia-Netherlands Creative Economy Partnerships"</em>, featuring key representatives from government, trade agencies, and creative industry leaders.</p></li>
                        <li><p><strong>Cold Culinary Demonstration:</strong> an exclusive live presentation of Indonesian spice mixology and modern cold culinary arts by featured chefs.</p></li>
                        <li><p><strong>Networking Reception:</strong> a curated tasting session and networking with diplomatic representatives, local government officials, and business partners.</p></li>
                    </ul>
                    <p><strong>Dress code:</strong> Smart Casual / Traditional Attire</p>
                    <p>Join us as we foster stronger cultural and economic bridges between Indonesia and the Netherlands. Please confirm your attendance with one of our representatives listed on the <a href="/contact">Contact page</a>.</p>
                    HTML,
            ],
        ];
    }
}
