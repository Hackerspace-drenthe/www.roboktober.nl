<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * HasMedia trait — polymorphic media attachment for any Eloquent model.
 *
 * SOLID compliance:
 * - SRP: media storage is handled by the Media model; this trait only exposes relationship methods
 * - LSP: any model using this trait behaves identically from the caller's perspective
 * - ISP: callers use mediaCollectie() to request only the collection they need
 * - DIP: callers depend on this abstract trait, not on concrete mediables table queries
 *
 * Usage:
 * ```php
 * class Post extends Model {
 *     use HasMedia;
 * }
 *
 * $post->media();                      // All media attached to this post
 * $post->mediaCollectie('gallery');    // Only gallery images
 * $post->featuredMedia();              // The single featured/header image (or null)
 * ```
 *
 * @see PLAN.md §5.2 — HasMedia trait specification
 */
trait HasMedia
{
    /**
     * All media attached to this model (all collections).
     *
     * @return MorphToMany<Media, $this>
     */
    public function media(): MorphToMany
    {
        return $this->morphToMany(
            related: Media::class,
            name: 'mediable',
            table: 'mediables',
            foreignPivotKey: 'mediable_id',
            relatedPivotKey: 'media_id',
        )
            ->withPivot(['collectie', 'alt_tekst', 'onderschrift', 'volgorde', 'meta'])
            ->orderByPivot('volgorde');
    }

    /**
     * Media filtered by collection name.
     *
     * Collections: 'featured', 'gallery', 'bijlagen', 'foto', 'hero', 'default'
     *
     * @return MorphToMany<Media, $this>
     */
    public function mediaCollectie(string $collectie): MorphToMany
    {
        return $this->media()->wherePivot('collectie', $collectie);
    }

    /**
     * Returns the first media item in the 'featured' collection, or null.
     *
     * Used for post/page header images and Open Graph image tags.
     */
    public function featuredMedia(): ?Media
    {
        /** @var Media|null */
        return $this->mediaCollectie('featured')->first();
    }

    /**
     * Attach a media item to this model in a given collection.
     *
     * @param  array<string, mixed>  $pivot  Optional pivot data (alt_tekst, onderschrift, volgorde, meta)
     */
    public function koppelMedia(Media $media, string $collectie = 'default', array $pivot = []): void
    {
        if (array_key_exists('meta', $pivot) && (is_array($pivot['meta']) || is_object($pivot['meta']))) {
            $pivot['meta'] = json_encode($pivot['meta'], JSON_THROW_ON_ERROR);
        }

        $this->media()->attach($media->id, array_merge(
            ['collectie' => $collectie],
            $pivot,
        ));
    }

    /**
     * Detach a media item from this model (all collections).
     */
    public function ontkoppelMedia(Media $media): void
    {
        $this->media()->detach($media->id);
    }
}
