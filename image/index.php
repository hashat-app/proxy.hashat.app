<?php

// If you want to use the proxy in developement mode, set the variable to true
// In developement mode, the proxy will work on all domains
$developement_mode = true;

// Check that the proxy is only used on the domain "hashat.app"
// ! [IMPORTANT] Change the domain to your own domain or remove the if statement if you want to use the proxy on all domains
if ($_SERVER['HTTP_HOST'] !== 'hashat.app' AND $developement_mode === false) {
    // Display error image because the proxy is not used on the domain "hashat.app"
    header('Content-Type: image/webp');
    $image = imagecreatefromwebp('error-images/error-4.webp');
    imagewebp($image);
    imagedestroy($image);
    exit;
}

// The image URL to proxy
@$imageUrl = $_GET['url'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Check if the URL is valid
if (filter_var($imageUrl, FILTER_VALIDATE_URL) === false OR empty($imageUrl)) {
    // Display error image because the URL is invalid
    header('Content-Type: image/webp');
    $image = imagecreatefromwebp('error-images/error-1.webp');
    imagewebp($image);
    imagedestroy($image);
    exit;
}

// Initialize a cURL session
$ch = curl_init($imageUrl);

// Set the HTTP headers from the request
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    // Send custom user agent for Hashat Bot
    // ! [IMPORTANT] Change the user agent to your own user agent
    'User-Agent: Mozilla/5.0 (compatible; HashatBot/2.0; +https://hashat.app);'
]);

// Return the response as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Set the timeout to 3 seconds
curl_setopt($ch, CURLOPT_TIMEOUT, 3);

// Execute the cURL request
$imageData = curl_exec($ch);

// Check if the request timed out
if (curl_errno($ch) == CURLE_OPERATION_TIMEDOUT) {
    // Display the error image because the request timed out
    header('Content-Type: image/webp');
    $image = imagecreatefromwebp('error-images/error-1.webp');
    imagewebp($image);
    imagedestroy($image);
    exit;
}

// Close the cURL session
curl_close($ch);

// Check if the file is an image
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_buffer($finfo, $imageData);
finfo_close($finfo);
if (strpos($mime_type, 'image/') === 0) {
    // send WebP header
    header('Content-Type: image/webp');

    if (isset($_GET['ratio'])) {
        // Get the desired aspect ratio
        $ratio = $_GET['ratio'];
        $ratio_parts = explode(':', $ratio);
        if (count($ratio_parts) != 2) {
            // display error image
            header('Content-Type: image/webp');
            $image = imagecreatefromwebp('error-images/error-2.webp');
            imagepng($image);
            imagedestroy($image);
            exit;
        }
        $ratio_width = $ratio_parts[0];
        $ratio_height = $ratio_parts[1];

        if ($mime_type == 'image/jpeg') {
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
        if ($mime_type == 'image/jpeg') {
            // generate webp image from jpeg
            imagewebp($cropped_image);
        } else {
            // generate webp image from png
            imagewebp($cropped_image);
        }

        // Clean up
        imagedestroy($image);
        imagedestroy($cropped_image);
    } elseif (isset($_GET['size'])) {
        // Get the desired size
        $size = $_GET['size'];
        $size_parts = explode('x', $size);
        if (count($size_parts) !== 2) {
            // Display error image because there are to less or to many size parts
            header('Content-Type: image/webp');
            $image = imagecreatefromwebp('error-images/error-2.webp');
            imagepng($image);
            imagedestroy($image);
            exit;
        }
        $width = $size_parts[0];
        $height = $size_parts[1];

        if ($mime_type == 'image/jpeg') {
            // Load the JPEG image
            $image = imagecreatefromjpeg($imageUrl);
        } else {
            // Load the image
            $image = imagecreatefromstring($imageData);
        }

        // Get the current width and height of the image
        $current_width = imagesx($image);
        $current_height = imagesy($image);

        // Calculate the new height and width based on the desired size
        $new_width = $width;
        $new_height = $height;

        // Resize the image
        $resized_image = imagescale($image, $new_width, $new_height);

        // Output the resized image
        imagewebp($resized_image);

        // Clean up
        imagedestroy($image);
        imagedestroy($resized_image);
    } else {
        // Output the image data
        echo $imageData;
    }
} else {
    // Display error image because the file is not an image
    header('Content-Type: image/webp');
    $image = imagecreatefromwebp('error-images/error-3.webp');
    imagewebp($image);
    imagedestroy($image);
    exit;
}