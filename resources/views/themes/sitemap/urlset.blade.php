{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
{!! '<'.'?xml-stylesheet type="text/xsl" href="'.asset('sitemap.xsl').'"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
        @if(!empty($url['lastmod']))
        <lastmod>{{ $url['lastmod'] }}</lastmod>
        @endif
        @if(!empty($url['changefreq']))
        <changefreq>{{ $url['changefreq'] }}</changefreq>
        @endif
        @if(!empty($url['priority']))
        <priority>{{ $url['priority'] }}</priority>
        @endif
        @if(!empty($url['image']))
        <image:image>
            <image:loc>{{ $url['image'] }}</image:loc>
            @if(!empty($url['title']))
            <image:title>{{ $url['title'] }}</image:title>
            @endif
        </image:image>
        @endif
    </url>
@endforeach
</urlset>
