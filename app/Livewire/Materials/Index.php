<?php

namespace App\Livewire\Materials;

use App\Models\Material;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    // Editing / creating state
    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:50')]
    public string $unit = 'unit';

    #[Validate('required|numeric|min:0')]
    public $unit_price = 0;

    #[Validate('nullable|string|max:100')]
    public string $category = '';

    public bool $showForm = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'unit', 'unit_price', 'category']);
        $this->unit = 'unit';
        $this->unit_price = 0;
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $material = Material::findOrFail($id);
        $this->editingId = $material->id;
        $this->name = $material->name;
        $this->unit = $material->unit;
        $this->unit_price = $material->unit_price;
        $this->category = (string) $material->category;
        $this->showForm = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        Material::updateOrCreate(['id' => $this->editingId], $data);

        session()->flash('status', $this->editingId ? 'ماده به‌روزرسانی شد.' : 'ماده افزوده شد.');
        $this->showForm = false;
        $this->reset(['editingId', 'name', 'unit', 'unit_price', 'category']);
    }

    public function delete(int $id): void
    {
        Material::findOrFail($id)->delete();
        session()->flash('status', 'ماده حذف شد.');
    }

    public function render()
    {
        $materials = Material::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('category', 'like', "%{$this->search}%"))
            ->orderBy('category')->orderBy('name')
            ->paginate(20);

        return view('livewire.materials.index', ['materials' => $materials])
            ->title('مواد و لیست قیمت');
    }
}
