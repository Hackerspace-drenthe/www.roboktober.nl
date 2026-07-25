# Roboktober Website Plan 2026+

## 1. Context en eigenaarschap
Roboktober is een evenement in oktober bij Hackerspace Drenthe, georganiseerd door Hackerspace Drenthe en Walter.

**Evenement:**
- **Kickoff**: begin oktober (zaterdag of woensdagavond)
- **Battle**: eind oktober (zaterdag of woensdagavond)
- **Locatie**: Hackerspace Drenthe (volledig adres + kaart op site)
- **Type robot**: combat robots (antweight/beetleweight gewichtsklassen)
- **Teamregistratie**: altijd open (geen sluitingsdatum)
- **Taal site**: uitsluitend Nederlands
- **Eerste editie**: 2026 (geen archiefmateriaal van eerdere jaren)

De website moet twee doelen tegelijk dienen:
- Teaser en aanjager voor de kickoff en de rest van oktober.
- Duurzame kennisbank en naslagwerk voor deze en toekomstige Roboktober-edities.

## 2. Naamkeuze
Definitieve merknaam: **Roboktober**

**Naam-uitleg:**
- **Roboktober** = **Robot** + **Oktober**
- De maand van de robot en robots bouwen
- Speelse woordspeling die direct de essentie communiceert
- Memorabel en uniek voor branding

**Gebruik:**
- Primaire merknaam: Roboktober (met k)
- Domein: roboktober.nl
- Alternatieve spelling "roboktober" (met t) kan voor variatie in communicatie
- Social media: #Roboktober of #roboktober


## 2.1 Domeinstrategie
Definitieve keuze:
- Hoofddomein: roboktober.nl
- Enige publieke URL: https://roboktober.nl
- Geen aliassen, geen subdomeinen, geen extra doorverwijsdomeinen.

Onderbouwing:
- Korter en memorabeler dan robotoberdrenthe.nl
- Beschikbaar en direct registreerbaar
- Eenduidig voor bezoekers en organisatie
- Minder beheercomplexiteit en minder kans op SEO-verwarring
- Toekomstvast voor meerdere Roboktober-edities op hetzelfde domein

Praktisch:
- Alle communicatie (posters, social, docs, QR-codes) verwijst naar exact deze ene URL.

## 2.2 Contentbeheerstrategie (definitief)

**Stack-keuze: Laravel API + Vue 3 frontend**

Server: Hackerspace Drenthe eigen server (SSH-toegang beschikbaar).

### Architectuur: API-first (headless)

```
┌─────────────────────────────────────┐
│  Frontend (Vue/React/Svelte/etc)    │
│  - Public website                    │
│  - Admin dashboard                   │
│  - Communiceert via REST API         │
└──────────────┬──────────────────────┘
               │ HTTP/JSON
               │
┌──────────────▼──────────────────────┐
│  Laravel API Backend (nieuwste)     │
│  - REST API endpoints                │
│  - User management (Sanctum/Passport)│
│  - Team/robot registratie            │
│  - Content management                │
│  - Asset management (STL, BOM, docs) │
│  - Database: MySQL/PostgreSQL        │
└─────────────────────────────────────┘
```

### Backend: Laravel 11.x API
- **Rol**: API-only backend, geen Blade-templates voor public site.
- **Framework**: Laravel 11.x (nieuwste stabiele versie).
- **API**: RESTful JSON API met Laravel Sanctum voor authenticatie.
- **Functionaliteit**:
  - User management (organizers, admins)
  - Team registratie API
  - Robot registratie API
  - Battle aanmelding API
  - Content API (pages, posts, resources)
  - Asset management API (upload STL, BOM, photos)
  - Media library met versiebeheer
- **Database**: MySQL of PostgreSQL.
- **Admin**: Laravel Filament 3.x als admin-paneel (draait binnen Laravel).

### Frontend: Vue 3 + Vite (definitief)
- **Rol**: Public-facing website, communiceert via REST API.
- **Framework**: Vue 3 + Vite (definitieve keuze).
- **Communicatie**: Axios naar Laravel API.
- **Deployment**: Build naar Laravel `public/` folder (eenvoudigste optie op eigen server).
- **Features**:
  - Client-side routing (Vue Router)
  - TypeScript (strict mode)
  - Tailwind CSS voor styling

### Voordelen van deze architectuur
- **Schaalbaar**: frontend en backend onafhankelijk schaalbaar.
- **Flexibel**: frontend-framework kan later wisselen zonder backend-wijzigingen.
- **Modern**: API-first is industry standard.
- **Herbruikbaar**: API kan later gebruikt worden voor mobiele app.
- **Duidelijke scheiding**: backend-developers en frontend-developers kunnen parallel werken.

### Git-workflow
- Twee repositories (of monorepo):
  - `roboktober-api` (Laravel backend)
  - `roboktober-frontend` (Vue/React/etc)
- Deploy-flow:
  - Backend: Git push → server pull → `composer install` → `php artisan migrate` → cache clear
  - Frontend: Git push → build → deploy naar CDN of Laravel public folder

## 3. Hoofddoelen van de site
1. Mensen warm maken voor het event (teaser, countdown, programmasfeer).
2. Deelnemers laten bouwen met complete, praktische bouwinformatie.
3. Alle assets duurzaam beschikbaar houden (BOM, STL, bouwdocs, blog, links).
4. Herbruikbare basis neerzetten voor toekomstige Roboktober-jaren.

## 4. Doelgroepen

### Primaire doelgroep: middelbare school niveau en beginnende makers
- **Leeftijd**: 12-18 jaar (middelbare scholieren) + volwassen beginners
- **Technisch niveau**: weinig tot geen ervaring met robotica of elektronica
- **Leerdoel**: praktisch leren door te doen, succeservaringen opdoen
- **Behoefte**: duidelijke uitleg, visuele ondersteuning, stap-voor-stap begeleiding

### Secundaire doelgroepen
- **Deelnemers met ervaring**: willen snel alle bouwbestanden en instructies
- **Begeleiders/ouders**: willen overzicht van materiaal, kosten, tijdsinvestering
- **Bezoekers/community**: willen nieuws, planning en sfeer
- **Toekomstige organisatoren**: willen kunnen voortbouwen op bestaande content

## 4.1 Content- en taalgebruik principes

### Taalgebruik (B1-niveau, middelbare school)
- **Simpel en direct**: korte zinnen, actieve vorm, geen jargon zonder uitleg
- **Uitleggen van vaktermen**: eerste keer vaktermen uitleggen of in tooltip
- **Concrete instructies**: "Schroef de motor vast met 4 boutjes" i.p.v. "Bevestig de aandrijving"
- **Positieve tone**: wervend, enthousiasmerend, niet intimiderend
- **Voorbeelden gebruiken**: "Dit lijkt op LEGO, maar dan met echte motoren"

### Visuele content (educatief en wervend)
- **Foto's boven tekst**: elke belangrijke stap heeft minimaal 1 foto
- **Annotaties**: pijlen, cirkels, labels op foto's waar nodig
- **Video's**: korte clips (30-90 sec) voor complexe handelingen
- **Infographics**: tijdlijn, kostenoverzicht, deelnamestappen als visueel schema
- **3D visualisaties**: interactieve viewer voor begrip van onderdelen
- **Voorbeeldrobots**: foto's van eerdere robots voor inspiratie
- **Procesfoto's**: laat verschillende bouwstadia zien, niet alleen eindresultaat

### Structuur van educatieve content
- **Korte introducties**: max 2-3 regels per sectie
- **Genummerde stappen**: duidelijke volgorde (1, 2, 3...)
- **Checkboxen/progress**: laat zien hoever je bent
- **Waarschuwingen en tips**: duidelijk gemarkeerd met iconen
- **Geschatte tijd**: "Dit duurt ongeveer 30 minuten"
- **Moeilijkheidsgraad**: ⭐ makkelijk, ⭐⭐ gemiddeld, ⭐⭐⭐ uitdagend

### Tone of voice voorbeelden
✅ **Goed**: "Eerst schroef je de motor vast. Let op: de kabels moeten naar achteren wijzen!"
❌ **Te complex**: "Monteer de actuator met de gespecificeerde bevestigingsmiddelen conform de assemblage-instructies."

✅ **Goed**: "Je robot is klaar voor zijn eerste test. Spannend!"
❌ **Te droog**: "De assemblage is voltooid. Proceed to testing phase."

## 5. Informatiestructuur (MVP + doorontwikkeling)
1. Home (teaser)
2. Programma en wat er moet gebeuren
3. Aanmelden (Team en Robot registratie)
4. Build Hub (BOM, STL, bouwbeschrijving, downloads)
5. 3D Viewer
6. Nieuws/Blog
7. Resources (aparte pagina, Wallie-sites + externe links, herbruikbaar per editie)
8. Teams en Robots (overzicht deelnemers)
9. Battle Aanmelding (laatste weken)
10. Archief (edities per jaar)
11. Credits en Wallie Touch

## 5.1 Team en Robot registratiemodel

### Teamstructuur
- Team bestaat uit minimaal 1 volwassene.
- Team kan optioneel worden geassisteerd door kinderen.
- Team kan 1 of meer robots maken.
- Team heeft: teamnaam, contactpersoon, email, aantal volwassenen, aantal kinderen (optioneel).

### Robotregistratie
- Per robot: naam, gewichtsklasse, korte beschrijving, foto (optioneel).
- Robot is gekoppeld aan een team.
- Status: in ontwikkeling, gereed voor battle.

### Battle-aanmelding (laatste weken)
- Team meldt specifieke robot(s) aan voor battle.
- Per battle-aanmelding: robot, gewichtsklasse, bevestiging technische eisen.
- Battle-aanmeldingen worden apart geregistreerd (later in evenement).

### Technische implementatie: Laravel 11.x API

## 5.2 Laravel API-structuur

