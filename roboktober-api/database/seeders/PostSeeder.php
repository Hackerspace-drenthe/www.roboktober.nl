<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ContentFormat;
use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $inhoud = <<<'HTML'
<p>
  Hackerspace Drenthe staat al jaren bekend als de plek waar hardware-enthousiastelingen,
  makers en tinkerers samenkomen. We organiseren al lezingen, soldeersessies en hackathons —
  maar dit najaar voegen we iets nieuws toe: <strong>Roboktober</strong>.
</p>

<h2>Wat is Roboktober?</h2>
<p>
  Roboktober is een combat robotics evenement dat in oktober 2026 plaatsvindt bij
  Hackerspace Drenthe. Kleine gevechtsrobots strijden tegen elkaar in een arena.
  Voor de eerste editie doen we het rustig aan: <strong>alleen antweights</strong> —
  robots van maximaal 150 gram. Goedkoper om te bouwen, makkelijker te transporteren
  en prima geschikt voor een eerste editie.
</p>

<h2>Hoe ziet het eruit?</h2>
<p>
  We plannen twee grote momenten in oktober:
</p>
<ul>
  <li><strong>Begin oktober:</strong> Kickoff-avond. Introductie, rondleiding, teams bekendgemaakt, eerste blik op de arena.</li>
  <li><strong>Eind oktober:</strong> Battle Day. De gevechten zelf — inclusief publiek, commentaar en veiligheidskeuring.</li>
</ul>
<p>
  Het exacte programma wordt de komende weken verder ingevuld. Houd deze pagina in de gaten.
</p>

<h2>Antweight: klein maar gevaarlijk</h2>
<p>
  150 gram klinkt weinig, maar onderschat ze niet. Met een goed ontworpen wapen en een stevig
  chassis kunnen antweights flink wat schade aanrichten. En het mooie: de kosten blijven
  beheersbaar. Een basisrobot hoef je niet meer dan <strong>€15 à €20</strong> voor uit te geven —
  zeker als je gebruik maakt van de lasercutter en 3D-printers bij de hackerspace. Wil je daarna
  verder gaan met leds, geluid of een beter wapen? Dat kan altijd nog.
</p>

<h2>Hackerspace Drenthe doet mee</h2>
<p>
  We zijn niet alleen de organisatoren — we bouwen ook gewoon mee. Ons team heeft al een
  eerste robot in de maak: <strong>HackerBot</strong>, een antweight van max. 150 gram.
  Nog volop in ontwikkeling, elke woensdagavond een stap verder. Ben je benieuwd? Loop gerust binnen.
</p>

<h2>Aanmelden als team</h2>
<p>
  Heb je zin om mee te doen? Aanmelden kan nu al via de
  <a href="/app/aanmelden">aanmeldpagina</a>. Je hoeft je robot nog niet af te hebben —
  geef gewoon aan dat je meedoet. We begeleiden beginners waar nodig.
</p>

<h2>Vragen?</h2>
<p>
  Kom langs op een van onze open avonden, of stuur een mail naar
  <a href="mailto:info@hackerspacedrenthe.nl">info@hackerspacedrenthe.nl</a>.
  We zijn enthousiast en horen graag van je.
</p>
HTML;

        Post::create([
            'titel' => 'Roboktober: combat robots komen naar Hackerspace Drenthe',
            'slug' => 'roboktober-combat-robots-hackerspace-drenthe',
            'excerpt' => 'Dit najaar organiseert Hackerspace Drenthe voor het eerst een combat robotics evenement: Roboktober. Kickoff begin oktober, battles eind oktober. Aanmelden kan nu al.',
            'content' => $inhoud,
            'content_format' => ContentFormat::Html,
            'is_published' => true,
            'published_at' => now(),
        ]);
    }
}
