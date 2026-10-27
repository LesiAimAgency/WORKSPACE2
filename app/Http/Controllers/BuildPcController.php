<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class BuildPcController extends Controller
{
    public function index(Request $request, $projectCode = null)
    {
        return view('pages.build_pc');
    }

    public function getProducts(Request $request, $projectCode = null)
    {
        $cat = strtolower((string) $request->input('cat', ''));

        // Map component category to search keywords
        $keywordMap = [
            'cpu' => ['cpu', 'core i', 'ryzen', 'vi xử lý', 'intel', 'amd'],
            'mainboard' => ['mainboard', 'bo mạch chủ', 'b760', 'b650', 'b660', 'z790', 'h610', 'a520', 'a320'],
            'ram' => ['ram', 'ddr4', 'ddr5', 'fury', 'corsair', 'gskill', 'kingston'],
            'vga' => ['vga', 'card màn hình', 'geforce', 'rtx', 'gtx', 'radeon', 'rx '],
            'storage' => ['ssd', 'nvme', 'samsung 980', 'samsung 990', 'lexar', 'kingston nv', 'sata'],
            'psu' => ['nguồn', 'psu', 'xigmatek thor', 'corsair cv', 'cooler master', '80 plus', 'watt', 'super flower'],
            'case' => ['case', 'vỏ case', 'xigmatek', 'nzxt', 'heaven', 'gaming case'],
        ];

        $keywords = $keywordMap[$cat] ?? [$cat];

        $query = Product::active();

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $q->orWhere('name', 'LIKE', "%{$kw}%")
                    ->orWhere('slug', 'LIKE', "%{$kw}%");
            }
        });

        $products = $query->limit(20)->get()->map(function ($p) {
            $img = $p->featured_image ?: $p->image ?: '';
            if ($img && ! str_starts_with($img, 'http')) {
                $img = asset(ltrim($img, '/'));
            }
            if (! $img) {
                $img = 'https://via.placeholder.com/80?text='.urlencode(substr($p->name, 0, 10));
            }

            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) ($p->sale_price ?: $p->price ?: 0),
                'img' => $img,
            ];
        })->toArray();

        // Fallback demo data if no product matched
        if (empty($products)) {
            $fallback = [
                'cpu' => [
                    ['id' => 101, 'name' => 'CPU Intel Core i5-12400F Tray (New)', 'price' => 2950000, 'img' => 'https://via.placeholder.com/80?text=i5'],
                    ['id' => 102, 'name' => 'CPU Intel Core i7-13700K (Box)', 'price' => 9500000, 'img' => 'https://via.placeholder.com/80?text=i7'],
                    ['id' => 103, 'name' => 'CPU AMD Ryzen 5 7600X (AM5)', 'price' => 5490000, 'img' => 'https://via.placeholder.com/80?text=R5'],
                ],
                'mainboard' => [
                    ['id' => 201, 'name' => 'Mainboard ASUS TUF GAMING B760M-PLUS D4', 'price' => 3890000, 'img' => 'https://via.placeholder.com/80?text=MB'],
                    ['id' => 202, 'name' => 'Mainboard GIGABYTE B760M AORUS ELITE AX', 'price' => 4490000, 'img' => 'https://via.placeholder.com/80?text=MB'],
                ],
                'ram' => [
                    ['id' => 301, 'name' => 'RAM Kingston FURY Beast 16GB (1x16GB) 3200MHz DDR4 RGB', 'price' => 1190000, 'img' => 'https://via.placeholder.com/80?text=RAM'],
                    ['id' => 302, 'name' => 'RAM Corsair Vengeance LPX 32GB (2x16GB) DDR4 3200MHz', 'price' => 1990000, 'img' => 'https://via.placeholder.com/80?text=RAM'],
                ],
                'vga' => [
                    ['id' => 401, 'name' => 'VGA ASUS Dual GeForce RTX 3060 12GB V2', 'price' => 7490000, 'img' => 'https://via.placeholder.com/80?text=VGA'],
                    ['id' => 402, 'name' => 'VGA MSI GeForce RTX 4060 VENTUS 2X 8G OC', 'price' => 8490000, 'img' => 'https://via.placeholder.com/80?text=VGA'],
                ],
                'storage' => [
                    ['id' => 501, 'name' => 'SSD Samsung 990 EVO Plus 1TB PCIe 4.0 x4', 'price' => 2450000, 'img' => 'https://via.placeholder.com/80?text=SSD'],
                    ['id' => 502, 'name' => 'SSD Kingston NV3 500GB PCIe 4.0 NVMe', 'price' => 1150000, 'img' => 'https://via.placeholder.com/80?text=SSD'],
                ],
                'psu' => [
                    ['id' => 601, 'name' => 'Nguồn Xigmatek Thor T650 650W - 80 Plus Bronze', 'price' => 1190000, 'img' => 'https://via.placeholder.com/80?text=PSU'],
                ],
                'case' => [
                    ['id' => 701, 'name' => 'VỎ CASE XIGMATEK HEAVEN | E-ATX, ATX, M-ATX', 'price' => 1980000, 'img' => 'https://via.placeholder.com/80?text=CASE'],
                ],
            ];
            $products = $fallback[$cat] ?? [];
        }

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
