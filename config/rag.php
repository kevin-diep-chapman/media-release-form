<?php

return [

    'chat_model' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),

    'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),

    'top_k' => (int) env('RAG_TOP_K', 5),

    'chunk_size' => 1500,

    'chunk_overlap' => 200,

    'max_message_length' => 1000,

    'source_types' => [
        'event',
        'media_release',
        'user',
    ],

];
