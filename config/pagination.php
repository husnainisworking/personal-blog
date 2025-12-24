<?php

return [

    'default_per_page' => env('PAGINATION_PER_PAGE', 10),
    'posts' => env('POSTS_PER_PAGE', 10),
    'categories' => env('CATEGORIES_PER_PAGE', 10),
    'tags' => env('TAGS_PER_PAGE', 10),
    'search' => env('SEARCH_RESULTS_PER_PAGE', 10),
    'comments' => env('COMMENTS_PER_PAGE', 20),

];
