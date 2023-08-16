# Hashat Proxy

⚠️ Visit [this support article](https://go.hashat.app/proxy-troubleshooting) if you are a website owner and you encountered an error while embedding an image from your site on Hashat.

___

This is the image proxy used by [Hashat](https://hashat.app) which is here to protect user privacy and stop tracking-features by the image host. 
The way it works is easy: \
When an image is requested, the proxy will download the recource from the given request URL and simply send only the image back to the requester. Its just like what a proxy does, weird... \
When using the proxy, the image host will only see the server's IP and useragent and not sensitive/trackable data from the requesting user.

___

To setup this proxy on your own server you'll need `PHP>=7.4` running on your system.
Please mind that you will have to change the following lines:

#### Developement mode
`$developement_mode = true;` \
If developement mode is enabled, the proxy will answer requests from every domain. If disabled, only request from [configured domains](https://github.com/hashat-app/proxy.hashat.app/edit/main/README.md#allowed-domains) will get an response, every other domain will get the error code `4`.

#### Useragent
```php
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.246;'
]);
```
**Set** your own agent here. (This exaple is an Windows 10-based agent using the Edge browser). \
⚠️ The use of the HashatBot agent *(which is the default in the code)* is strictly forbidden.

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
To only allow connections comming from a specific domain, just change the `'hashat.app'`, to e.g. `'example.com'`. If you want to allow every request origin, just replace `$_SERVER['HTTP_HOST'] !== 'hashat.app'` with `false`

___

## Contributing to this proxy
We do not guarantee to process any Pull-Request or Issues. \
A more reliable way of contributing or reporting issues is via our [contact page](https://go.hashat.app/contact). But please read the [troubleshooting guide](https://go.hashat.app/proxy-troubleshooting) first :)
