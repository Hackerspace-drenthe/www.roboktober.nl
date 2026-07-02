<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\LinkCategorie;
use App\Models\Link;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            // Wallie / Hackerspace
            [
                'titel' => 'Hackerspace Drenthe',
                'url' => 'https://hackerspacedrenthe.nl',
                'beschrijving' => 'De organiserende hackerspace. Werkplaats, gereedschap, kennis en community — alles op één plek.',
                'categorie' => LinkCategorie::Wallie,
            ],

            // Community
            [
                'titel' => 'Robotic Combatants Forum (RCF)',
                'url' => 'https://www.rcforum.net',
                'beschrijving' => 'Het grootste Engelstalige forum voor gevechtrobotbouwers. Duizenden threads over designs, wapens en materiaalkeuze.',
                'categorie' => LinkCategorie::Community,
            ],
            [
                'titel' => 'Facebook: Robot Combat NL/BE',
                'url' => 'https://www.facebook.com/groups/robotcombatNLBE',
                'beschrijving' => 'Nederlandstalige community voor gevechtrobot-enthousiastelingen in Nederland en België.',
                'categorie' => LinkCategorie::Community,
            ],
            [
                'titel' => 'Reddit: r/battlebots',
                'url' => 'https://www.reddit.com/r/battlebots',
                'beschrijving' => 'Actieve subreddit met nieuws, bouw-logs en discussies over BattleBots, Robot Wars en lokale events.',
                'categorie' => LinkCategorie::Community,
            ],

            // Competitie
            [
                'titel' => 'Antweight World Series Regels',
                'url' => 'https://www.antweightworldseries.co.uk/rules',
                'beschrijving' => 'Officiële veiligheids- en constructieregels voor de antweight klasse (≤ 150 gram). Goed startpunt voor beginners.',
                'categorie' => LinkCategorie::Competitie,
            ],
            [
                'titel' => 'Beetleweight Design Guide',
                'url' => 'https://www.beetleweight.com/design-guide',
                'beschrijving' => 'Praktische gids voor het ontwerpen van een beetleweight gevechtrobot (≤ 1,36 kg).',
                'categorie' => LinkCategorie::Competitie,
            ],
            [
                'titel' => 'Technische Keuring Checklist — Roboktober',
                'url' => '/app/technische-keuring',
                'beschrijving' => 'Wat inspecteren we op de dag zelf? Alle veiligheidseisen voor deelname op een rij.',
                'categorie' => LinkCategorie::Competitie,
            ],

            // Tools
            [
                'titel' => 'Fusion 360 (gratis voor hobbyisten)',
                'url' => 'https://www.autodesk.com/products/fusion-360/personal',
                'beschrijving' => 'Krachtige CAD/CAM software, gratis voor persoonlijk gebruik. Ideaal voor het ontwerpen van robot-frames en onderdelen.',
                'categorie' => LinkCategorie::Tools,
            ],
            [
                'titel' => 'OnShape (volledig browser-based CAD)',
                'url' => 'https://www.onshape.com',
                'beschrijving' => 'Gratis cloud-CAD, geen installatie nodig. Werkt uitstekend voor samenwerken aan robot-designs.',
                'categorie' => LinkCategorie::Tools,
            ],
            [
                'titel' => 'BotBrain Combat Calculator',
                'url' => 'https://www.botbrain.net',
                'beschrijving' => 'Bereken kinetic energy, tip speed en impact force van spinners en andere wapens.',
                'categorie' => LinkCategorie::Tools,
            ],
            [
                'titel' => 'KiCad — Open-source PCB Design',
                'url' => 'https://www.kicad.org',
                'beschrijving' => 'Gratis PCB-ontwerpsoftware. Handig als je zelf ESC\'s, failsafes of sensor-boards wilt maken.',
                'categorie' => LinkCategorie::Tools,
            ],

            // Onderdelen
            [
                'titel' => 'RobotShop Nederland',
                'url' => 'https://www.robotshop.com',
                'beschrijving' => 'Brede webshop voor motoren, ESC\'s, servos, sensoren en kant-en-klare robotonderdelen.',
                'categorie' => LinkCategorie::Onderdelen,
            ],
            [
                'titel' => 'Fingertech Robotics',
                'url' => 'https://www.fingertechrobotics.com',
                'beschrijving' => 'Gespecialiseerd in antweight en beetleweight onderdelen: compacte ESC\'s, UHMWPE platen, titanium hardware.',
                'categorie' => LinkCategorie::Onderdelen,
            ],
            [
                'titel' => 'Botbuilder.eu',
                'url' => 'https://www.botbuilder.eu',
                'beschrijving' => 'Europese leverancier van gevechtrobot-onderdelen. Korte levertijden naar Nederland.',
                'categorie' => LinkCategorie::Onderdelen,
            ],
            [
                'titel' => 'AliExpress: N20 motoren & TB6612FNG',
                'url' => 'https://www.aliexpress.com',
                'beschrijving' => 'Goedkope N20 gearmotoren en motordriver-breakout-boards — populair voor antweight builds. Zoek op "N20 micro metal gear motor".',
                'categorie' => LinkCategorie::Onderdelen,
            ],

            // Documentatie
            [
                'titel' => 'Hackerspace Drenthe Wiki: Lasercutter gebruik',
                'url' => 'https://wiki.hackerspacedrenthe.nl/lasercutter',
                'beschrijving' => 'Interne wiki over het gebruik van de lasercutter — handig voor chassis-platen uit acrylaat of hout.',
                'categorie' => LinkCategorie::Documentatie,
            ],
            [
                'titel' => '3D-printen bij de Hackerspace',
                'url' => 'https://wiki.hackerspacedrenthe.nl/3dprinter',
                'beschrijving' => 'Overzicht van beschikbare printers, materialen en insteltips voor PETG en TPU — populair voor robot-bumpers.',
                'categorie' => LinkCategorie::Documentatie,
            ],
            [
                'titel' => 'Beginnersgids Gevechtrobots (PDF)',
                'url' => 'https://www.combatrobotics.com/beginners-guide.pdf',
                'beschrijving' => 'Engelstalige gids van RobotCombatOne: concepten, weight classes, wapentypes en veiligheidsregels voor nieuwe bouwers.',
                'categorie' => LinkCategorie::Documentatie,
            ],
        ];

        foreach ($links as $link) {
            Link::create($link);
        }
    }
}
