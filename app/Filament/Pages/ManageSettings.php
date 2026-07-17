<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $title = 'Site Settings';
    protected string $view = 'filament.pages.manage-settings';

    // Holds the live state of your form inputs
    public ?array $data = [];

    public function mount(): void
    {
        // Populate the $data property with existing settings from the DB
        $this->form->fill(SiteSetting::getSettings()->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    Tabs::make('Settings')
                        ->tabs([
                            Tabs\Tab::make('General')
                                ->schema([
                                    TextInput::make('site_name')
                                        ->required(),
                                    FileUpload::make('site_logo')
                                        ->image()
                                        ->directory('site'),
                                    Textarea::make('site_description'),
                                    TextInput::make('top_bar_text')
                                        ->label('Top Bar Text')
                                        ->placeholder('Free shipping on orders over $50...')
                                        ->hint('Max 10 Words recommended.')
                                        ->hintIcon('heroicon-m-information-circle')
                                        ->hintColor('primary'),
                                ]),
                            Tabs\Tab::make('Contact & Socials')
                                ->schema([
                                    TextInput::make('admin_email')
                                        ->email(),
                                    TextInput::make('facebook_url')
                                        ->url(),
                                    TextInput::make('messenger_link')
                                        ->url(),
                                    TextInput::make('whats_up_number')
                                        ->tel(),
                                    TextInput::make('phone_number')
                                        ->tel(),
                                    TextInput::make('twitter_url')
                                        ->url(),
                                ]),
                            Tabs\Tab::make('System')
                                ->schema([
                                    Toggle::make('maintenance_mode')
                                        ->label('Enable Maintenance Mode'),
                                ]),
                        ]),
                ]),
            ])
            ->statePath('data'); // Binds form state directly to $this->data
    }

    public function save(): void
    {
        $validatedData = $this->form->getState();

        $settings = SiteSetting::getSettings();

        if (! $settings->exists) {
            $settings->fill($validatedData);
            $settings->save();
        } else {
            $settings->update($validatedData);
        }

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }

    public function saveAction(): Action
    {
        return Action::make('save')
            ->label('Save Changes')
            ->submit('save');
    }
}
