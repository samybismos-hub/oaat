# Spatie\Translatable\Exceptions\AttributeIsNotTranslatable - Internal Server Error

Cannot translate attribute `name` as it's not one of the translatable attributes: `description`

PHP 8.3.30
Laravel 13.33.0
127.0.0.1:8000

## Stack Trace

0 - vendor\spatie\laravel-translatable\src\Exceptions\AttributeIsNotTranslatable.php:13
1 - vendor\spatie\laravel-translatable\src\HasTranslations.php:283
2 - vendor\spatie\laravel-translatable\src\HasTranslations.php:136
3 - vendor\spatie\laravel-translatable\src\HasTranslations.php:249
4 - vendor\spatie\laravel-translatable\src\HasTranslations.php:289
5 - vendor\spatie\laravel-translatable\src\HasTranslations.php:82
6 - resources\views\partials\_carte_projet.blade.php:103
7 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:123
8 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
9 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
10 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:22
11 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
12 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:10
13 - vendor\laravel\framework\src\Illuminate\View\View.php:208
14 - vendor\laravel\framework\src\Illuminate\View\View.php:191
15 - vendor\laravel\framework\src\Illuminate\View\View.php:160
16 - resources\views\pages\projets-index.blade.php:248
17 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:123
18 - vendor\laravel\framework\src\Illuminate\Filesystem\Filesystem.php:124
19 - vendor\laravel\framework\src\Illuminate\View\Engines\PhpEngine.php:57
20 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:22
21 - vendor\laravel\framework\src\Illuminate\View\Engines\CompilerEngine.php:76
22 - vendor\livewire\livewire\src\Mechanisms\ExtendBlade\ExtendedCompilerEngine.php:10
23 - vendor\laravel\framework\src\Illuminate\View\View.php:208
24 - vendor\laravel\framework\src\Illuminate\View\View.php:191
25 - vendor\laravel\framework\src\Illuminate\View\View.php:160
26 - vendor\laravel\framework\src\Illuminate\Http\Response.php:82
27 - vendor\laravel\framework\src\Illuminate\Http\Response.php:40
28 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:939
29 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:906
30 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
31 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
32 - app\Http\Middleware\SetLocale.php:55
33 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
34 - vendor\laravel\framework\src\Illuminate\Routing\Middleware\SubstituteBindings.php:52
35 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
36 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestForgery.php:104
37 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
38 - vendor\laravel\framework\src\Illuminate\View\Middleware\ShareErrorsFromSession.php:48
39 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
40 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:120
41 - vendor\laravel\framework\src\Illuminate\Session\Middleware\StartSession.php:63
42 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
43 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse.php:36
44 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
45 - vendor\laravel\framework\src\Illuminate\Cookie\Middleware\EncryptCookies.php:74
46 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
47 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
48 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:821
49 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:800
50 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:764
51 - vendor\laravel\framework\src\Illuminate\Routing\Router.php:753
52 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:200
53 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:180
54 - vendor\livewire\livewire\src\Features\SupportDisablingBackButtonCache\DisableBackButtonCacheMiddleware.php:19
55 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
56 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
57 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull.php:31
58 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
59 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TransformsRequest.php:21
60 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\TrimStrings.php:51
61 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
62 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePostSize.php:27
63 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
64 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance.php:110
65 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
66 - vendor\laravel\framework\src\Illuminate\Http\Middleware\HandleCors.php:61
67 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
68 - vendor\laravel\framework\src\Illuminate\Http\Middleware\TrustProxies.php:58
69 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
70 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks.php:22
71 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
72 - vendor\laravel\framework\src\Illuminate\Http\Middleware\ValidatePathEncoding.php:28
73 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:219
74 - vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php:137
75 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:175
76 - vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php:144
77 - vendor\laravel\framework\src\Illuminate\Foundation\Application.php:1227
78 - public\index.php:20
79 - vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php:23


## Request

GET /fr/projets

## Headers

