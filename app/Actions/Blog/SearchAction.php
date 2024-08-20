<?php

namespace App\Actions\Blog;

use App\Enums\StatusEnum;
use App\Http\Resources\BlogListResource;
use App\Models\Blog;
use App\Services\ResponseService;
use Illuminate\Support\Carbon;

class SearchAction
{
    public function __construct(protected ResponseService $responseService)
    {
    }

    public function execute(string $searchTerm)
    {
        $itemsPerPage = config('blog.items_per_page');

        $paginator = Blog::with('tags')
            ->whereDate('published_at', '<=', Carbon::now())
            ->where('status', StatusEnum::DONE->value)
            ->whereLike('title', "%$searchTerm%")
            ->orderByDesc('published_at')
            ->paginate($itemsPerPage);

        $rows = BlogListResource::collection($paginator->items());

        return $this->responseService->getPaginationResponse($rows, $paginator);
    }
}
