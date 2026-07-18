<?php
namespace App\Http\Livewire\SupplyChain\Settings;

use Livewire\Component;

class MainSettingsPage extends Component
{
    public $activeTab = 'general-settings';

    public function render()
    {
        return view('livewire.supply-chain.settings.main-settings-page');
    }
}
