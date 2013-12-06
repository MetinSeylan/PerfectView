PerfectView nedir ?
=====================


CSS, JS gibi kaynakları kolayca yönetebilir, şablonlara sınırsız alt şablon atayabilir, ajax sorgularında ek kod yazmadan json cevap verebilir, şablonlar üzerinden base html şablonuna sayfa başlığı, script ve css leri hooking edebilir ve HEAD tagları ekleyebilirsiniz

----------


Kurulum
---------
Laravel 4 composer.json dosyasının "require" dizisine ekleyiniz
```json
    "metinseylan/perfectview": "dev-master"
```
ardından konut satırına
```composer
    composer update
```
çılıştırdıktan sonra Laravel 4 config klasörü altındaki app.php dosyasının "**providers**" dizisine
```php
    'MetinSeylan\PerfectView\PerfectViewServiceProvider'
```
ekleyin ve yine aynı dosyanın "**aliases**" dizisine
```php
    'PerfectView'     => 'MetinSeylan\PerfectView\Facades\PerfectView'
```
ekleyin ve kurulum tamamlandı.


Kullanım
---------

Öncelikle view klasörü altına bir "**base.blade.php**" adında bir dosya oluşturuyoruz bu bizim olmazsa olmaz view dosyamız oluyor çağırılan bütün view dosyaları bunun üzerinden gösterilecek.

**Örnek base dosyamız**

```php
<!doctype html>
<html prefix="og: http://ogp.me/ns#">
<head>
    @section('head_hook')
    <base href="@yield('base', Config::get('app.url'))">
    <meta charset="UTF-8">
    <title>{{ PerfectView::title() }}</title>
    <meta name="viewport" content="@yield('viewport', 'width=device-width, initial-scale=1')">
    {{ PerfectView::tag() }}
    {{ PerfectView::style() }}
    @show
</head>
<body>
@section('content_hook')
    {{ $content }}
@show
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
{{ PerfectView::script() }} 
</body>
</html>
```

**Basit kullanım**

```php
    PerfectView::make("merhaba");
```