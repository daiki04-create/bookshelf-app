<?php

namespace App\Http\Controllers;

use App\Models\Review;

class ReviewLikeController extends Controller
{
    public function toggle(Review $review)
    {
        $user = auth()->user();
        $user->likedReviews()->toggle($review->id);

        return back();
    }
}
