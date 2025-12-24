<?php

return [

    // Comment pagination
    'pagination' => [
        'per_page' => env('COMMENTS_PER_PAGE', 20),
    ],

    // Rate Limiting
    'rate_limit' => [
        'max_attempts' => env('COMMENT_RATE_LIMIT_ATTEMPTS', 5),
        'decay_minutes' => env('COMMENT_RATE_LIMIT_MINUTES', 5),
    ],

    // Spam prevention
    'spam_prevention' => [
        'min_word_count' => env('COMMENT_MIN_WORDS', 3),
        'max_repeated_chars' => env('COMMENT_MAX_REPEATED_CHARS', 5),
        'duplicate_check_hours' => env('COMMENT_DUPLICATE_CHECK_HOURS', 24),
        'allow_urls' => env('COMMENT_ALLOW_URLS', false),
        'allow_emails' => env('COMMENT_ALLOW_EMAILS', false),
    ],

    // Moderation
    'moderation' => [
        'auto_approve' => env('COMMENT_AUTO_APPROVE', false),
        'track_ip' => env('COMMENT_TRACK_IP', true),
    ],

];