### Project-structuur
```
roboktober-api/ (Laravel 11.x)
├── app/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Team.php          (HasMedia trait)
│   │   ├── Robot.php         (HasMedia trait)
│   │   ├── BattleRegistration.php
│   │   ├── Page.php          (HasMedia trait)
│   │   ├── Post.php          (HasMedia trait)
│   │   ├── Media.php         (centraal mediamodel)
│   │   ├── MediaVariant.php  (thumbnails/varianten)
│   │   └── Link.php          (externe URLs)
│   ├── Traits/
│   │   └── HasMedia.php      (polymorfische media-koppeling)
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── TeamController.php
│   │   │   ├── RobotController.php
│   │   │   ├── BattleController.php
│   │   │   ├── PageController.php
│   │   │   ├── PostController.php
│   │   │   ├── MediaController.php    (upload + download)
│   │   │   └── LinkController.php     (externe URL-resources)
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Filament/
│   ├── Jobs/
│   │   └── GenerateMediaVariants.php  (thumbnails na upload)
│   └── Policies/
├── database/migrations/
│   ├── create_media_table.php
│   ├── create_media_varianten_table.php
│   ├── create_mediables_table.php
│   ├── create_links_table.php
│   └── ...
└── tests/
```

### REST API Endpoints (api.php)

#### Public endpoints (geen auth)
```
GET    /api/pages                         # Lijst van pagina's
GET    /api/pages/{slug}                  # Specifieke pagina (incl. media)
GET    /api/posts                         # Nieuwsberichten (incl. featured media)
GET    /api/posts/{slug}                  # Post detail (incl. alle media-collecties)
GET    /api/teams                         # Publiek teamoverzicht
GET    /api/teams/{id}/robots             # Robots van een team
GET    /api/links                         # Externe links/resources
GET    /api/media/{id}/download           # Download bestand (telt download)
GET    /api/media/{id}/varianten/{naam}   # Opvragen variant (thumbnail, etc.)
POST   /api/teams                         # Team registratie
```

#### Authenticated endpoints (Sanctum token vereist)
```
POST   /api/admin/login
POST   /api/admin/logout

# Media management
POST   /api/admin/media                   # Upload bestand (image, STL, PDF, ...)
PUT    /api/admin/media/{id}              # Metadata updaten
DELETE /api/admin/media/{id}             # Soft delete
POST   /api/admin/media/{id}/koppel      # Koppel aan model (post, robot, page)
DELETE /api/admin/media/{id}/ontkoppel   # Ontkoppel van model

# Link management (externe URLs)
POST   /api/admin/links
PUT    /api/admin/links/{id}
DELETE /api/admin/links/{id}

# Content management
POST   /api/admin/pages
PUT    /api/admin/pages/{id}
DELETE /api/admin/pages/{id}
POST   /api/admin/posts
PUT    /api/admin/posts/{id}

# Team management
GET    /api/admin/teams
PUT    /api/admin/teams/{id}
DELETE /api/admin/teams/{id}
POST   /api/admin/robots
PUT    /api/admin/robots/{id}
DELETE /api/admin/robots/{id}

# Battle management
GET    /api/admin/battles
POST   /api/admin/battles
PUT    /api/admin/battles/{id}
```

### Database-schema (Laravel migrations)

#### Ontwerpprincipes (SOLID)
Het media/asset/resource-beheer volgt deze SOLID-redenering:

- **SRP**: `media` beheert opgeslagen bestanden; `links` beheert externe URLs; `mediables` koppelt media aan willekeurige modellen.
- **OCP**: Nieuwe content-types (video, firmware, CAD) vereisen geen schema-wijziging — alleen een nieuw `mime_type`.
- **LSP**: Elk model dat media nodig heeft (`Post`, `Robot`, `Page`) gebruikt dezelfde `mediables`-interface.
- **ISP**: Consumenten (frontend, API) vragen alleen de collectie op die ze nodig hebben ('featured', 'gallery', 'downloads').
- **DIP**: Content-modellen zijn niet afhankelijk van een specifiek bestandstype — ze hangen af van de abstracte `Mediable`-interface.

```
media                    ← ALLE bestanden (images, STL, PDF, BOM, firmware, …)
  ↕ via mediables        ← polymorfische koppeltabel (collection per context)
Post, Robot, Page, …     ← willekeurig model kan media hebben

links                    ← externe URLs (aparte verantwoordelijkheid)
```

```php
/**
 * media — centrale opslag voor alle binaire bestanden.
 *
 * Vervangt: post_images, assets.
 * Koppeling aan content via mediables (polymorfisch).
 *
 * Ondersteunde typen (via mime_type, niet via enum):
 *   image/jpeg, image/png, image/webp, image/svg+xml
 *   model/stl, model/obj, model/gltf+json
 *   application/pdf, text/csv, application/vnd.ms-excel
 *   application/octet-stream (firmware .hex/.bin)
 *   video/mp4 (optioneel, voor opname-embeds)
 */
Schema::create('media', function (Blueprint $table) {
    $table->id();
    $table->string('naam');                              // Weergavenaam
    $table->string('bestandsnaam');                      // Originele bestandsnaam
    $table->string('pad');                               // Storage-pad (relatief aan disk)
    $table->string('disk')->default('public');           // 'local', 's3', 'public'
    $table->string('mime_type');                         // IANA: 'image/jpeg', 'model/stl', …
    $table->string('extensie', 10);                      // 'jpg', 'stl', 'pdf', …
    $table->unsignedBigInteger('grootte');               // Bestandsgrootte in bytes
    $table->string('hash', 64)->nullable();              // SHA-256 (deduplicatie)
    $table->json('meta')->nullable();                    // Afh. van type:
                                                         //   image: {width, height, color_space}
                                                         //   stl: {triangles, volume_mm3}
                                                         //   pdf: {pages}
                                                         //   video: {duration_sec, width, height}
    $table->string('versie', 20)->nullable();            // '1.0', '2.1' — voor downloads
    $table->text('versie_notities')->nullable();         // Changelog per versie
    $table->foreignId('vorige_versie_id')                // Versieketen (self-referencing)
          ->nullable()->constrained('media')->nullOnDelete();
    $table->unsignedInteger('downloads')->default(0);    // Downloadteller
    $table->foreignId('geupload_door')
          ->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->softDeletes();                               // Veilig verwijderen

    $table->index('mime_type');
    $table->index('hash');
});

/**
 * media_varianten — verwerkte versies van media (thumbnails, geoptimaliseerde varianten).
 *
 * Verantwoordelijkheid: sla alleen gegenereerde afgeleiden op.
 * Aangemaakt door een queue-job na upload.
 */
Schema::create('media_varianten', function (Blueprint $table) {
    $table->id();
    $table->foreignId('media_id')->constrained()->onDelete('cascade');
    $table->string('naam', 50);                          // 'thumb_sm', 'medium', 'large', 'preview'
    $table->string('pad');
    $table->string('mime_type');
    $table->unsignedBigInteger('grootte');
    $table->json('meta')->nullable();                    // {width, height}
    $table->timestamps();

    $table->unique(['media_id', 'naam']);
});

/**
 * mediables — polymorfische koppeltabel.
 *
 * Koppelt media aan elk willekeurig model via een benoemde collectie.
 * Zo kan een Post zowel een 'featured' image, een 'gallery' en 'bijlagen' hebben.
 * Een Robot kan een 'foto' collectie hebben. Een Page een 'hero' afbeelding.
 *
 * Collectie-conventies:
 *   'featured'    — één hoofdafbeelding (header/thumbnail)
 *   'gallery'     — meerdere afbeeldingen in volgorde
 *   'bijlagen'    — downloadbare bestanden (STL, PDF, BOM, firmware)
 *   'foto'        — enkelvoudige foto (robot, team)
 */
Schema::create('mediables', function (Blueprint $table) {
    $table->id();
    $table->foreignId('media_id')->constrained()->onDelete('cascade');
    $table->morphs('mediable');                          // mediable_type + mediable_id
    $table->string('collectie')->default('default');     // zie conventies hierboven
    $table->string('alt_tekst')->nullable();             // WCAG: verplicht voor images
    $table->text('onderschrift')->nullable();            // Zichtbare caption
    $table->unsignedSmallInteger('volgorde')->default(0);
    $table->json('meta')->nullable();                    // Gebruik-specifieke context
    $table->timestamps();

    $table->index(['mediable_type', 'mediable_id', 'collectie']);
});

/**
 * teams table
 */
Schema::create('teams', function (Blueprint $table) {
    $table->id();
    $table->string('naam');
    $table->string('contactpersoon');
    $table->string('email');
    $table->unsignedSmallInteger('volwassenen')->default(1);
    $table->unsignedSmallInteger('kinderen')->nullable();
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->text('opmerkingen')->nullable();
    $table->timestamps();
    // Teamfoto via mediables (collectie: 'foto')
});

/**
 * robots table
 */
Schema::create('robots', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id')->constrained()->onDelete('cascade');
    $table->string('naam');
    $table->enum('gewichtsklasse', ['antweight', 'beetleweight', 'featherweight']);
    $table->text('beschrijving')->nullable();
    $table->enum('status', ['in_ontwikkeling', 'gereed', 'battle_ready'])->default('in_ontwikkeling');
    $table->timestamps();
    // Robotfoto via mediables (collectie: 'foto')
    // STL-bestanden via mediables (collectie: 'bijlagen')
});

/**
 * battle_registrations table
 */
Schema::create('battle_registrations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('robot_id')->constrained()->onDelete('cascade');
    $table->date('datum');
    $table->boolean('technische_check')->default(false);
    $table->boolean('approved')->default(false);
    $table->text('opmerkingen')->nullable();
    $table->timestamps();
});

/**
 * pages table — statische content (Home, Programma, etc.)
 */
Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->string('titel');
    $table->longText('content');
    $table->enum('content_format', ['html', 'markdown'])->default('html');
    $table->json('seo')->nullable();                     // {title, description, og_image}
    $table->boolean('is_published')->default(false);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
    // Afbeeldingen via mediables (collectie: 'hero', 'gallery')
});

/**
 * posts table — nieuws en blog.
 *
 * featured_image en post_images zijn verwijderd:
 * die worden via mediables gekoppeld.
 * Collectie 'featured' = één headerafbeelding.
 * Collectie 'gallery'  = extra foto's onderaan de post.
 * Collectie 'bijlagen' = downloadbare bestanden bij de post.
 */
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->string('titel');
    $table->text('excerpt')->nullable();
    $table->longText('content');
    $table->enum('content_format', ['html', 'markdown'])->default('html');
    $table->string('categorie')->nullable();
    $table->json('tags')->nullable();
    $table->boolean('is_published')->default(false);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
    // Media via mediables:
    //   collectie 'featured'  → één headerafbeelding
    //   collectie 'gallery'   → galerij foto's
    //   collectie 'bijlagen'  → PDF, STL, BOM, video, etc.
});

/**
 * links table — externe URL-resources (vroeger: resources).
 *
 * Verantwoordelijkheid: beheer van externe hyperlinks.
 * Gescheiden van media (bestanden) via SRP.
 * mime_type en meta bewaren wat we weten over de externe resource.
 */
Schema::create('links', function (Blueprint $table) {
    $table->id();
    $table->string('titel');
    $table->string('url');
    $table->text('beschrijving')->nullable();
    $table->string('mime_type')->nullable();             // 'text/html', 'application/pdf', …
    $table->json('meta')->nullable();                    // {og_title, og_description, og_image,
                                                         //  favicon, last_checked_status_code}
    $table->enum('categorie', [
        'wallie', 'community', 'competitie',
        'tools', 'onderdelen', 'documentatie',
    ]);
    $table->string('eigenaar')->nullable();
    $table->timestamp('verified_at')->nullable();
    $table->timestamps();
});
```

