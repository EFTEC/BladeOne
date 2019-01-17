# BladeOneCache extension library (optional)

Requires: BladeOne

This library adds cache to the visual layer and business/logic layer.
For using this library, the code requires to include and use the trait BladeOneCache

```php
class MyBlade extends  bladeone\BladeOne {
    use bladeone\BladeOneCache;
}
$blade=new MyBlade($views,$compiledFolder);
```
- Where MyBlade is a new class that extends the bladeone class and use the cache features.



## New Tags (template file)

### cache

```html
@ cache(1,86400). 
<!-- content here will be cached-->
@ endcache()
<!-- this content will not be cached-->
@ cache(2,86400). 
<!-- content here will also be cached-->
@ endcache()
```

- @cache([id],[duration]) start a new cache block
-   The cacheid (optional) indicates the id of the cache. It shoulds be unique. If not id then its added automatically
-   The duration (optional) in seconds indicates the duration of the cache. 
- @endcache
-   End of the cache block.  It shouldn't be stacked.

## New Business Logic / Controller function

### function cacheExpired
```php
if ($blade->cacheExpired('hellocache',1,5)) {   //'helloonecache' =template, =1 id cache, 5=duration (seconds)
    // cache expired, so we should do some stuff (such as read from the database)
}
``` 

- function cacheExpired(cachefile,cacheid,duration) returns true if the cache expires (and it shoulds be calculated and rebuild), otherwise false
-    cachefile indicates the template to use.
-    cacheid  indicates the id of the cache.
-    duration indicates the duration of the cache (in seconds)

> Note : if BLADEONE_MODE = 1 (**forced**) then the cache system is never used.

> Note : The cache system works per **template** and **cacheid**. I.e. its possible to cache a part of a template for a limited time, while caching the rest for a long while.
