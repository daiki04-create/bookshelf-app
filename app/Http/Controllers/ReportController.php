<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $userReviews = Review::where('user_id', $user->id)
            ->with(['book.genres'])
            ->get();

        $totalReviews = $userReviews->count();
        $booksRead = $userReviews->pluck('book_id')->unique()->count();
        $averageRating = $totalReviews > 0 ? round($userReviews->avg('rating'), 1) : 0;

        $ratingDistribution = collect([1, 2, 3, 4, 5])->mapWithKeys(function ($star) use ($userReviews) {
            return [$star => $userReviews->where('rating', $star)->count()];
        });

        $topRatedBooks = $userReviews->where('rating', '>=', 4)
            ->sortByDesc('created_at')
            ->sortByDesc('rating')
            ->take(5)
            ->map(function ($review) {
                return [
                    'id' => $review->book->id,
                    'title' => $review->book->title,
                    'author' => $review->book->author,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                ];
            })
            ->values();

        $genreStats = $userReviews->flatMap(function ($review) {
            return $review->book->genres->map(function ($genre) use ($review) {
                return [
                    'genre_id' => $genre->id,
                    'genre_name' => $genre->name,
                    'rating' => $review->rating,
                ];
            });
        })
            ->groupBy('genre_id')
            ->map(function ($items) {
                return [
                    'id' => $items->first()['genre_id'],
                    'name' => $items->first()['genre_name'],
                    'average_rating' => round($items->avg('rating'), 1),
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('count')
            ->sortByDesc('average_rating')
            ->take(5)
            ->values();

        $stats = [
            'summary' => [
                'total_reviews' => $totalReviews,
                'books_read' => $booksRead,
                'average_rating' => $averageRating,
            ],
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreStats,
        ];

        return view('reports.index', compact('stats'));
    }
}