#### Overzicht model-relaties

| Model | Collectie | Mediatype |
|---|---|---|
| `Post` | `featured` | Één headerafbeelding (image/*) |
| `Post` | `gallery` | Extra foto's (image/*) |
| `Post` | `bijlagen` | PDF, STL, BOM, video, firmware |
| `Robot` | `foto` | Één robotfoto (image/*) |
| `Robot` | `bijlagen` | STL-bestanden van dit robot-ontwerp |
| `Team` | `foto` | Teamfoto (image/*) |
| `Page` | `hero` | Achtergrond/headerafbeelding |
| `Page` | `gallery` | Extra content-afbeeldingen |

#### Eloquent-voorbeeld (DIP + LSP)

```php
/**
 * Mediable trait — voeg toe aan elk model dat media ondersteunt.
 * Alle modellen gebruiken dezelfde interface (LSP).
 */
trait HasMedia
{
    /**
     * Alle gekoppelde media (via mediables pivot).
     *
     * @return MorphToMany<Media>
     */
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')
                    ->withPivot(['collectie', 'alt_tekst', 'onderschrift', 'volgorde', 'meta'])
                    ->orderBy('mediables.volgorde');
    }

    /**
     * Media uit een specifieke collectie.
     *
     * @param  string  $collectie
     * @return MorphToMany<Media>
     */
    public function mediaCollectie(string $collectie): MorphToMany
    {
        return $this->media()->wherePivot('collectie', $collectie);
    }

    /**
     * Enkelvoudige featured media (eerste in collectie).
     *
     * @return Media|null
     */
    public function featuredMedia(): ?Media
    {
        return $this->mediaCollectie('featured')->first();
    }
}

// Gebruik in modellen:
class Post extends Model
{
    use HasMedia; // → Post::find(1)->mediaCollectie('gallery')
}

class Robot extends Model
{
    use HasMedia; // → Robot::find(1)->mediaCollectie('bijlagen')
}
```

### Laravel Filament Admin (adminpanel)
- **URL**: `/admin`
- **Functionaliteit**: CRUD voor alle modellen via Filament Resources
- **Authenticatie**: Laravel Sanctum + Filament auth
- **Rich Text Editor**: 
  - Filament RichEditor field voor blog posts
  - Ondersteuning voor HTML markup (headings, lists, bold, italic, links)
  - Inline image uploads via editor
  - Code blocks voor technische content
- **Image Management**:
  - Upload van elk bestandstype via Media-resource
  - Automatische mime_type detectie
  - Thumbnails gegenereerd via queue job
  - Koppeling aan Post/Robot/Page via collectie-picker
  - Alt-tekst en onderschrift per koppeling
  - Drag-and-drop volgorde (volgorde in `mediables`)
- **Features**:
  - Team-overzicht met approve/reject knoppen
  - Robot-management per team
  - Content-editor voor pages en posts met markdown-preview
  - Asset-upload met versiebeheer
  - Battle-planning en -goedkeuring
  - Dashboard met statistieken (aantal teams, robots, downloads)

## 5.3 Frontend-structuur

### Framework-keuze
**Definitief**: Vue 3 + Vite + TypeScript

**Alternatieven** (niet gekozen): React + Vite, Svelte + Vite, Nuxt 3 voor SSR

### Project-structuur (Vue 3 voorbeeld)
```
roboktober-frontend/
├── src/
│   ├── views/
│   │   ├── Home.vue
│   │   ├── Programma.vue
│   │   ├── Aanmelden.vue
│   │   ├── BuildHub.vue
│   │   ├── Teams.vue
│   │   ├── Resources.vue
│   │   └── ...
│   ├── components/
│   │   ├── TeamCard.vue
│   │   ├── RobotCard.vue
│   │   ├── DownloadButton.vue
│   │   └── ...
│   ├── services/
│   │   ├── api.js (Axios instance met base URL)
│   │   ├── teamService.js
│   │   ├── contentService.js
│   │   └── assetService.js
│   ├── composables/ (Vue Composition API)
│   │   ├── useTeams.js
│   │   ├── useContent.js
│   │   └── ...
│   ├── router/
│   │   └── index.js
│   └── main.js
├── public/
└── vite.config.js
```

### API-communicatie voorbeeld
```javascript
// services/api.js
import axios from 'axios';

const api = axios.create({
  baseURL: 'https://roboktober.nl/api',
  headers: {
    'Content-Type': 'application/json',
  }
});

export default api;

// services/teamService.js
import api from './api';

export const getTeams = () => api.get('/teams');
export const getTeam = (id) => api.get(`/teams/${id}`);
export const registerTeam = (data) => api.post('/teams', data);

// services/postService.js
import api from './api';

export const getPosts = (params) => api.get('/posts', { params });
export const getPost  = (slug)   => api.get(`/posts/${slug}`);

// Voorbeeld API response voor GET /api/posts/mijn-eerste-robot
// Media is gegroepeerd per collectie; elk media-item bevat mime_type en meta.
// {
//   "id": 1,
//   "slug": "mijn-eerste-robot",
//   "titel": "Mijn eerste robot is af!",
//   "excerpt": "Eindelijk klaar met bouwen...",
//   "content": "<h2>De eerste test</h2><p>Vandaag heb ik...</p>",
//   "content_format": "html",
//   "categorie": "bouwtip",
//   "tags": ["beginners", "troubleshooting"],
//   "media": {
//     "featured": [
//       {
//         "id": 12,
//         "naam": "Robot klaar",
//         "mime_type": "image/jpeg",
//         "url": "https://roboktober.nl/storage/media/robot-klaar.jpg",
//         "varianten": {
//           "thumb_sm": "https://roboktober.nl/storage/media/varianten/robot-klaar-sm.jpg",
//           "medium":   "https://roboktober.nl/storage/media/varianten/robot-klaar-md.jpg"
//         },
//         "meta": { "width": 1920, "height": 1080 },
//         "alt_tekst": "Voltooide antweight combat robot",
//         "onderschrift": null,
//         "volgorde": 0
//       }
//     ],
//     "gallery": [
//       {
//         "id": 13,
//         "mime_type": "image/jpeg",
//         "url": "...",
//         "alt_tekst": "Motor aansluiting",
//         "onderschrift": "Zo sluit je de motor aan",
//         "volgorde": 0
//       }
//     ],
//     "bijlagen": [
//       {
//         "id": 14,
//         "naam": "Robot chassis v1.1",
//         "mime_type": "model/stl",
//         "extensie": "stl",
//         "grootte": 204800,
//         "versie": "1.1",
//         "download_url": "https://roboktober.nl/api/media/14/download",
//         "onderschrift": "Printbare behuizing"
//       },
//       {
//         "id": 15,
//         "naam": "Bouwhandleiding",
//         "mime_type": "application/pdf",
//         "extensie": "pdf",
//         "grootte": 512000,
//         "download_url": "https://roboktober.nl/api/media/15/download"
//       }
//     ]
//   },
//   "published_at": "2026-10-15T14:30:00Z"
// }

// views/Teams.vue
<script setup>
import { ref, onMounted } from 'vue';
import { getTeams } from '@/services/teamService';

const teams = ref([]);

