<?php

// The image URL to proxy
$imageUrl = $_GET['url'];

if (empty($imageUrl)) {
    // Return an error if the URL is not set
    http_response_code(400);
    echo 'Error: No URL specified';
    exit;
}

// Create a stream context with the HTTP headers from the request
$options = [
    'http' => [
        'method' => 'GET',
        'header' => implode("\r\n", [
            'User-Agent: ' . $_SERVER['HTTP_USER_AGENT']
        ]),
    ],
];

$context = stream_context_create($options);

// Load the image from the URL using the HTTP headers from the request
$imageData = file_get_contents($imageUrl, false, $context);

// Check if the file is an image
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_buffer($finfo, $imageData);
if (strpos($mimeType, 'image/') === 0) {
    // Set the content type to the content type of the image
    $contentType = get_headers($imageUrl, 1)['Content-Type'];
    header('Content-Type: ' . $contentType);

    // Output the image data
    echo $imageData;
} else {
    // Return an error if the file is not an image
    http_response_code(400);
    echo 'Error: The URL does not point to an image';
}

finfo_close($finfo);
