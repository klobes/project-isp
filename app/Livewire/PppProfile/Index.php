<?php

namespace App\Livewire\PppProfile;

use App\Models\PppProfile;
use App\Models\Role;
use App\Models\Router;
use App\Traits\HandlesFlashMessages;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    use HandlesFlashMessages;

    public $search = '';

    public function mount()
    {

    }

    public function deletePackage($pppProfileId)
    {
        if (!auth()->user()->hasPermissionTo('delete', 'Profile')) {
            $this->flashInfo('You are not authorized to delete profile.');
        } else {
            try {
                $pppProfile = PppProfile::findOrFail($pppProfileId);

                $pppProfile->delete();

                $this->flashSuccess('Profile deleted successfully.');
            } catch (ModelNotFoundException $e) {
                $this->flashError('Profile not found.');
            } catch (\Exception $e) {
                $this->flashError('Error deleting profile: ' . $e->getMessage());
            }
        }
    }
    public function render()
    {
        $profiles = PppProfile::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.ppp-profile.index', compact('profiles'));
    }

    public function placeholder()
    {
        return view('components.table-placeholder');
    }
}
