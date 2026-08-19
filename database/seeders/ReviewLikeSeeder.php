<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = Review::all();

        foreach ($reviews as $review) {
            $eligibleUsers = User::where('id', '!=', $review->user_id)->get();
            $likeCount = rand(0, min(3, $eligibleUsers->count()));

            if ($likeCount > 0) {
                $likerIds = $eligibleUsers->random($likeCount)->pluck('id');
                $review->likes()->syncWithoutDetaching($likerIds);
            }
        }
    }
}