onMounted(async () => {
  const response = await getTeams();
  teams.value = response.data;
});
</script>
```

### Deployment voor frontend
1. **Binnen Laravel** (gekozen, eigen server):
   - Build frontend: `npm run build`
   - Output naar Laravel `public/` folder
   - Deploy samen met Laravel-API

### Deployment-flow

#### Backend (Laravel API)
1. Lokale ontwikkeling: `php artisan serve`
2. Git push naar `roboktober-api` repository
3. Server:
   ```bash
   cd /var/www/roboktober-api
   git pull origin main
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan storage:link
   ```

#### Frontend
1. Lokale ontwikkeling: `npm run dev`
2. Git push naar `roboktober-frontend` repository
3. Build en deploy:
   - **Eenvoudigst**: `npm run build` → copy `dist/` naar Laravel `public/` folder
   - **Alternatief**: Auto-deploy via Netlify/Vercel/Cloudflare Pages bij Git push

## 6. Pagina- en contentontwerp

### 6.1 Home (teaser + kickoff)
- Hero met duidelijke eventboodschap, datum, locatie en organizer-vermelding.
- Countdown tot kickoff (client-side JavaScript).
- Drie hoofdacties: Doe mee, Start bouwen, Lees de updates.
- Content via API: `GET /api/pages/home`
- Snelle blokken: wat is Roboktober, voor wie, hoe doe je mee.

### 6.2 Programma en "wat moet er gebeuren"
- Tijdlijn per fase (kickoff, bouwweken, demo/afsluiting).
- Checklist per deelnemerstype (starter/gevorderd).
- Takenbord per week: ontwerp, printen, assembleren, testen.
- Praktische info: tijden, locatie, contact, veiligheidsregels.
- Content via API: `GET /api/pages/programma`

### 6.3 Build Hub (kern)
- Bill of Materials (BOM): onderdelen, aantallen, alternatieven, links, richtprijzen.
- STL-bestanden: versiebeheer, changelog, download per onderdeel en als bundel.
- Bouwbeschrijving: stap-voor-stap met foto's en troubleshooting.
- Firmware/software-sectie indien van toepassing.
- Veelgestelde bouwvragen.
- Assets via API: `GET /api/assets` en `GET /api/assets/{id}/download`
- Content via API: `GET /api/pages/build-hub`

### 6.4 3D Viewer
- Interactieve 3D-weergave van behuizing/assemblage.
- Toggle voor onderdelen (explode view optioneel in fase 2).
- Aanbevolen techniek: glTF/GLB met lichte webviewer.

### 6.5 Nieuws/Blog
- Updates rond kickoff, voortgang, highlights en aftermovie-fase.
- Labels: aankondiging, bouwtip, eventupdate, release notes.
- Doorlopende publicatie, ook buiten oktober.
- Content via API: `GET /api/posts` (lijst), `GET /api/posts/{slug}` (detail)

**Content features:**
- **Rich text content**: HTML markup met headings, lists, bold, italic, links
- **Multiple images**: elke post kan meerdere afbeeldingen bevatten
  - Featured image: hoofdafbeelding voor overzichtspagina
  - In-content images: inline in de tekst via editor
  - Image gallery: aparte galerij van extra foto's onder de post
- **Image attributes**: alt-text (toegankelijkheid), captions (onderschriften)
- **Tags**: filterbaar op categorie en tags
- **Code blocks**: voor technische snippets (Arduino code, configuratie)
- **Embeds**: YouTube/Vimeo video's, externe content

### 6.6 Resources
Aparte, prominente pagina met linkenshub.

Must-have links:
- Walter's eigen sites (alle relevante projecten en kanalen).
- Dutch Robot Games (Nederlandse combat robotics competitie).
- Hackerspace Drenthe.
- Andere relevante Nederlandse en internationale combat robotics sites.
- Tools, onderdelen, documentatie.

Structuur:
- Curated lijst met korte toelichting per link.
- Vaste categorieën, zodat content jaarlijks herbruikbaar is.
- Resource-items in database-tabel `resources`.
- Tags: wallie, community, competitie, tools, onderdelen, documentatie.
- Per resource minimaal: titel, URL, korte beschrijving, eigenaar/beheerder, laatste controle-datum.
- Beheer via Laravel Filament admin.

### 6.7 Archief en toekomstige Roboktobers
- **2026 is de eerste editie** - geen archiefmateriaal beschikbaar.
- Jaarstructuur opzetten voor toekomstige edities: 2027, 2028, etc.
- Per editie bewaren: thema, programma, assets, lessons learned, foto's.
- Herbruikbare templates voor nieuwe edities.

### 6.8 Credits en Wallie Touch
- Zichtbare credits aan Walter/Wally en Hackerspace Drenthe.
- Vaste rubriek: "Tips van Wallie / Cookiemonster".
- **Beheer**: Walter/Wally en organisatie samen via adminpaneel.
- Persoonlijke toon: korte praktijkadviezen, valkuilen, snelle fixes.

### 6.9 Aanmeldpagina (Team en Robot registratie)
- Duidelijke uitleg over teamsamenstelling (min. 1 volwassene, optioneel kinderen).
- Formulier met velden:
  - Teamnaam (verplicht)
  - Contactpersoon naam (verplicht)
  - Email (verplicht)
  - Aantal volwassenen (verplicht, min. 1)
  - Aantal kinderen (optioneel)
  - Aantal robots dat je wilt maken (indicatief)
  - Ervaring (beginner / gevorderd)
  - Opmerkingen
- Bevestigingspagina na verzenden.
- Link naar overzichtspagina met geregistreerde teams.
- Registratie via API: `POST /api/teams`

### 6.10 Teams en Robots overzicht
- Publieke pagina met alle geregistreerde teams.
- Per team: naam, aantal robots, status.
- Per robot: naam, gewichtsklasse, foto (indien beschikbaar).
- Data via API: `GET /api/teams` en `GET /api/teams/{id}/robots`

### 6.11 Battle Aanmelding (laatste weken)
- Apart formulier voor battle-inschrijving.
- Team selecteert welke robot(s) meedoen.
- Bevestiging dat robot voldoet aan technische eisen.
- Battle-schema wordt later gepubliceerd op basis van aanmeldingen.
- Battle-aanmelding via API: `POST /api/battles` (of via admin voor team)

## 7. Functionele eisen
- Heldere navigatie tussen Teaser, Programma, Aanmelden, Build Hub, Teams en Archief.
- Team en robot registratieformulier met validatie (frontend + backend).
- Publiek overzicht van geregistreerde teams en robots via API.
- Battle-aanmeldingsformulier (actief in laatste weken).
- Downloadcentrum met versiebeheer voor STL en documentatie via Asset API.
- Filterbare BOM (categorie, beschikbaarheid, prijsrange) in frontend.
- Blog met rich text content, meerdere afbeeldingen, tags en zoekfunctie via Posts API.
- Linkhub op aparte Resources-pagina via Resources API.
- Laravel Filament adminpaneel voor contentbeheer.
- RESTful API endpoints voor alle publieke en admin-functionaliteit.
- Authenticatie voor admin via Laravel Sanctum.
- Email-notificatie bij nieuwe teamaanmeldingen.
- Database-backup strategie (automatisch + export naar Git).
- **Visuele content management**:
  - Image upload met preview in adminpaneel
  - Ondersteuning voor foto's, video's, 3D-modellen (STL)
  - Alt-text verplicht voor toegankelijkheid
  - Automatische thumbnail-generatie
  - Video-embeds (YouTube/Vimeo) voor tutorials
- **Educatieve features**:
  - Stap-voor-stap wizards met voortgangsindicatie
  - Tooltips/popups voor vaktermen
  - Downloadbare checklists (PDF-generatie)
  - Print-vriendelijke versie van bouwbeschrijving

## 8. Niet-functionele eisen
- Mobiel eerst (deelnemers gebruiken vaak telefoon in de werkplaats).
- Snelle laadtijd, ook op matige verbindingen:
  - API response caching (Laravel cache)
  - Frontend code splitting en lazy loading
  - CDN voor static assets (optioneel)
- Toegankelijkheid minimaal WCAG 2.2 AA op kernflows.
- **Leesbaarheid**: tekst minimaal 16px, hoog contrast, duidelijke koppen.
- **Taalgebruik**: B1-niveau (middelbare school), simpele zinnen, vaktermen uitgelegd.
- **Visuele hiërarchie**: belangrijkste informatie eerst, scanbare layout.
- **Educatieve UX**: progressie-indicatie, duidelijke next-steps, positieve feedback.
- Betrouwbare hosting en eenvoudige content updates door organisatie via adminpaneel.
- Back-upstrategie:
  - Database: dagelijkse automated backup
  - Assets (STL/BOM): in Laravel storage + Git LFS (optioneel)
  - Code: Git repositories (backend + frontend)
- Reproduceerbare deploy vanuit Git bij iedere release.
- API rate limiting voor publieke endpoints.
- CORS configuratie voor frontend-backend communicatie.
- **Code kwaliteit en standaarden**:
  - PHP 8.2+ met strict types
  - PHPStan level 9 (maximum static analysis)
  - Laravel Pint / PHP-CS-Fixer voor code style (PSR-12)
  - Rector voor geautomatiseerde refactoring en upgrades
  - 100% PHPDoc coverage (alle classes, methods, properties)
  - Security audit met Laravel best practices
  - WCAG 2.2 AA compliance voor alle frontend components
  - Geautomatiseerde CI/CD pipeline met kwaliteitschecks

## 8.1 Code kwaliteitsstandaarden (verplicht)

### PHP Backend (Laravel)
**Static Analysis:**
- **PHPStan Level 9**: Hoogste level, geen mixed types toegestaan
- **Larastan**: Laravel-specifieke PHPStan rules
- **Psalm**: Aanvullende type checking (optioneel)

**Code Style:**
- **Laravel Pint**: Officiële Laravel code formatter (PSR-12 + Laravel conventions)
- **PHP-CS-Fixer**: Alternatief voor strikte PSR-12 compliance
- Pre-commit hooks voor automatische formatting

**Refactoring & Modernization:**
- **Rector**: Geautomatiseerde code upgrades en refactoring
- PHP 8.2+ features gebruiken waar mogelijk (readonly properties, enums)

**Security:**
- **Laravel Security Scanner**: Enlightn of Laravel Security Checker
- **Dependency scanning**: Composer audit voor kwetsbare packages
- **OWASP Top 10** compliance
- SQL injection preventie: alleen Eloquent/Query Builder, geen raw queries
- XSS preventie: Blade escaping, CSP headers
- CSRF tokens op alle state-changing endpoints
- Rate limiting op alle publieke endpoints
- Input validation via Form Requests
- Output sanitization in API Resources

**Testing:**
- **PHPUnit**: Unit tests met minimaal 80% coverage
- **Pest**: Moderne testing (optioneel, maar aanbevolen)
- Feature tests voor alle API endpoints
- Browser tests met Laravel Dusk voor kritieke flows

**Documentatie:**
- **PHPDoc voor alle code**:
  ```php
  /**
   * Register a new team for Roboktober event.
   *
   * Validates team data, stores in database, and sends notification email.
   * Follows Single Responsibility Principle - only handles registration logic.
   *
   * @param  TeamRegistrationRequest  $request  Validated team registration data
   * @return JsonResponse Team data with 201 status
   *
   * @throws ValidationException When team data is invalid
   * @throws \Exception When email notification fails
   *
   * @see TeamRegistrationRequest For validation rules
   * @see TeamRegisteredNotification For email notification
   */
  public function store(TeamRegistrationRequest $request): JsonResponse
  {
      // Implementation
  }
  ```
- Inline comments voor complexe logica
- README.md per module/package
- API documentatie via Laravel Scribe/Scramble
- Architecture Decision Records (ADR) voor belangrijke keuzes

### Frontend (Vue/React)
**Code Quality:**
- **ESLint**: Strikte rules (Airbnb of Standard style)
- **TypeScript**: Verplicht voor type safety (geen `any` types)
- **Prettier**: Code formatting

**Accessibility (WCAG 2.2 AA):**
- **axe-core**: Geautomatiseerde accessibility testing
- **ARIA labels**: Op alle interactieve elementen
- **Keyboard navigation**: Alle functionaliteit toegankelijk zonder muis
- **Screen reader testing**: Met NVDA of JAWS
- **Color contrast**: Minimaal 4.5:1 voor normale tekst
- **Focus indicators**: Zichtbaar op alle focusable elementen
- **Alt text**: Verplicht op alle afbeeldingen
- **Semantic HTML**: Correcte heading hierarchy (h1→h2→h3)
- **Form labels**: Expliciet gekoppeld aan inputs

**Testing:**
- **Vitest** of **Jest**: Unit tests
- **Vue Test Utils** / **React Testing Library**: Component tests
- **Cypress** of **Playwright**: E2E tests

**Documentatie:**
- JSDoc voor alle functions
- Component documentation (Storybook optioneel)
- Props/events gedocumenteerd

### CI/CD Pipeline Requirements
**Pre-commit:**
- PHP Pint/CS-Fixer formatting
- ESLint + Prettier frontend

**CI Pipeline (GitHub Actions / GitLab CI):**
1. **Static Analysis**:
   - PHPStan level 9
   - Larastan
   - ESLint
   - TypeScript compiler
2. **Tests**:
   - PHPUnit (backend)
   - Pest (backend)
   - Vitest/Jest (frontend)
   - E2E tests (subset, niet alle)
3. **Security**:
   - Composer audit
   - npm audit
   - OWASP dependency check
4. **Code Coverage**:
   - Minimaal 80% backend
   - Minimaal 70% frontend
5. **Build**:
   - Laravel octane/opcache warmup
   - Frontend production build
6. **Accessibility**:
   - axe-core scan op key pages

**Deployment Gates:**
- Alle CI checks moeten slagen (geen warnings)
- Code review verplicht (minimaal 1 approval)
- Security scan passed
- Performance budget (<3s First Contentful Paint)

## 9. SOLID principes in Laravel implementatie

### 1. Single Responsibility Principle (SRP)
**Principe**: Elke class heeft één verantwoordelijkheid en één reden om te veranderen.

**Laravel implementatie:**
```php
// ❌ FOUT: Controller doet te veel
class TeamController extends Controller
{
    public function store(Request $request)
    {
        // Validatie
        $validated = $request->validate([...]);
        
        // Database opslag
        $team = Team::create($validated);
        
        // Email verzenden
        Mail::to($team->email)->send(new TeamRegistered($team));
        
        // Logging
        Log::info('Team registered', ['team_id' => $team->id]);
        
        return response()->json($team);
    }
}

