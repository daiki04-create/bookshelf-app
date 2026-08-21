<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'published_date' => $this->published_date?->format('Y-m-d') ?? $this->published_date,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'average_rating' => $this->reviews_avg_rating ? round((float) $this->reviews_avg_rating, 1) : null,
            'reviews_count' => $this->reviews_count ?? $this->reviews->count(),
            'genres' => $this->whenLoaded('genres', function () {
                return $this->genres->map(fn ($genre) => [
                    'id' => $genre->id,
                    'name' => $genre->name,
                ]);
            }),
            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(fn ($review) => [
                    'id' => $review->id,
                    'user_name' => $review->user?->name,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at?->format('Y-m-d H:i:s'),
                ]);
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
