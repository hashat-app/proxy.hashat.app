<?php

// Check that the proxy is only used on the domain "hashat.app"
// if (!preg_match('/^https?:\/\/hashat\.app/', $_SERVER['HTTP_REFERER'])) {
//     http_response_code(403);
//     echo 'Error: This proxy can only be used on the domain "hashat.app"';
//     exit;
// }

// The image URL to proxy
$imageUrl = $_GET['url'];

// Check if the URL is valid
if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false OR empty($imageUrl)) {
    // display error image
    header('Location: https://proxy.hashat.app/image/error-1.png');
    exit;
}

// Initialize a cURL session
$ch = curl_init($imageUrl);

// Set the HTTP headers from the request
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: ' . $_SERVER['HTTP_USER_AGENT']
]);

// Return the response as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Set the timeout to 3 seconds
curl_setopt($ch, CURLOPT_TIMEOUT, 3);

// Execute the cURL request
$imageData = curl_exec($ch);

// Check if the request timed out
if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
    // Display the error image
    header('Content-Type: image/png');
    $image = imagecreatefrompng('error-1.png');
    imagepng($image);
    imagedestroy($image);
    exit;
}

// Check if the file is an image
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_buffer($finfo, $imageData);
if (strpos($mimeType, 'image/') === 0) {
    // Set the content type to the content type of the image
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    header('Content-Type: ' . $contentType);

    if (isset($_GET['ratio'])) {
        // Get the desired aspect ratio
        $ratio = $_GET['ratio'];
        $ratio_parts = explode(':', $ratio);
        if (count($ratio_parts) != 2) {
            // display error image
            header('Content-Type: image/png');
            $image = imagecreatefrompng('error-2.png');
            imagepng($image);
            imagedestroy($image);
            exit;
        }
        $ratio_width = $ratio_parts[0];
        $ratio_height = $ratio_parts[1];

        if ($mimeType == 'image/jpeg') {
            // Load the JPEG image
            $image = imagecreatefromjpeg($imageUrl);
        } else {
            // Load the image
            $image = imagecreatefromstring($imageData);
        }

        // Get the current width and height of the image
        $width = imagesx($image);
        $height = imagesy($image);

        // Calculate the new height and width based on the desired aspect ratio
        $new_height = $width * ($ratio_height / $ratio_width);
        $new_width = $height * ($ratio_width / $ratio_height);

        // Crop the image to the desired aspect ratio
        if ($new_height < $height) {
            $y = ($height - $new_height) / 2;
            $cropped_image = imagecrop($image, ['x' => 0, 'y' => $y, 'width' => $width, 'height' => $new_height]);
        } else {
            $x = ($width - $new_width) / 2;
            $cropped_image = imagecrop($image, ['x' => $x, 'y' => 0, 'width' => $new_width, 'height' => $height]);
        }

        // Output the cropped image
        if ($mimeType == 'image/jpeg') {
            imagejpeg($cropped_image);
        } else {
            imagepng($cropped_image);
        }

        // Clean up
        imagedestroy($image);
        imagedestroy($cropped_image);
    } else {
        // Output the image data
        echo $imageData;
    }
} else {
    // display error image
    header('Content-Type: image/png');
    $image = imagecreatefrompng('error-3.png');
    imagepng($image);
    imagedestroy($image);
    exit;
}

finfo_close($finfo);

// Close the cURL session
curl_close($ch);