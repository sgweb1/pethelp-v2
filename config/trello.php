<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trello API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Trello API integration with PetHelp project
    |
    */

    'api_key' => env('TRELLO_API_KEY'),
    'token' => env('TRELLO_TOKEN'),
    'board_id' => env('TRELLO_BOARD_ID'),
    'board_url' => env('TRELLO_BOARD_URL'),
    'webhook_secret' => env('TRELLO_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | List IDs Configuration
    |--------------------------------------------------------------------------
    |
    | These will be populated after running trello:setup command
    |
    */

    'lists' => [
        'backlog' => env('TRELLO_LIST_BACKLOG'),
        'sprint' => env('TRELLO_LIST_SPRINT'),
        'development' => env('TRELLO_LIST_DEVELOPMENT'),
        'testing' => env('TRELLO_LIST_TESTING'),
        'review' => env('TRELLO_LIST_REVIEW'),
        'done' => env('TRELLO_LIST_DONE'),
        'deployed' => env('TRELLO_LIST_DEPLOYED'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Automation Settings
    |--------------------------------------------------------------------------
    */

    'automation' => [
        'auto_create_cards' => env('TRELLO_AUTO_CREATE_CARDS', true),
        'auto_move_on_status' => env('TRELLO_AUTO_MOVE_STATUS', true),
        'sync_with_git' => env('TRELLO_SYNC_GIT', true),
        'progress_tracking' => env('TRELLO_PROGRESS_TRACKING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Card Templates
    |--------------------------------------------------------------------------
    */

    'templates' => [
        'service_created' => [
            'name' => '✨ New Service: {title}',
            'description' => "**Service Created**\n" .
                           "**Sitter:** {sitter_name}\n" .
                           "**Category:** {category}\n" .
                           "**Price:** {price}\n" .
                           "**Pet Types:** {pet_types}\n\n" .
                           "**Tasks:**\n" .
                           "- [ ] Review service details\n" .
                           "- [ ] Verify category assignment\n" .
                           "- [ ] Check pricing structure\n" .
                           "- [ ] Approve for publication",
            'labels' => ['✨ New Feature', '🏗️ Core System']
        ],

        'bug_report' => [
            'name' => '🐛 Bug: {title}',
            'description' => "**Bug Report**\n" .
                           "**Reporter:** {reporter}\n" .
                           "**Priority:** {priority}\n" .
                           "**Environment:** {environment}\n\n" .
                           "**Description:**\n{description}\n\n" .
                           "**Steps to Reproduce:**\n" .
                           "1. \n" .
                           "2. \n" .
                           "3. \n\n" .
                           "**Expected vs Actual:**\n" .
                           "- Expected: \n" .
                           "- Actual: ",
            'labels' => ['🐛 Bug Fix']
        ],

        'feature_request' => [
            'name' => '✨ Feature: {title}',
            'description' => "**Feature Request**\n" .
                           "**Requested by:** {requester}\n" .
                           "**Priority:** {priority}\n" .
                           "**Estimated effort:** {effort}\n\n" .
                           "**Description:**\n{description}\n\n" .
                           "**Acceptance Criteria:**\n" .
                           "- [ ] \n" .
                           "- [ ] \n" .
                           "- [ ] \n\n" .
                           "**Technical Notes:**\n",
            'labels' => ['✨ New Feature']
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Events
    |--------------------------------------------------------------------------
    */

    'webhook_events' => [
        'updateCard',
        'createCard',
        'moveCard',
        'addMemberToCard',
        'removeMemberFromCard',
        'addChecklistToCard',
        'updateCheckItemStateOnCard'
    ]
];