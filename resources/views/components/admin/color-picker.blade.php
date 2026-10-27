@props(['name', 'value' => '#ffffff', 'label' => ''])

<div class="color-picker-v2 mb-6" 
     x-data="{ 
        color: '{{ $value }}',
        pickr: null,
        init() {
            this.pickr = Pickr.create({
                el: $refs.picker,
                theme: 'classic',
                default: this.color,
                swatches: [
                    'rgba(98, 157, 35, 1)',
                    'rgba(31, 31, 37, 1)',
                    'rgba(255, 255, 255, 1)',
                    'rgba(0, 0, 0, 1)',
                    'rgba(220, 38, 36, 1)',
                    'rgba(62, 183, 94, 1)'
                ],
                components: {
                    preview: true,
                    opacity: true,
                    hue: true,
                    interaction: {
                        hex: true,
                        rgba: true,
                        input: true,
                        clear: true,
                        save: true
                    }
                }
            });

            this.pickr.on('save', (color) => {
                this.color = color.toHEXA().toString();
                this.pickr.hide();
            });
        }
     }">
    @if($label)
        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 block italic">{{ $label }}</label>
    @endif
    
    <div class="flex items-center gap-4 p-4 bg-white rounded-[2rem] border-2 border-slate-50 shadow-sm hover:shadow-md transition-all group">
        {{-- The Picker Trigger --}}
        <div class="relative">
            <div x-ref="picker" class="pickr-trigger"></div>
        </div>

        {{-- Value Display --}}
        <div class="flex-1 flex flex-col">
            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Mã màu (HEX/RGBA)</span>
            <input type="text" 
                   name="{{ $name }}" 
                   x-model="color" 
                   class="bg-transparent border-0 p-0 font-mono text-sm font-black text-slate-800 focus:ring-0 uppercase w-full">
        </div>

    </div>
</div>

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css"/>
        <style>
            .pickr-trigger { width: 44px; height: 44px; border-radius: 12px; }
            .pickr { background: transparent; }
            .pcr-button { 
                width: 44px; 
                height: 44px; 
                border-radius: 14px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }
            .pcr-button:hover { transform: scale(1.1); }
            /* Styling the internal pickr elements to match our premium Light UI */
            .pcr-app { 
                background: #ffffff; 
                border-radius: 24px; 
                padding: 20px;
                box-shadow: 0 30px 70px rgba(0,0,0,0.12);
                border: 1px solid #f1f5f9;
            }
            .pcr-app .pcr-interaction input {
                background: #f8fafc;
                color: #1e293b;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                font-family: inherit;
                font-weight: 700;
                padding: 8px 12px;
            }
            .pcr-app .pcr-interaction .pcr-save {
                background: #629D23;
                border-radius: 12px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                padding: 10px 20px;
                box-shadow: 0 10px 20px rgba(98, 157, 35, 0.2);
            }
        </style>
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
    @endpush
@endonce
