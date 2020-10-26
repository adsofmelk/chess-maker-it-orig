<?php

/*
|--------------------------------------------------------------------------
| Ably configuration file
|--------------------------------------------------------------------------
|
| You may use any of the ClientOptions here.
| See the complete list here: https://www.ably.io/documentation/rest/usage#client-options
|
| A key or any other valid means of authenticating is required.
*/

return [
    'key' => env('ABLY_KEY', 'cVCwZA.Jawr_Q:3whIJe45HDXvkVwz'),
];



// // curl https://rest.ably.io/channels/damos/presence  -u "cVCwZA.Jawr_Q:3whIJe45HDXvkVwz"
// // curl https://rest.ably.io/channels/damos/presence  -u "cVCwZA.vcHe5Q:jRwniDycbsRvHjXO"
// // curl https://rest.ably.io/channels/rest-example/presence/history \
//  -u "cVCwZA.Jawr_Q:3whIJe45HDXvkVwz"

// // curl https://rest.ably.io/channels/rest-example/presence/history \
//  -u "cVCwZA.vcHe5Q:jRwniDycbsRvHjXO"

//  curl -X POST https://rest.ably.io/channels/damos/messages \
//  -u "cVCwZA.Jawr_Q:3whIJe45HDXvkVwz" \
//  -H "Content-Type: application/json" \
//  --data '{ "name": "greeting", "data": "example con curl" }'

//  curl https://rest.ably.io/push/deviceRegistrations \
//  -u "cVCwZA.Jawr_Q:3whIJe45HDXvkVwz"

//  curl https://rest.ably.io/push/channels \
//  -u "cVCwZA.Jawr_Q:3whIJe45HDXvkVwz"

//  curl https://rest.ably.io/push/channels \
//  -u "cVCwZA.vcHe5Q:jRwniDycbsRvHjXO"