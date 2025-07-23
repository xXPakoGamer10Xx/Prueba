<?php

namespace App\Livewire\Odontologia\Consultorio;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Odontologia\Consultorio;
use App\Models\Odontologia\Insumo;     
use App\Models\Odontologia\Laboratorio;
use App\Models\Odontologia\Presentacion;

class InsumosTable extends Component
{
    use WithPagination;

    public $search = ''; // Propiedad para la b\u00fasqueda

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $insumosConsultorio = Consultorio::query()
            ->select(
                'consultorio.id_insumo_consultorio',
                'consultorio.cantidad',
                'insumos.clave',
                'insumos.descripcion',
                'insumos.contenido',
                'insumos.caducidad',
                'laboratorios.descripcion as laboratorio',
                'presentaciones.descripcion as presentacion'
            )
            ->join('insumos', 'consultorio.id_insumo_fk', '=', 'insumos.id_insumo')
            ->join('laboratorios', 'insumos.id_laboratorio', '=', 'laboratorios.id_laboratorio')
            ->join('presentaciones', 'insumos.id_presentacion', '=', 'presentaciones.id_presentacion')
            ->when($this->search, function ($query) {
                $query->where('insumos.clave', 'like', '%' . $this->search . '%')
                      ->orWhere('insumos.descripcion', 'like', '%' . $this->search . '%')
                      ->orWhere('laboratorios.descripcion', 'like', '%' . $this->search . '%')
                      ->orWhere('presentaciones.descripcion', 'like', '%' . $this->search . '%');
            })
            ->paginate(10); // Pagina los resultados, 10 por p\u00e1gina

        return view('livewire.odontologia.consultorio.insumos-table', [
            'insumosConsultorio' => $insumosConsultorio,
        ]);
    }
}