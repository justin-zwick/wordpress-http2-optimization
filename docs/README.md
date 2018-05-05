# Documentation
 
Documentation for the WordPress plugin HTTP/2 Optimization.

**The plugin is in beta. Please submit your feedback on the [Github forum](https://github.com/o10n-x/http2-optimization/issues).**

The plugin provides in a complete solution for HTTP/2 Server Push, using JSON based configuration and filters to dynamicly push assets. In partnership with the [Service Worker / PWA Optimization](https://github.com/o10n-x/wordpress-pwa-optimization) plugin this plugin adds a Service Worker based Cache-Digest calculation to save data transfer by pushing only assets that are not already cached in the browser.

Additional features can be requested on the [Github forum](https://github.com/o10n-x/http2-optimization/issues).

## Getting started

Before you start using the plugin it is important to test your server for HTTP/2 support. You can use the online test by KeyCDN: https://tools.keycdn.com/http2-test

# HTTP/2 Configuration

The configuration is an array of JSON objects. Each object is a asset to push and contains the required properties `url` and `as` and the optional properties `type` and `meta`.

`url` is the URI of the asset to push by the server. This can be a local file or a URL. When a external URL is pushed, the plugin adds `crossorigin` to the server push header.

`as` is the content type instruction for the HTTP/2 server. Mozilla reports the following possible values

- `audio` Audio file.
- `document` An HTML document intended to be embedded inside a <frame> or <iframe>.
- `embed` A resource to be embedded inside an <embed> element.
- `fetch` Resource to be accessed by a fetch or XHR request, such as an ArrayBuffer or JSON file.
- `font` Font file.
- `image` Image file.
- `object` A resource to be embedded inside an <embed> element.
- `script` JavaScript file.
- `style` Stylesheet.
- `track` WebVTT file.
- `worker` A JavaScript web worker or shared worker.
- `video` Video file.

`type` is an optional mime type definition, e.g. `video/mp4`.

`meta` is a boolean that instructs the plugin to add a `rel="preload"` meta tag for the asset to the `<head>` of the page.

#### Example Configuration

```json
[
  {
    "url": "/wp-content/themes/theme-x/style.css",
    "as": "style"
  },
  {
    "url": "/wp-content/themes/theme-x/images/logo.png",
    "as": "image",
    "type": "image/png"
  }
]
```

<details/>
  <summary>JSON schema for HTTP/2 config</summary>

```json
{
	"push": {
	    "type": "object",
	    "properties": {
	        "enabled": {
	            "title": "Enable HTTP/2 Server Push",
	            "type": "boolean",
	            "default": false
	        },
	        "list": {
	            "title": "HTTP/2 Server Push configuration",
	            "type": "array",
	            "items": {
	                "title": "Asset to push",
	                "type": "object",
	                "properties": {
	                    "url": {
	                        "type": "string",
	                        "format": "uri",
	                        "minLength": 1
	                    },
	                    "as": {
	                        "title": "Type of asset",
	                        "type": "string",
	                        "enum": ["audio", "document", "embed", "fetch", "font", "image", "object", "script", "style", "track", "worker", "video"]
	                    },
	                    "type": {
	                        "title": "Mime type of asset",
	                        "type": "string",
	                        "pattern": "^[^/]+/[^/]+$"
	                    },
	                    "meta": {
	                        "title": "Add/remove meta rel=preload in header.",
	                        "type": "boolean"
	                    }
	                },
	                "required": ["url", "as"],
	                "additionalProperties": false
	            },
	            "uniqueItems": true
	        },
	        "meta": {
	            "title": "Add meta rel=preload to header.",
	            "type": "boolean"
	        }
	    },
	    "additionalProperties": false,
	    "required": ["enabled"]
	}
}
```
</details>


#### Push from PHP

The following method can be used to push an asset from PHP.

```php
\O10n\push('url','as','type');
```