<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Servicios\Mantenimiento;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\EncargadoMantenimiento;

class GestionMantenimiento extends Component
{
    use WithPagination;

    // Propiedades públicas
    public $search = '';
    public $showModal = false;
    public $showEncargadoModal = false;

    // Propiedades para el formulario de Mantenimiento
    public $id_inventario_fk;
    public $id_encargado_man_fk;
    public $fecha;
    public $tipo = 'preventivo'; // Valor por defecto
    public $refacciones_material;
    public $observaciones;

    // Propiedades para el formulario de Encargado
    public $nombre_encargado;
    public $apellidos_encargado;
    public $cargo_encargado;
    public $contacto_encargado;


    /**
     * El método mount() se ejecuta al iniciar el componente.
     * Aquí leemos los parámetros de la URL para precargar datos.
     */
    public function mount()
    {
        // Revisa si los parámetros 'equipo_id' y 'abrir_modal' vienen en la URL
        $equipoId = request('equipo_id');
        $abrirModal = request('abrir_modal');

        // Si ambos existen, llama a la función para abrir el modal con el equipo ya seleccionado.
        if ($equipoId && $abrirModal) {
            $this->crearMantenimiento($equipoId);
        }
    }

    /**
     * Define las reglas de validación para el formulario de Mantenimiento.
     */
    protected function rules()
    {
        return [
            'id_inventario_fk' => 'required|integer|exists:inventarios,id_inventario',
            'id_encargado_man_fk' => 'required|integer|exists:encargados_mantenimiento,id_encargado_man',
            'fecha' => 'required|date',
            'tipo' => 'required|in:preventivo,correctivo',
            'refacciones_material' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ];
    }

    /**
     * Renderiza la vista del componente y le pasa los datos necesarios.
     */
    public function render()
    {
        $reportes = Mantenimiento::with(['inventario.equipo', 'encargadoMantenimiento'])
            ->when($this->search, function ($query) {
                $query->where('tipo', 'like', '%' . $this->search . '%')
                    ->orWhereHas('inventario.equipo', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('encargadoMantenimiento', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->search . '%')
                            ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        // Se obtienen solo equipos que puedan recibir mantenimiento
        $equipos_inventario = Inventario::with('equipo')
            ->whereNotIn('status', ['baja', 'proceso de baja'])
            ->get();

        $encargados = EncargadoMantenimiento::orderBy('nombre')->get();

        return view('livewire.servicios.gestion-mantenimiento', [
            'reportes' => $reportes,
            'equipos_inventario' => $equipos_inventario,
            'encargados' => $encargados,
        ]);
    }

    /**
     * Prepara y abre el modal para registrar un nuevo mantenimiento.
     * @param int|null $equipoId El ID del equipo a preseleccionar (opcional).
     */
    public function crearMantenimiento($equipoId = null)
    {
        $this->resetMantenimientoInput(); // Resetea los campos del formulario
        if ($equipoId) {
            $this->id_inventario_fk = $equipoId; // Asigna el ID del equipo si se proporcionó
        }
        $this->showModal = true; // Abre el modal
    }

    /**
     * Guarda el nuevo reporte de mantenimiento en la base de datos.
     */
    public function saveMantenimiento()
    {
        $this->validate(); // Valida usando el método rules()

        Mantenimiento::create([
            'id_inventario_fk' => $this->id_inventario_fk,
            'id_encargado_man_fk' => $this->id_encargado_man_fk,
            'fecha' => $this->fecha,
            'tipo' => $this->tipo,
            'refacciones_material' => $this->refacciones_material,
            'observaciones' => $this->observaciones,
        ]);

        $this->showModal = false;
        session()->flash('success', 'Reporte de mantenimiento guardado exitosamente.');
    }

    /**
     * Prepara y abre el modal para registrar un nuevo encargado.
     */
    public function crearEncargado()
    {
        $this->resetEncargadoInput();
        $this->showEncargadoModal = true;
    }

    /**
     * Guarda el nuevo encargado en la base de datos.
     */
    public function saveEncargado()
    {
        $this->validate([
            'nombre_encargado' => 'required|string|max:100',
            'apellidos_encargado' => 'required|string|max:100',
            'cargo_encargado' => 'required|string|max:100',
            'contacto_encargado' => 'nullable|string|max:100',
        ]);

        EncargadoMantenimiento::create([
            'nombre' => $this->nombre_encargado,
            'apellidos' => $this->apellidos_encargado,
            'cargo' => $this->cargo_encargado,
            'contacto' => $this->contacto_encargado,
        ]);

        $this->showEncargadoModal = false;
        session()->flash('success', 'Encargado registrado exitosamente.');
    }

    /**
     * Método para emitir el evento de generación de PDF.
     * Se llama desde la vista con wire:click.
     * Los datos del reporte se pasan al evento de JavaScript.
     * @param array $reporteData Los datos del reporte a generar.
     */
    public function generarPDF($reporteData)
    {
        // Livewire 3 utiliza dispatch para emitir eventos
        $this->dispatch('generarPDF', $reporteData);
    }

    /**
     * Resetea los campos del formulario de mantenimiento a sus valores iniciales.
     */
    private function resetMantenimientoInput()
    {
        $this->resetErrorBag();
        $this->id_inventario_fk = null;
        $this->id_encargado_man_fk = null;
        $this->fecha = now()->format('Y-m-d');
        $this->tipo = 'preventivo';
        $this->refacciones_material = '';
        $this->observaciones = '';
    }

    /**
     * Resetea los campos del formulario de encargado de mantenimiento.
     */
    private function resetEncargadoInput()
    {
        $this->resetErrorBag();
        $this->nombre_encargado = '';
        $this->apellidos_encargado = '';
        $this->cargo_encargado = '';
        $this->contacto_encargado = '';
    }

    /**
     * Resetea la paginación al buscar.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }
}