// ✅ GOED: Gescheiden verantwoordelijkheden
/**
 * Handle team registration HTTP requests.
 * 
 * Responsibility: HTTP layer - validate input, return response.
 * Does NOT handle business logic or side effects.
 */
class TeamController extends Controller
{
    public function __construct(
        private readonly TeamRegistrationService $registrationService
    ) {}
    
    /**
     * Register new team via API.
     * 
     * @param TeamRegistrationRequest $request Validated request (SRP: validation)
     * @return JsonResponse
     */
    public function store(TeamRegistrationRequest $request): JsonResponse
    {
        $team = $this->registrationService->register($request->validated());
        
        return new TeamResource($team);
    }
}

/**
 * Team registration business logic service.
 * 
 * Responsibility: Orchestrate team registration process.
 */
class TeamRegistrationService
{
    public function __construct(
        private readonly TeamRepository $repository,
        private readonly NotificationService $notifications,
        private readonly AuditLogger $logger
    ) {}
    
    /**
     * Register team and trigger side effects.
     */
    public function register(array $data): Team
    {
        DB::beginTransaction();
        
        try {
            $team = $this->repository->create($data);
            $this->notifications->sendTeamRegistered($team);
            $this->logger->logTeamRegistration($team);
            
            DB::commit();
            return $team;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
```

### 2. Open/Closed Principle (OCP)
**Principe**: Open voor extensie, gesloten voor modificatie.

**Laravel implementatie:**
```php
// ✅ GOED: Strategy pattern voor verschillende notificatie types
interface NotificationChannel
{
    public function send(Team $team, string $message): void;
}

class EmailNotificationChannel implements NotificationChannel
{
    public function send(Team $team, string $message): void
    {
        Mail::to($team->email)->send(new GenericNotification($message));
    }
}

class SlackNotificationChannel implements NotificationChannel
{
    public function send(Team $team, string $message): void
    {
        // Slack webhook
    }
}

/**
 * Notification service - extensible without modification.
 */
class NotificationService
{
    /** @var array<NotificationChannel> */
    private array $channels = [];
    
    public function addChannel(NotificationChannel $channel): void
    {
        $this->channels[] = $channel;
    }
    
    public function sendTeamRegistered(Team $team): void
    {
        $message = "New team registered: {$team->naam}";
        
        foreach ($this->channels as $channel) {
            $channel->send($team, $message);
        }
    }
}
```

### 3. Liskov Substitution Principle (LSP)
**Principe**: Subtypes moeten substitueerbaar zijn voor hun base types.

**Laravel implementatie:**
```php
// ✅ GOED: Content types volgen zelfde contract
interface Publishable
{
    public function publish(): void;
    public function unpublish(): void;
    public function isPublished(): bool;
}

class Post extends Model implements Publishable
{
    public function publish(): void
    {
        $this->update(['is_published' => true, 'published_at' => now()]);
    }
    
    public function unpublish(): void
    {
        $this->update(['is_published' => false]);
    }
    
    public function isPublished(): bool
    {
        return $this->is_published && $this->published_at?->isPast();
    }
}

class Page extends Model implements Publishable
{
    // Zelfde interface, verschillende implementatie details
    public function publish(): void { /* ... */ }
    public function unpublish(): void { /* ... */ }
    public function isPublished(): bool { /* ... */ }
}

/**
 * Content publisher - werkt met elk Publishable type.
 */
class ContentPublisher
{
    /**
     * Publish any publishable content.
     * 
     * @param Publishable $content Any content implementing Publishable
     */
    public function publishContent(Publishable $content): void
    {
        $content->publish(); // Werkt voor Post, Page, etc.
    }
}
```

### 4. Interface Segregation Principle (ISP)
**Principe**: Clients moeten niet afhangen van interfaces die ze niet gebruiken.

**Laravel implementatie:**
```php
// ❌ FOUT: Fat interface
interface ContentRepository
{
    public function create(array $data): Model;
    public function update(int $id, array $data): Model;
    public function delete(int $id): bool;
    public function find(int $id): ?Model;
    public function all(): Collection;
    public function paginate(int $perPage): LengthAwarePaginator;
    public function search(string $query): Collection;
    public function export(): string;
    public function import(string $data): void;
}

// ✅ GOED: Gesegregeerde interfaces
interface ReadableRepository
{
    public function find(int $id): ?Model;
    public function all(): Collection;
}

interface WritableRepository
{
    public function create(array $data): Model;
    public function update(int $id, array $data): Model;
    public function delete(int $id): bool;
}

interface SearchableRepository
{
    public function search(string $query): Collection;
}

// Public API gebruikt alleen read
class PublicTeamController
{
    public function __construct(
        private readonly ReadableRepository $teams
    ) {}
}

// Admin API gebruikt read + write
class AdminTeamController
{
    public function __construct(
        private readonly ReadableRepository & WritableRepository $teams
    ) {}
}
```

### 5. Dependency Inversion Principle (DIP)
**Principe**: Afhangen van abstracties, niet van concrete implementaties.

**Laravel implementatie:**
```php
// ❌ FOUT: Direct afhankelijk van Eloquent
class TeamService
{
    public function getApprovedTeams(): Collection
    {
        return Team::where('status', 'approved')->get(); // Tight coupling
    }
}

// ✅ GOED: Afhankelijk van abstractie
interface TeamRepositoryInterface
{
    /**
     * Get all approved teams.
     * 
     * @return Collection<int, Team>
     */
    public function getApproved(): Collection;
}

class EloquentTeamRepository implements TeamRepositoryInterface
{
    public function getApproved(): Collection
    {
        return Team::where('status', 'approved')
            ->with('robots')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

/**
 * Service depends on abstraction, not concrete implementation.
 */
class TeamService
{
    public function __construct(
        private readonly TeamRepositoryInterface $repository
    ) {}
    
    public function getApprovedTeams(): Collection
    {
        return $this->repository->getApproved();
    }
}

// Service Provider binding
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TeamRepositoryInterface::class,
            EloquentTeamRepository::class
        );
    }
}
```

**Modules gescheiden:**
- **Controllers**: HTTP layer, geen business logic
- **Services**: Business logic orchestration
- **Repositories**: Data access abstraction
- **Models**: Data structures + relationships
- **Requests**: Input validation
- **Resources**: Output transformation
- **Notifications**: Side effects
- **Jobs**: Async processing

## 10. PDCA voor Roboktober-cyclus

### Plan (voor september)
**Project planning:**
- Doel per editie vastleggen (instroom, bouwresultaten, community-groei).
- Contentkalender maken: teaser, kickoff, weekupdates, afsluiting.
- Assetplanning: BOM-v1, STL-v1, bouwdoc-v1, 3D-model-v1.

**Code kwaliteit planning:**
- CI/CD pipeline opzetten (PHPStan, tests, security scans)
- Code review proces definiëren
- Documentatie templates maken (PHPDoc, ADR)
- Accessibility audit tooling configureren
- Performance budgets vaststellen

### Do (oktober uitvoering)
**Content:**
- Wekelijkse updates publiceren via adminpaneel.
- Build Hub actief onderhouden met fixes en versie-updates.
- Wallie tips periodiek toevoegen via adminpaneel.
- Teamaanmeldingen verwerken en goedkeuren via adminpaneel.
- Teams en robots overzicht blijft automatisch actueel via database.
- Battle-aanmeldingen activeren in laatste weken.
- Alle updates direct zichtbaar na publicatie in adminpaneel.

**Code kwaliteit:**
- Dagelijkse CI runs monitoren
- PHPStan level behouden op 9
- Test coverage minimaal 80% houden
- Security patches direct toepassen
- Code reviews binnen 24 uur
- Performance metrics wekelijks checken
- Accessibility issues direct fixen

### Check (eind oktober)
**Event metrics:**
- Meten: bezoekers, downloads, deelname, veelgestelde vragen.
- Feedback ophalen van deelnemers en begeleiders.
- Evalueren welke bouwstappen problemen gaven.

**Code kwaliteit metrics:**
- PHPStan: level 9 maintained? Errors/warnings count
- Test coverage: backend ≥80%, frontend ≥70%?
- Security: vulnerabilities found and fixed count
- Performance: Core Web Vitals scores
  - LCP (Largest Contentful Paint) <2.5s
  - FID (First Input Delay) <100ms
  - CLS (Cumulative Layout Shift) <0.1
- Accessibility: WCAG 2.2 AA violations count (target: 0)
- Code review: average time to review, approval rate
- CI/CD: build success rate, deployment frequency

### Act (november en verder)
**Content en proces:**
- Verbeteringen verwerken in templates en docs.
- Archief publiceren en editie afsluiten.
- Basis klaarzetten voor volgende Roboktober-editie.

**Code kwaliteit verbeteringen:**
- Technical debt items identificeren en plannen
- Refactoring opportunities documenteren (ADRs)
- Dependency updates plannen (security patches prioriteit)
- Performance optimalisaties implementeren
- Accessibility audit bevindingen verwerken
- Testing gaps identificeren en opvullen
- Documentation gaps aanvullen
- CI/CD pipeline optimaliseren (sneller, betrouwbaarder)
- Lessons learned documenteren voor volgende editie

## 11. KPI's
**Event metrics:**
- Teaserperiode: minimaal 500 unieke bezoekers.
- Teamregistratie: minimaal 8 teams aangemeld voor kickoff.
- Robotregistratie: minimaal 12 robots geregistreerd tijdens bouwfase.
- Battle-deelname: minimaal 8 robots ingeschreven voor battle.
- Build Hub: minimaal 150 assetdownloads (BOM/STL/docs).
- Engagement: minimaal 8 nieuwsupdates in oktober.
- Hergebruik: alle content van editie binnen 2 weken in archiefvorm beschikbaar.

**Code kwaliteit KPI's:**
- PHPStan: level 9 maintained (0 errors)
- Test coverage: backend ≥80%, frontend ≥70%
- Security vulnerabilities: 0 high/critical
- Accessibility: WCAG 2.2 AA compliance (0 violations)
- Performance: Core Web Vitals
  - LCP <2.5s (Largest Contentful Paint)
  - FID <100ms (First Input Delay)
  - CLS <0.1 (Cumulative Layout Shift)
- Code review: <24h average response time
- CI/CD: >95% build success rate
- Documentation: 100% PHPDoc coverage
- Technical debt ratio: <5%

## 12. Governance en rollen
- Product owner/contenteindredactie: Walter/Wally.
- Organisatie en eventinformatie: Hackerspace Drenthe.
- **Backend developer**: Laravel API-ontwikkeling en database-migraties.
  - Verantwoordelijk voor PHPStan level 9, tests, security
  - Code reviews uitvoeren
  - PHPDoc documentatie schrijven
- **Frontend developer**: Vue/React-frontend ontwikkeling.
  - Verantwoordelijk voor TypeScript, ESLint, accessibility
  - WCAG 2.2 AA compliance waarborgen
  - Component documentatie schrijven
- **DevOps/CI maintainer**: CI/CD pipeline beheren, deployment.
  - Static analysis tools configureren
  - Security scanning opzetten
  - Performance monitoring
- **Code reviewer**: Minimaal 1 approval vereist voor merge.
  - Checken op SOLID principes
  - Security review
  - Documentation completeness
- Contentbeheer: organizers via Laravel Filament adminpaneel.
- Teamregistratiebeheer: automatisch in database, organizer goedkeurt via admin.
- Battle-coordinator: beheert battle-aanmeldingen en planning via adminpaneel.
- Publicatieritme oktober: 2 updates per week minimaal (via adminpaneel).

## 13. Concrete backlog (ontwikkelingsfasen)

### Fase 0: Setup en kwaliteitstools (week 1-2)
**Backend:**
1. Laravel 11.x nieuw project opzetten (PHP 8.2+, strict types)
2. Database configuratie (MySQL/PostgreSQL)
3. Laravel Filament 3.x installeren en configureren
4. Laravel Sanctum installeren voor API-authenticatie
5. Git repository setup (`roboktober-api`)
6. Basis API-structuur opzetten (routes, controllers, middleware)
7. **Code kwaliteitstools installeren en configureren**:
   - PHPStan level 9 + Larastan
   - Laravel Pint (code style)
   - Rector (refactoring)
   - PHPUnit + Pest
   - Laravel Scribe/Scramble (API docs)
8. **CI/CD pipeline opzetten**:
   - GitHub Actions of GitLab CI
   - Static analysis (PHPStan)
   - Tests (PHPUnit/Pest)
   - Security scan (composer audit)
   - Code coverage report
9. **Pre-commit hooks**:
   - Laravel Pint formatting
   - PHPStan check
10. **PHPDoc templates en standards documenteren**

**Frontend:**
11. Frontend-project opzetten (Vue 3 + TypeScript + Vite)
12. Git repository setup (`roboktober-frontend`)
13. **Kwaliteitstools configureren**:
    - ESLint (Airbnb/Standard)
    - Prettier
    - TypeScript strict mode
    - Vitest (unit tests)
    - Cypress/Playwright (E2E)
    - axe-core (accessibility)
14. Axios configureren voor API-communicatie
15. Router en basisnavigatie opzetten
16. Responsive layout en design system (Tailwind CSS)
17. **Accessibility setup**:
    - ARIA templates
    - Focus management
    - Keyboard navigation patterns
18. **CI/CD voor frontend**:
    - ESLint + TypeScript checks
    - Tests
    - Build verification
    - Accessibility scan

**Infrastructuur:**
19. Server PHP-versie (8.2+) en database controleren
20. CORS configuratie voor API
21. Deployment-script maken voor backend en frontend
22. **Monitoring setup**:
    - Error tracking (Sentry/Bugsnag)
    - Performance monitoring (New Relic/Scout)
    - Uptime monitoring

### Fase 1: Core API en Admin met SOLID & kwaliteit (week 3-5)
**Backend:**
1. **Database-migraties maken** (volledige PHPDoc):
   - users, teams, robots, battle_registrations
   - pages, posts, post_images (gallery support), resources, assets
   - Foreign keys, indexes, constraints gedocumenteerd
   ```php
   /**
    * Create teams table for roboktober event registrations.
    * 
    * Stores team information including contact details and status.
    * Related to robots (1:N) and battle_registrations (1:N).
    * 
    * @return void
    */
   public function up(): void
   {
       Schema::create('teams', function (Blueprint $table) {
           $table->id();
           // ... rest van migration
       });
   }
   ```

2. **Eloquent Models met PHPDoc en relaties** (SOLID: SRP):
   ```php
   /**
    * Team model representing registered roboktober teams.
    * 
    * Responsibilities:
    * - Data structure for teams
    * - Relationships to robots and battle registrations
    * - Query scopes for approved/pending teams
    * 
    * @property int $id
    * @property string $naam Team name
    * @property string $email Contact email
    * @property TeamStatus $status Approval status
    * @property Carbon $created_at
    * 
    * @method static Builder approved() Get only approved teams
    * @method static Builder pending() Get only pending teams
    * 
    * @package App\\Models
    */
   class Team extends Model
   {
       /**
        * Get robots belonging to this team.
        * 
        * @return HasMany<Robot>
        */
       public function robots(): HasMany
       {
           return $this->hasMany(Robot::class);
       }
   }
   ```

3. **Repository Pattern** (SOLID: DIP - Dependency Inversion):
   ```php
   /**
    * Team repository interface.
    * 
    * Abstracts data access logic from business logic.
    * Implementations can be swapped (Eloquent, Cache, API).
    */
   interface TeamRepositoryInterface
   {
       /**
        * Find team by ID with robots eager loaded.
        * 
        * @param int $id Team identifier
        * @return Team|null
        */
       public function findWithRobots(int $id): ?Team;
       
       /**
        * Get all approved teams.
        * 
        * @return Collection<int, Team>
        */
       public function getApproved(): Collection;
   }
   ```

4. **Service Layer** (SOLID: SRP - Single Responsibility):
   ```php
   /**
    * Team registration service.
    * 
    * Orchestrates team registration process including:
    * - Data persistence
    * - Email notifications
    * - Audit logging
    * 
    * @package App\\Services
    */
   class TeamRegistrationService
   {
       public function __construct(
           private readonly TeamRepositoryInterface $repository,
           private readonly NotificationService $notifications,
           private readonly AuditLogger $logger
       ) {}\n       \n       /**
        * Register new team for roboktober.
        * 
        * @param array{naam: string, email: string, ...} $data Validated team data
        * @return Team Created team instance
        * 
        * @throws \\Exception When database transaction fails
        */
       public function register(array $data): Team
       {
           // Implementation with transaction
       }
   }
   ```

5. **API Controllers** (SOLID: SRP - HTTP layer only):
   ```php
   /**
    * Public team API controller.
    * 
    * Responsibility: Handle HTTP requests/responses for teams.
    * Does NOT contain business logic (delegated to service).
    * 
    * @package App\\Http\\Controllers\\Api
    */
   class TeamController extends Controller
   {
       public function __construct(
           private readonly TeamRegistrationService $service
       ) {}\n       \n       /**
        * Register new team via API.
        * 
        * @param TeamRegistrationRequest $request Validated request
        * @return JsonResponse
        * 
        * @response 201 {"data": {"id": 1, "naam": "...", ...}}
        * @response 422 {"message": "Validation failed", "errors": {...}}
        */
       public function store(TeamRegistrationRequest $request): JsonResponse
       {
           $team = $this->service->register($request->validated());
           return new TeamResource($team);\n       }
   }
   ```

6. **Form Request Validation** (SOLID: SRP):
   ```php
   /**
    * Team registration validation rules.
    * 
    * @package App\\Http\\Requests
    */
   class TeamRegistrationRequest extends FormRequest
   {
       /**
        * Get validation rules.
        * 
        * @return array<string, array<string>>
        */
       public function rules(): array
       {
           return [
               'naam' => ['required', 'string', 'max:255'],
               'email' => ['required', 'email', 'unique:teams,email'],
               // ...
           ];
       }
   }
   ```

7. **API Resources** (SOLID: SRP - Output transformation):
   ```php
   /**
    * Team API resource transformer.
    * 
    * @package App\\Http\\Resources
    */
   class TeamResource extends JsonResource
   {
       /**
        * Transform team to array for API response.
        * 
        * @param Request $request
        * @return array<string, mixed>
        */
       public function toArray(Request $request): array
       {
           return [
               'id' => $this->id,
               'naam' => $this->naam,
               // ...
           ];
       }
   }
   ```

8. **Laravel Filament Resources** met validatie:
   - TeamResource (met approve/reject actions)
   - RobotResource, PageResource
   - **PostResource** (RichEditor, FileUpload, Repeater)
   - ResourceResource, AssetResource
   - Alle fields met helper text en validation rules

9. **Tests schrijven** (80% coverage minimum):
   ```php
   /**
    * Team registration API tests.
    * 
    * @package Tests\\Feature\\Api
    */
   class TeamRegistrationTest extends TestCase
   {
       /**
        * Test successful team registration.
        * 
        * @return void
        */
       public function test_team_can_register_successfully(): void
       {
           $data = ['naam' => 'Test Team', 'email' => 'test@example.com'];\n           \n           $response = $this->postJson('/api/teams', $data);\n           \n           $response->assertStatus(201)\n               ->assertJsonStructure(['data' => ['id', 'naam', 'email']]);\n           \n           $this->assertDatabaseHas('teams', ['naam' => 'Test Team']);\n       }\n       \n       /**
        * Test validation fails with invalid email.
        * 
        * @return void
        */
       public function test_registration_fails_with_invalid_email(): void
       {
           $response = $this->postJson('/api/teams', ['email' => 'invalid']);\n           \n           $response->assertStatus(422)\n               ->assertJsonValidationErrors(['email']);\n       }
   }
   ```

10. **Seeders met voorbeelddata**
11. **PHPStan check** (moet level 9 passeren)
12. **Security review** (OWASP checklist)

**Frontend:**
13. **Service laag met TypeScript**:
   ```typescript
   /**
    * Team API service.
    * 
    * Handles all team-related API calls.
    */
   interface Team {
     id: number;
     naam: string;
     email: string;
     status: 'pending' | 'approved' | 'rejected';
   }\n   \n   /**
    * Get all approved teams.
    * 
    * @returns Promise with teams array
    * @throws ApiError When request fails
    */
   export async function getTeams(): Promise<Team[]> {
     const response = await api.get<{ data: Team[] }>('/teams');
     return response.data.data;
   }
   ```

14. **Components met accessibility**:
   ```vue
   <!--
    Home page component.
    
    Displays event teaser with countdown and CTA buttons.
    Fully keyboard accessible, WCAG 2.2 AA compliant.
   -->
   <template>
     <main role=\"main\" aria-label=\"roboktober homepage\">
       <h1 id=\"page-title\">roboktober 2026</h1>
       <!-- ... -->
     </main>
   </template>
   ```

15. **Component tests** met Testing Library:
   ```typescript
   /**
    * Home page component tests.
    */
   describe('HomePage', () => {
     it('renders heading correctly', () => {
       const { getByRole } = render(HomePage);
       expect(getByRole('heading', { level: 1 })).toHaveTextContent('roboktober 2026');
     });
     
     it('is keyboard accessible', async () => {
       const { getByRole } = render(HomePage);
       const button = getByRole('button', { name: 'Doe mee' });
       \n       button.focus();
       expect(document.activeElement).toBe(button);
     });
   });
   ```

16. Resources-pagina met links van API
17. **Accessibility audit** met axe-core

### Fase 2: Registraties, Downloads & Security (week 6-7)
**Backend:**
1. **Team registratie-endpoint** (volledige PHPDoc, tests):
   - Input sanitization (XSS preventie)
   - Rate limiting (max 5 aanmeldingen per IP per uur)
   - CSRF token validatie
   - Email verificatie met signed URLs
2. **Email-notificatie** met Queue:
   ```php
   /**
    * Send team registration notification.
    * 
    * Queued job to prevent blocking HTTP response.
    * 
    * @package App\Jobs
    */
   class SendTeamRegistrationNotification implements ShouldQueue
   {
       // Implementation
   }
   ```
3. **Asset upload met security**:
   - MIME type validation (geen executables)
   - File size limits (max 50MB voor STL)
   - Virus scanning (ClamAV optioneel)
   - Storage in private disk, serve via signed URLs
4. **Asset download tracking** (analytics):
   - Download counter met database transaction
   - User agent logging (geen PII)
5. **Battle-aanmelding endpoints** met business rules:
   - Validatie: robot moet goedgekeurd zijn
   - Validatie: robot moet aan gewichtsklasse voldoen
   - Geen duplicate aanmeldingen
6. **Security audit** (OWASP Top 10):
   - SQL injection check (geen raw queries)
   - XSS preventie (Blade escaping)
   - CSRF tokens op alle POST/PUT/DELETE
   - Authentication bypass attempts logging
7. **PHPStan niveau 9** check (0 errors)
8. **Tests uitbreiden** (coverage ≥80%):
   - Integration tests voor registratie flow
   - Security tests (invalid input, injection attempts)
   - Performance tests (N+1 query detection)

**Frontend:**
9. **Aanmeldformulier** (TypeScript, fully accessible):
   ```typescript
   /**
    * Team registration form component.
    * 
    * WCAG 2.2 AA compliant with:
    * - ARIA labels on all inputs
    * - Error announcements for screen readers
    * - Keyboard navigation support
    */
   ```
   - Client-side validatie met duidelijke foutmeldingen
   - ARIA live regions voor form errors
   - Focus management (error naar eerste fout)
   - Loading states tijdens submit
10. **Teams-overzichtspagina**:
    - Keyboard navigable filters
    - ARIA labels op search/filter controls
    - Empty state messaging
11. **Build Hub** met downloads:
    - Download buttons met ARIA labels
    - File size en type weergave
    - Versie-geschiedenis accessible table
12. **3D Viewer** (accessible alternative):
    - Keyboard controls (rotate, zoom)
    - Alternative text description van model
    - Fallback voor browsers zonder WebGL
13. **Battle-aanmeldingsformulier**
14. **Educatieve features** (alle WCAG compliant):
    - Stap-voor-stap wizard met progressie ARIA
    - Tooltips met role="tooltip" en aria-describedby
    - Image lightbox met keyboard close (ESC)
    - Video-embeds met captions support
    - Print CSS met hidden nav/footer
15. **Component tests** (≥70% coverage):
    - Form validation tests
    - Accessibility tests met axe-core
    - User interaction tests (click, type, submit)
16. **E2E test** voor registratie flow

### Fase 3: Content, educatie, polish & accessibility (week 8-10)
**Backend:**
1. **Blog/nieuws API** (PHPDoc, cached):
   - Paginering met cursor-based (performance)
   - Category filtering met query scope
   - Tag searching met full-text index
   - Cache strategy (Redis, 5 min TTL)
2. **Full-text search** (Laravel Scout):
   - Algolia of Meilisearch configuratie
   - Searchable attributes: titel, content, tags
   - Highlight matching terms in results
3. **API rate limiting** (per endpoint):
   - Public: 60 requests/min per IP
   - Admin: 120 requests/min per user
   - Throttle exceptions logged
4. **Performance optimalisatie**:
   - Eager loading checklist (N+1 preventie)
   - Database index optimization
   - Query optimization (EXPLAIN analysis)
   - Redis cache voor frequently accessed data
   - Image optimization pipeline (intervention/image)
5. **Database backup** geautomatiseerd:
   - Daily automated backups
   - Offsite storage (S3/Backblaze)
   - Restore test weekly
6. **Rector refactoring** (code modernization)
7. **PHPStan level 9** maintained
8. **Security scan** (composer audit)

**Frontend:**
9. **Nieuws/blog pages** (WCAG 2.2 AA compliant):
   - Semantic HTML (article, time, heading hierarchy)
   - Skip links voor keyboard users
   - Focus indicators zichtbaar (outline 2px)
   ```vue
   <!--
    Blog post detail component.
    
    Accessibility features:
    - Semantic article structure
    - Time element with datetime attribute
    - Alt text op alle images
    - ARIA labels op interactive elements
   -->
   <template>
     <article>
       <h1 id="post-title">{{ post.titel }}</h1>
       <time :datetime="post.published_at">
         {{ formatDate(post.published_at) }}
       </time>
       <!-- Rich HTML content -->
       <div v-html="post.content" aria-labelledby="post-title"></div>
     </article>
   </template>
   ```
10. **Zoekfunctionaliteit**:
    - Search input met aria-label
    - Results announced to screen readers
    - Keyboard navigation door results
11. **Educatieve features** (gedocumenteerd):
    - Interactieve infographics (SVG met ARIA)
    - Foto-annotaties (accessible tooltips)
    - Glossary met keyboard navigation
    - PDF-generatie (jsPDF met toegankelijke output)
    - Moeilijkheidsgraad visueel + ARIA label
12. **Visuele optimalisatie**:
    - Image lazy loading (native loading="lazy")
    - Responsive images (srcset voor 3 breakpoints)
    - Alt-text verification (geen lege alts)
    - ARIA-labels op decorative images (role="presentation")
13. **Error handling patterns**:
    - User-friendly error messages (Nederlands, B1-niveau)
    - Error boundary components
    - Network error retry logic
    - Loading skeletons (niet alleen spinners)
14. **Accessibility audit** (geautomatiseerd + manueel):
    - axe-core in CI pipeline (0 violations)
    - NVDA/JAWS screen reader testing
    - Keyboard-only navigation testing
    - Color contrast verification (4.5:1 minimum)
    - Focus order logical
15. **Leesbaarheid audit**:
    - Tekst minimaal 16px (1rem base)
    - Line-height 1.5 voor body text
    - Heading sizes duidelijk verschillend
    - Contrast-ratio check (WebAIM tool)
    - Flesch-Douma reading score voor B1
16. **Mobile responsiveness**:
    - Touch targets minimaal 44x44px
    - Geen horizontal scrolling
    - Responsive tables (stack on mobile)
    - Mobile performance budget (3s LCP)
17. **SEO optimalisatie**:
    - Meta tags (title, description)
    - Open Graph tags (social sharing)
    - Structured data (JSON-LD)
    - Sitemap.xml generatie
    - Robots.txt configuratie
18. **Performance budget enforcement**:
    - Lighthouse CI in pipeline
    - Bundle size limit (500KB max)
    - Image size limit (200KB max per image)
    - FCP <1.8s, LCP <2.5s, CLS <0.1
19. **Component documentation** (Storybook optioneel)
20. **E2E tests** voor kritieke flows:
    - Search en navigatie
    - Form submissions
    - Blog post lezen
21. Credits-pagina met Wallie Touch

### Fase 4: Testing, security audit & deployment (week 11-12)

**Kwaliteitscontrole (verplicht voor launch):**
1. **PHPStan level 9** final check:
   - 0 errors, 0 warnings
   - Alle @var, @param, @return tags correct
   - Geen mixed types
2. **Test coverage verificatie**:
   - Backend: ≥80% (PHPUnit coverage report)
   - Frontend: ≥70% (Vitest coverage report)
   - Critical paths: 100% (auth, registration, payments)
3. **End-to-end testing** (Cypress/Playwright):
   - Happy path scenarios:
     * Team registratie + goedkeuring
     * Asset download
     * Blog post lezen
     * Battle aanmelding
   - Error scenarios:
     * Invalid form input
     * Network failures
     * 404 pages
   - Cross-browser testing (Chrome, Firefox, Safari)
4. **Performance testing**:
   - Lighthouse audit (alle pages ≥90 score):
     * Performance: 90+
     * Accessibility: 100
     * Best Practices: 100
     * SEO: 100
   - Core Web Vitals:
     * LCP <2.5s (Largest Contentful Paint)
     * FID <100ms (First Input Delay)
     * CLS <0.1 (Cumulative Layout Shift)
   - Load testing (Apache Bench / k6):
     * 100 concurrent users
     * <500ms p95 response time
     * No errors under load
5. **Security audit** (comprehensive):
   - **OWASP Top 10 checklist**:
     * A01 - Broken Access Control: ✓ Middleware checks
     * A02 - Cryptographic Failures: ✓ HTTPS, encrypted secrets
     * A03 - Injection: ✓ Eloquent only, no raw SQL
     * A04 - Insecure Design: ✓ Threat model documented
     * A05 - Security Misconfiguration: ✓ Config reviewed
     * A06 - Vulnerable Components: ✓ Composer/npm audit
     * A07 - Auth Failures: ✓ Sanctum, rate limiting
     * A08 - Data Integrity: ✓ CSRF, signed URLs
     * A09 - Logging Failures: ✓ Centralized logging
     * A10 - SSRF: ✓ No user-controlled URLs
   - **Penetration testing**:
     * SQL injection attempts
     * XSS injection attempts
     * CSRF bypass attempts
     * Authentication bypass attempts
     * File upload vulnerabilities
   - **Dependency scanning**:
     * `composer audit` (no critical vulnerabilities)
     * `npm audit` (no critical vulnerabilities)
     * Snyk or Dependabot enabled
   - **Security headers**:
     * CSP (Content Security Policy)
     * X-Frame-Options: DENY
     * X-Content-Type-Options: nosniff
     * Strict-Transport-Security
     * Referrer-Policy
6. **Accessibility final audit** (WCAG 2.2 AA):
   - Automated: axe-core scan (0 violations)
   - Manual keyboard navigation test
   - Screen reader testing (NVDA/JAWS):
     * All forms navigable
     * All images have alt text
     * All buttons have labels
     * Heading hierarchy correct
   - Color contrast verification
   - Focus indicators present
   - ARIA attributes correct
7. **Code review** (volledige codebase):
   - SOLID principes toegepast?
   - DRY principle (geen duplicatie)
   - Naming conventions consistent
   - Comments duidelijk en relevant
   - No commented-out code
   - No TODO's zonder ticket

**Documentatie (verplicht):**
8. **API documentatie** (Laravel Scribe/Scramble):
   - Alle endpoints gedocumenteerd
   - Request/response examples
   - Authentication requirements
   - Rate limiting info
   - Error codes explained
9. **Developer documentatie**:
   - README.md met setup instructions
   - CONTRIBUTING.md met code standards
   - Architecture Decision Records (ADRs)
   - Database schema diagram
   - Deployment runbook
   - Troubleshooting guide
10. **Organizer-handleiding**:
    - Filament adminpaneel gebruik
    - Content publicatie workflow
    - Team approval proces
    - Asset management
    - Screenshots en video walkthrough
11. **User documentation** (optioneel):
    - FAQ pagina
    - Contact informatie
    - Privacy policy
    - Terms of service

**Deployment:**
12. **Staging deployment** (pre-productie):
    - Smoke tests op staging
    - Performance check
    - Accessibility check
    - Security headers verified
13. **Production deployment** (zero-downtime):
    - Database migrations (tested)
    - Asset compilation
    - Cache warming
    - Queue workers gestart
    - Health check endpoint
14. **Monitoring setup**:
    - Error tracking (Sentry/Bugsnag)
    - Performance monitoring (Scout/New Relic)
    - Uptime monitoring (Pingdom/UptimeRobot)
    - Log aggregation (Papertrail/Loggly)
    - Alerts configured (email/Slack)
15. **Backup verification**:
    - Database backup tested
    - Storage backup tested
    - Restore procedure documented
16. **SSL/TLS verificatie**:
    - Certificate installed
    - A+ rating on SSL Labs
    - HSTS enabled
    - Mixed content resolved

**Launch:**
17. **Soft launch** (beperkte groep):
    - Invite 10-20 test users
    - Monitor errors/performance
    - Collect feedback
    - Fix critical issues
18. **Feedback verwerken**:
    - Bug fixes prioriteren
    - UX improvements
    - Performance tweaks
19. **Final checks**:
    - All CI pipelines green
    - No security vulnerabilities
    - Performance budgets met
    - Accessibility compliance verified
20. **Definitieve launch** 🚀
    - Announcement via social media
    - Monitor first 24 hours closely
    - On-call developer beschikbaar

## 14. Open vragen (graag beslissen)

### ✅ Beantwoord:
- Frontend: Vue 3 + Vite (definitief)
- Server: Hackerspace Drenthe eigen server (SSH)
- Frontend hosting: build naar Laravel `public/` folder
- Robot type: combat robots (antweight / beetleweight)
- Taal: uitsluitend Nederlands
- Teamregistratie: altijd open
- Archief: 2026 is eerste editie
- BOM/STL: Walter/Wally levert aan
- Wallie Tips beheer: Walter + organisatie samen
- Locatie: volledig adres + kaart op site
- Evenement: kickoff begin oktober, battle eind oktober (zaterdag of woensdagavond)

### Prioriteit 1 (voor start development):
1. **Server-specificaties controleren** (hackerspace server):
   - PHP-versie (Laravel 11 vereist PHP 8.2+)
   - Database: MySQL 8.0+ of PostgreSQL 13+?
   - Is Composer beschikbaar?
   - Exacte SSH-toegang / deploy workflow?
2. **Domain/SSL**:
   - Heeft roboktober.nl al SSL-certificaat?
   - API-subdomain nodig (api.roboktober.nl) of alles op root?

### Prioriteit 2 (eerste sprint):
3. Moet upload/download publiek zijn of deels achter aanmelding?
4. Git-workflow: direct push op main of via pull requests (voor beide repos)?
5. Wil je bij de 3D viewer alleen kijken, of ook meten/exploded view in fase 1?
6. Moeten teams zelf hun robotgegevens kunnen updaten, of alleen via organizer/admin?
7. Database backup-frequentie: dagelijks, wekelijks?
8. Wil je real-time features (Laravel Echo + WebSockets) of is polling via API voldoende?
9. Wat is het exacte adres van Hackerspace Drenthe voor op de site?
10. Worden specifieke data voor kickoff en battle nog vastgelegd (dan verwerken in countdown)?

### Prioriteit 3 (content en visueel materiaal):
11. **Foto's**: worden er foto's gemaakt tijdens bouwsessies voor op de site?
12. **Video's**: wil je video-tutorials voor moeilijke bouwstappen?
13. **Educatief materiaal** (Walter levert BOM/STL aan):
    - Is er al een bouwhandleiding of bouwinstructie-tekst?
    - Welke bouwstappen zijn het moeilijkst (extra uitleg/video nodig)?
    - Wil je een glossary voor combat-robot termen (antweight, ESC, LiPo, etc.)?
14. **Infographics gewenst**:
    - Tijdlijn (kickoff → bouw → battle) als visueel schema?
    - Kostenoverzicht van het robot-pakket?
    - "Wat heb ik nodig" checklist (gereedschap, veiligheid)?
16. **Tone of voice Wallie tips**: technisch strak, of speels met humor?
17. **Voorbeeldcontent**: heb je referentiesites die qua toon/niveau goed zijn?

## 15. Actie-items (voor start)
1. **Server-specificaties controleren** (hackerspace server):
   - PHP 8.2+ beschikbaar?
   - MySQL 8.0+ of PostgreSQL 13+ beschikbaar?
   - Composer beschikbaar?
   - SSH deploy workflow vastleggen
   - SSL-certificaat voor roboktober.nl?
2. **Walter/Wally contacteren voor aanlevering**:
   - BOM (bill of materials)
   - STL-bestanden
   - Foto's voor visuele content
   - Wallie tips eerste set
   - Adres Hackerspace Drenthe
3. **Exacte data bevestigen**: kickoff en battle (zaterdag/woensdagavond)
4. **Frontend build** configureren voor Laravel public/ deployment
4. Inventariseren van alle Walter's sites/projecten voor Resources-database.
5. Volledige linklijst samenstellen: Dutch Robot Games, hackerspaces, tools, onderdelen-suppliers.
6. Per link: categorie, korte beschrijving en eigenaar toevoegen aan resources-seeder.
7. Teamregistratieformulier specificeren met alle benodigde velden en validatieregels.
8. Battle-aanmeldingsformulier specificeren (activeren in laatste weken oktober).
9. Content-seeder voorbereiden voor initiële pages (Home, Programma, etc.).
10. Email-templates ontwerpen voor teamaanmelding-notificaties.
11. Handleiding schrijven voor organizers: Laravel Filament adminpaneel gebruik.
12. API-documentatie genereren (Laravel Scramble of Scribe aanbevolen).
13. **Visuele content voorbereiden**:
    - Foto's verzamelen: eerdere robots, bouwstappen, events
    - Foto's annoteren: pijlen, labels, highlight-zones toevoegen
    - Video's maken/verzamelen: complexe stappen, tutorials
    - 3D-modellen klaar zetten (STL bestanden voor viewer)
14. **Educatief materiaal opstellen**:
    - Bouwhandleiding herschrijven naar B1-niveau (middelbare school)
    - Vaktermen-lijst maken voor glossary/tooltips
    - Moeilijkheidsgraad toekennen per stap
    - Tijdsindicaties per bouwstap toevoegen
    - Troubleshooting-sectie schrijven (veelgemaakte fouten)
15. **Infographics ontwerpen** (optioneel, in samenwerking met designer):
    - Evenement-tijdlijn (visueel stappenplan)
    - Kostenoverzicht (wat kost het totaal?)
    - "Wat heb ik nodig" checklist (gereedschap, materiaal)
    - Gewichtsklassen en regelgeving (visueel schema)
16. **Tone of voice documenten**:
    - Schrijfstijlgids maken (do's & don'ts)
    - Voorbeeldzinnen voor verschillende secties
    - Template voor Wallie tips (structuur, toon)