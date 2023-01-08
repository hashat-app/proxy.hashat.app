<?php

// The image URL to proxy
$imageUrl = $_GET['url'];

// Check that the proxy is only used on the domain "hashat.app"
// if (!preg_match('/^https?:\/\/hashat\.app/', $_SERVER['HTTP_REFERER'])) {
//     http_response_code(403);
//     echo 'Error: This proxy can only be used on the domain "hashat.app"';
//     exit;
// }

// Initialize a cURL session
$ch = curl_init($imageUrl);

// Set the HTTP headers from the request
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $_SERVER['HTTP_USER_AGENT']
]);

// Return the response as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$imageData = curl_exec($ch);

// Check if the file is an image
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_buffer($finfo, $imageData);
if (strpos($mimeType, 'image/') === 0) {
    // Set the content type to the content type of the image
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    header('Content-Type: ' . $contentType);

    if (isset($_GET['thumb'])) {
        if ($mimeType == 'image/jpeg') {
            // Load the JPEG image as a thumbnail
            $image = imagecreatefromjpeg($imageUrl);
            $thumb = imagescale($image, 200);
            imagejpeg($thumb);
            imagedestroy($image);
            imagedestroy($thumb);
        } else {
            // Load the image as a thumbnail
            $image = imagecreatefromstring($imageData);
            $thumb = imagescale($image, 100);
            imagealphablending($thumb, true);
            imagesavealpha($thumb, true);
            imagepng($thumb);
            imagedestroy($image);
            imagedestroy($thumb);
        }
    } else {
        // Output the image data
        echo $imageData;
    }
} else {
    // Return an error if the file is not an image
    http_response_code(400);
    echo 'Error: The URL does not point to an image';
}

finfo_close($finfo);

// Close the cURL session
curl_close($ch);