* **host**: 127.0.0.1:8000
* **connection**: keep-alive
* **cache-control**: max-age=0
* **sec-ch-ua**: "Google Chrome";v="155", "Chromium";v="155", "Not(A:Brand";v="24"
* **sec-ch-ua-mobile**: ?0
* **sec-ch-ua-platform**: "Windows"
* **upgrade-insecure-requests**: 1
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36
* **accept**: text/html,application/xhtml+xml,application/xml;q=0.9,image/jxl,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
* **sec-fetch-site**: same-origin
* **sec-fetch-mode**: navigate
* **sec-fetch-user**: ?1
* **sec-fetch-dest**: document
* **referer**: http://127.0.0.1:8000/fr/projets?annee=&status=all&tri=rec&page=1
* **accept-encoding**: gzip, deflate, br, zstd
* **accept-language**: fr-FR,fr;q=0.9,en-FR;q=0.8,en;q=0.7,en-US;q=0.6
* **cookie**: XSRF-TOKEN=eyJpdiI6IjVjeWI4UlZPWVRiNXhtU3NYUFVKY3c9PSIsInZhbHVlIjoiN0RVcnJpeE9YWDVWMkR2TmhnaFR4NmcxdjYyKzlFOXA4Yk1CZGtTNDQzanNIZHVKT0wrVUQ3b053Yk5vdklXbEsxTlRNYTIvRWhtYUM0NHRGRGF6TTQ3NGtqaXRxQjdRZTBsL3cxaW5wS2FENTNad2F3VmtFSTZmOCtCY01nV0siLCJtYWMiOiJjYjdkNmUxMDYzZTZjYjZhZThiMDE2YWQxMzQ3NTBmNDE2OGYwZjA0OTQyMmY5ZGJjYmQwYmQzODYzMzFkMzczIiwidGFnIjoiIn0%3D; oaat-session=eyJpdiI6IllDRGZ5eEcrODJjd3NvZFFhb1NQS1E9PSIsInZhbHVlIjoiK0JYZldWc3Z5Qy9RQTRBTWFHS3RpQXhYTnFZemU2SXA5VE9YTEpMYWRCSWZQNmlPMWw1YmczZTQzaE1OMTZKdHZJTytiMk9kN2pLNU5xM2VmWGdWekNDaDVzZ0VlZUxpanFmd2NGR3F6S3kzc21WQkVqcGNzN2NHVFUvU01vK3IiLCJtYWMiOiIzYWFmMDI3YzFhYWQ2M2I2OTU5OWY2NDQ0YTc0NzNkZTJhNzc0Njc3Y2E2Y2U1M2I4ZGYwNzRhMDgxY2VkYjFjIiwidGFnIjoiIn0%3D

## Route Context

controller: App\Http\Controllers\Public\ProjetController@index
route name: projets.index
middleware: web, App\Http\Middleware\SetLocale

## Route Parameters

{
    "locale": "fr"
}

## Database Queries

* mysql - select * from `sessions` where `id` = 'eh5748Y5BEmjy8h0QBH2NLHVxEjlmEqnIRx0CHTd' limit 1 (29.67 ms)
* mysql - select * from `projects` where `published_at` is not null and `published_at` <= '2026-10-14 10:07:20' order by CASE WHEN status = 'awaiting_funding' THEN 0 ELSE 1 END, start_date DESC (4.41 ms)
* mysql - select * from `domains` where `domains`.`id` in (1, 2, 3, 4, 5, 6, 7, 9) (1.98 ms)
* mysql - select `partners`.*, `project_partner`.`project_id` as `pivot_project_id`, `project_partner`.`partner_id` as `pivot_partner_id`, `project_partner`.`created_at` as `pivot_created_at`, `project_partner`.`updated_at` as `pivot_updated_at`, `project_partner`.`role` as `pivot_role` from `partners` inner join `project_partner` on `partners`.`id` = `project_partner`.`partner_id` where `project_partner`.`project_id` in (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 35, 36, 37, 38, 39) (2.09 ms)
* mysql - select `domains`.*, (select count(*) from `projects` where `domains`.`id` = `projects`.`domain_id` and `published_at` is not null and `published_at` <= '2026-10-14 10:07:20') as `projects_count` from `domains` where exists (select * from `projects` where `domains`.`id` = `projects`.`domain_id` and `published_at` is not null and `published_at` <= '2026-10-14 10:07:20') (7.98 ms)
* mysql - select count(*) as `aggregate` from `projects` where `published_at` is not null and `published_at` <= '2026-10-14 10:07:20' (1.2 ms)
* mysql - select count(*) as `aggregate` from `projects` where `published_at` is not null and `published_at` <= '2026-10-14 10:07:20' and `status` = 'completed' (1.36 ms)
* mysql - select count(*) as `aggregate` from `projects` where `published_at` is not null and `published_at` <= '2026-10-14 10:07:20' and `status` = 'awaiting_funding' (1.04 ms)
* mysql - select count(*) as `aggregate` from `projects` where `published_at` is not null and `published_at` <= '2026-10-14 10:07:20' and `status` = 'ongoing' (1 ms)
* mysql - select count(*) as `aggregate` from `domains` where exists (select * from `projects` where `domains`.`id` = `projects`.`domain_id` and `published_at` is not null and `published_at` <= '2026-10-14 10:07:20') (1.28 ms)
* mysql - select count(distinct `city`) as `aggregate` from `projects` where `published_at` is not null and `published_at` <= '2026-10-14 10:07:20' (1.14 ms)
* mysql - select * from `media` where `media`.`model_id` in (39) and `media`.`model_type` = 'App\Models\Project' (7.14 ms)
* mysql - select * from `media` where `media`.`model_id` in (11) and `media`.`model_type` = 'App\Models\Project' (1.23 ms)
