<?php

namespace App\Filament\Pages;

use App\Models\Seo;


use Filament\Pages\Page;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use BackedEnum;
use UnitEnum;

class ManageSeo extends Page
{
    protected static BackedEnum|string|null  $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'SEO & Tracking';

    protected static ?string $title = 'SEO & Script Settings';
    protected string $view = 'filament.pages.manage-seo';
    public ?array $data = [];
    public function mount(): void
    {
        // First or create record
        $seo = Seo::firstOrCreate([]);

        $this->form->fill($seo->toArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('General SEO Meta Tags')
                    ->description('ওয়েবসাইটের ডিফল্ট মেটা টাইটেল ও ডেসক্রিপশন সেট করুন।')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->placeholder('Ex: My E-commerce Store'),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3),

                        TextInput::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->placeholder('comma, separated, keywords'),
                    ])->columns(1),

                // Section::make('Data Layer Settings')
                //     ->schema([
                //         Toggle::make('enable_data_layer')
                //             ->label('Enable Google Tag Manager Data Layer')
                //             ->helperText('ই-কমার্স ইভেন্টগুলো (AddToCart, Purchase) GTM-এ পাঠাতে এটি অন রাখুন।'),
                //     ]),

                Section::make('Tracking Scripts & Pixels')
                    ->description('Facebook Pixel, Google Analytics বা GTM কোডগুলো এখানে পেস্ট করুন।')
                    ->schema([
                        Textarea::make('header_script')
                            ->label('Header Scripts (Placed before </head>)')
                            ->placeholder('<script>/* Facebook Pixel or GTM Head code */</script>')
                            ->rows(5),

                        Textarea::make('body_script')
                            ->label('Body Scripts (Placed after <body>)')
                            ->placeholder('<!-- GTM Noscript code -->')
                            ->rows(4),

                        Textarea::make('footer_script')
                            ->label('Footer Scripts (Placed before </body>)')
                            ->rows(4),
                    ])->columns(1),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $seo = Seo::first();
        if ($seo) {
            $seo->update($data);
        } else {
            Seo::create($data);
        }

        // Cache invalidate kora taake instantly frontend a update hoy
        Cache::forget('global_seo_settings');

        Notification::make()
            ->title('SEO Settings Updated Successfully!')
            ->success()
            ->send();
    }
}
