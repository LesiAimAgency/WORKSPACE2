<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FontController extends Controller
{
    public function index()
    {
        $fonts = setting('fonts', []);

        return view('cms.settings.fonts', compact('fonts'));
    }

    public function getGoogleFonts(Request $request)
    {
        $apiKey = config('services.google.fonts_api_key');

        if (! empty($apiKey)) {
            try {
                $response = Http::timeout(5)->get('https://www.googleapis.com/webfonts/v1/webfonts', [
                    'key' => $apiKey,
                    'sort' => 'popularity',
                ]);

                if ($response->successful()) {
                    $fonts = collect($response->json()['items'] ?? [])->take(150);

                    return response()->json($fonts->values());
                }
            } catch (\Throwable $e) {
                // If remote API fails or times out, continue to fallback
            }
        }

        return response()->json($this->getFallbackFonts());
    }

    protected function getFallbackFonts(): array
    {
        return [
            ['family' => 'Roboto', 'category' => 'sans-serif', 'variants' => ['100', '300', 'regular', '500', '700', '900']],
            ['family' => 'Open Sans', 'category' => 'sans-serif', 'variants' => ['300', 'regular', '500', '600', '700', '800']],
            ['family' => 'Montserrat', 'category' => 'sans-serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Lato', 'category' => 'sans-serif', 'variants' => ['100', '300', 'regular', '700', '900']],
            ['family' => 'Poppins', 'category' => 'sans-serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Inter', 'category' => 'sans-serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Nunito', 'category' => 'sans-serif', 'variants' => ['200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Raleway', 'category' => 'sans-serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Ubuntu', 'category' => 'sans-serif', 'variants' => ['300', 'regular', '500', '700']],
            ['family' => 'Playfair Display', 'category' => 'serif', 'variants' => ['regular', '500', '600', '700', '800', '900']],
            ['family' => 'Merriweather', 'category' => 'serif', 'variants' => ['300', 'regular', '700', '900']],
            ['family' => 'Lora', 'category' => 'serif', 'variants' => ['regular', '500', '600', '700']],
            ['family' => 'PT Sans', 'category' => 'sans-serif', 'variants' => ['regular', '700']],
            ['family' => 'Rubik', 'category' => 'sans-serif', 'variants' => ['300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Noto Sans', 'category' => 'sans-serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Kanit', 'category' => 'sans-serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Work Sans', 'category' => 'sans-serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Fira Sans', 'category' => 'sans-serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Quicksand', 'category' => 'sans-serif', 'variants' => ['300', 'regular', '500', '600', '700']],
            ['family' => 'Barlow', 'category' => 'sans-serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Inconsolata', 'category' => 'monospace', 'variants' => ['200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Bebas Neue', 'category' => 'display', 'variants' => ['regular']],
            ['family' => 'Oswald', 'category' => 'sans-serif', 'variants' => ['200', '300', 'regular', '500', '600', '700']],
            ['family' => 'Source Sans Pro', 'category' => 'sans-serif', 'variants' => ['200', '300', 'regular', '600', '700', '900']],
            ['family' => 'Titillium Web', 'category' => 'sans-serif', 'variants' => ['200', '300', 'regular', '600', '700', '900']],
            ['family' => 'Bitter', 'category' => 'serif', 'variants' => ['100', '200', '300', 'regular', '500', '600', '700', '800', '900']],
            ['family' => 'Cabin', 'category' => 'sans-serif', 'variants' => ['regular', '500', '600', '700']],
            ['family' => 'Dancing Script', 'category' => 'handwriting', 'variants' => ['regular', '500', '600', '700']],
            ['family' => 'Pacifico', 'category' => 'handwriting', 'variants' => ['regular']],
            ['family' => 'Comfortaa', 'category' => 'display', 'variants' => ['300', 'regular', '500', '600', '700']],
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required',
            'type' => 'required',
            'label' => 'required',
            'load' => 'required',
        ]);

        $fonts = setting('fonts', []);

        $fonts[] = [
            'id' => uniqid(),
            'key' => $request->key,
            'type' => $request->type,
            'label' => $request->label,
            'load' => $request->load,
            'is_active' => true,
            'is_default' => false,
        ];

        setting(['fonts' => $fonts]);

        return back()->with('success', 'Đã thêm font');
    }

    public function toggle(Request $request)
    {
        $fonts = setting('fonts', []);
        $fonts = collect($fonts)->map(function ($font) use ($request) {
            if ($font['id'] === $request->id) {
                $font['is_active'] = ! $font['is_active'];
            }

            return $font;
        })->toArray();

        setting(['fonts' => $fonts]);

        return back()->with('success', 'Đã cập nhật');
    }

    public function setDefault(Request $request)
    {
        $fonts = setting('fonts', []);
        $fonts = collect($fonts)->map(function ($font) use ($request) {
            $font['is_default'] = $font['id'] === $request->id;

            return $font;
        })->toArray();

        setting(['fonts' => $fonts]);

        return back()->with('success', 'Đã đặt mặc định. Chạy: npm run build')->with('warning', 'Cần rebuild CSS!');
    }

    public function destroy(Request $request)
    {
        $fonts = setting('fonts', []);
        $fonts = collect($fonts)->reject(fn ($font) => $font['id'] === $request->id)->values()->toArray();

        setting(['fonts' => $fonts]);

        return back()->with('success', 'Đã xóa');
    }
}
