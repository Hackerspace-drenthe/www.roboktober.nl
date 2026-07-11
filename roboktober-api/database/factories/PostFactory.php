<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ContentFormat;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $titel = $this->faker->sentence(4);

        return [
            'titel' => $titel,
            'slug' => Str::slug($titel).'-'.$this->faker->unique()->numberBetween(1000, 9999),
            'excerpt' => $this->faker->optional()->sentence(),
            'content' => '<p>'.implode('</p><p>', $this->faker->paragraphs(3)).'</p>',
            'content_format' => ContentFormat::Html,
            'categorie' => null,
            'tags' => null,
            'is_published' => false,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state([
            'is_published' => true,
            'published_at' => now()->subDays(rand(1, 30)),
        ]);
    }
}
