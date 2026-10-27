<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeWidgetCommand extends Command
{
    protected $signature = 'widget:make {name : Tên widget class (vd: SliderWidget)}';

    protected $description = 'Tạo một Widget class mới trong app/Widgets/Types/';

    public function handle(): int
    {
        $name = Str::studly($this->argument('name'));
        $name = Str::endsWith($name, 'Widget') ? $name : $name.'Widget';
        $key = Str::snake(Str::replaceLast('Widget', '', $name));
        $path = app_path("Widgets/Types/{$name}.php");
        $viewPath = resource_path("views/widgets/types/{$key}.blade.php");
        $viewName = "widgets.types.{$key}";

        if (file_exists($path)) {
            $this->error("Widget [{$name}] đã tồn tại!");

            return self::FAILURE;
        }

        // Tạo Widget class
        $stub = $this->getStub($name, $key, $viewName);
        file_put_contents($path, $stub);
        $this->info("✅ Đã tạo: app/Widgets/Types/{$name}.php");

        // Tạo Blade view
        if (! file_exists($viewPath)) {
            @mkdir(dirname($viewPath), 0755, true);
            file_put_contents($viewPath, $this->getViewStub($name, $key));
            $this->info("✅ Đã tạo: resources/views/widgets/types/{$key}.blade.php");
        }

        $this->newLine();
        $this->line('📌 <comment>Bước tiếp theo:</comment> Đăng ký widget trong <info>app/Widgets/WidgetRegistry.php</info>:');
        $this->line("   <info>'{$key}' => \\App\\Widgets\\Types\\{$name}::class,</info>");
        $this->newLine();

        return self::SUCCESS;
    }

    private function getStub(string $name, string $key, string $viewName): string
    {
        $label = Str::headline(Str::replaceLast('Widget', '', $name));

        return <<<PHP
<?php

namespace App\Widgets\Types;

use App\Models\Widget as WidgetModel;
use App\Widgets\BaseWidget;

class {$name} extends BaseWidget
{
    public static string \$label       = '{$label}';
    public static string \$description = 'Mô tả widget {$label}';
    public static string \$icon        = 'fa-solid fa-puzzle-piece';

    public static function fields(): array
    {
        return [
            ['key' => 'title', 'label' => 'Tiêu đề', 'type' => 'text', 'default' => ''],
            // Thêm fields tại đây
            // Types: text | textarea | image | number | select | repeater | html | toggle
        ];
    }

    public static function render(array \$config, WidgetModel \$widget): string
    {
        // Thêm logic lấy dữ liệu tại đây nếu cần
        return static::view('{$viewName}', ['config' => \$config, 'widget' => \$widget]);
    }
}
PHP;
    }

    private function getViewStub(string $name, string $key): string
    {
        $label = Str::headline(Str::replaceLast('Widget', '', $name));

        return <<<BLADE
{{--
    Widget: {$label}
    \$config  - mảng config từ admin
    \$widget  - Widget model instance

    Các hàm helper:
      \$config['title']          - lấy field 'title'
      \$config['slides'] ?? []   - lấy repeater với fallback
--}}
<section class="widget-{$key} py-5">
    <div class="container">
        @if(!empty(\$config['title']))
            <h2 class="section-title text-center mb-4">{{ \$config['title'] }}</h2>
        @endif

        {{-- Viết HTML widget của bạn tại đây --}}

    </div>
</section>
BLADE;
    }
}
