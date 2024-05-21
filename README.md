# Installation
1. Edit the PHP file to reflect basic settings like the address/adresses that should get the mail
1. Run the HTML file

# Features
1. Just 1 page for client (HTML) and 1 page for server (PHP)
1. Every form field can either be GET or POST
1. Triple mode for errors and success - they can be presented right in the form itself, in a blank page or redirect to external pages
1. Dual mode for results - they can be either sent, logged or both
1. The form can be limited to be used in specific domains only
2. Auto reply can be sent automatically
1. The client side uses HTML5 to check for required fields, but the server side can check them too for a double verify

![image](https://github.com/lwcorp/antispam-php-contact-form/assets/1773306/1bfcbe55-e7a9-4f16-a0f2-ccf0aa0f0a82)

# Advanced
## Server
The configuration file can be used to:
1. Limit the domains that can use the form
1. Enable logging
1. Define auto reply
1. Define required form fields (in case the user tricked the client side)
## Client
You can optionally include these special fields (not having to be hidden, of course):
```html
<input type="hidden" name="redirect" value="false">`
```
Errors and success will appear right in the form itself slowing down the user by redirecting. Use `true` to make them appear in another page.
```html
<input type="hidden" name="sendto" value="X">
<!-- or -->
<input type="hidden" name="sendto" value="X,Y,Z">
```
Mention numbers of recpients that should get the results, whereas only the server can know the addresses behind those addresses.
```html
<input type="hidden" name="return_link_url" value="javascript:window.close()">
```
Mention a page's URL to link to in case of success:
```html
<input type="hidden" name="return_link_title" value="Close this window">
```
Mention a page's title to link to in case of success:
```html
<input type="hidden" name="errorpage" value="https://...">
```
Mention an error page that will always be redirected to on errors.
```html
<input type="hidden" name="successpage" value="https://...">
```
Mention a success page that will always be redirected to on errors.
```html
<input type="hidden" name="required" value="subject,email">
```
Ask the server to also check if these fields were filled on top of what the server was already defined to check.
