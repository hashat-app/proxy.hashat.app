# Hashat Proxy

⚠️ Visit [this support article](https://go.hashat.app/proxy-troubleshooting) if you are a website owner and you encountered an error while embedding an image from your site on Hashat.

___

Hashat uses an image proxy to protect the privacy of our users.
This proxy will download the image from the requested url and will show it to the user.
The server hosting the image will only see the IP and useragent of Hashat's proxy server and not the sensitive data of the requesting user.

___

To set up this proxy on your own server you'll need at least PHP 7.4 running on your system.
Please mind that you will have to change the following lines:

#### Developement mode
`$developement_mode = true;` ➡️ If developement mode is set to true, the proxy will answer requests from every domain. If false, only domains as shown below will be answered and other domains will be shown an error.

#### Useragent
```php
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (compatible; HashatBot/2.0; +https://hashat.app);'
]);
```
You have to set your own user agent here. Using HashatBot for your own proxy is stricly forbidden.

#### Allowed domains
```php
if ($_SERVER['HTTP_HOST'] !== 'hashat.app' AND $developement_mode === false) {
    header('Content-Type: image/webp');
    $image = imagecreatefromwebp('error-images/error-4.webp');
    imagewebp($image);
    imagedestroy($image);
    exit;
}
```
Modify or remove this if-statement as you need. You can change hashat.app to everything you like to only allow the proxy to be used by your domain.

___

## Contributing to this proxy
We are not accepting any pull requests or issues to this repository.
If you have feedback or questions, contact our support team [here](https://go.hashat.app/contact) or read our [support article](https://go.hashat.app/proxy-troubleshooting) about this proxy.
