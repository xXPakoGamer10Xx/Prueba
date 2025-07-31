<?php

// El namespace coincide con tu estructura de carpetas en minúsculas.
namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination; // Importar el trait de paginación para manejar la paginación

// Importar los modelos necesarios para interactuar con la base de datos
use App\Models\Servicios\Area;
use App\Models\Servicios\EncargadoArea;

// El nombre de la clase es 'GestionAreasEncargados'
class GestionAreasEncargados extends Component
{
    use WithPagination; // Habilitar la funcionalidad de paginación de Livewire

    // Define el tema de paginación a usar. 'bootstrap' es común para estilos predefinidos.
    protected $paginationTheme = 'bootstrap';

    // --- PROPIEDADES PARA LA GESTIÓN DE ÁREAS ---
    public $searchArea = ''; // Propiedad para el término de búsqueda de áreas
    public $areaId;         // Para almacenar el ID del área a editar/eliminar
    public $nombreArea;     // Para el campo 'nombre' del formulario de área
    public $id_encargado_area_fk; // Para el campo de clave foránea del encargado en el formulario de área
    public $isAreaModalOpen = false; // Controla la visibilidad del modal de áreas
    public $isAreaEditMode = false;  // Indica si el modal está en modo edición o creación

    // --- PROPIEDADES PARA LA GESTIÓN DE ENCARGADOS ---
    public $searchEncargado = ''; // Propiedad para el término de búsqueda de encargados
    public $encargadoId;          // Para almacenar el ID del encargado a editar/eliminar
    public $nombreEncargado;      // Para el campo 'nombre' del formulario de encargado
    public $apellidosEncargado;   // Para el campo 'apellidos' del formulario de encargado
    public $cargoEncargado;       // Para el campo 'cargo' del formulario de encargado
    public $isEncargadoModalOpen = false; // Controla la visibilidad del modal de encargados
    public $isEncargadoEditMode = false;  // Indica si el modal está en modo edición o creación

    /**
     * Define las reglas de validación para los formularios.
     * Las reglas son condicionales, se aplican según qué modal esté abierto.
     *
     * @return array
     */
    protected function rules()
    {
        if ($this->isAreaModalOpen) {
            return [
                'nombreArea' => 'required|string|max:100',
                // 'exists' asegura que el ID del encargado exista en la tabla 'encargados_area'
                'id_encargado_area_fk' => 'required|exists:encargados_area,id_encargado_area',
            ];
        }
        if ($this->isEncargadoModalOpen) {
            return [
                'nombreEncargado' => 'required|string|max:100',
                'apellidosEncargado' => 'required|string|max:100',
                'cargoEncargado' => 'required|string|max:100',
            ];
        }
        // Si ningún modal está abierto, no hay reglas de validación activas.
        return [];
    }

    /**
     * El método render() es el corazón de un componente Livewire.
     * Se encarga de renderizar la vista asociada al componente y pasarle los datos necesarios.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        // --- Consulta y paginación de ÁREAS ---
        $areas = Area::with('encargado') // Carga eager loading la relación 'encargado' para evitar N+1 queries
            ->where(function ($query) {
                // Filtra por nombre de área
                $query->where('nombre', 'like', '%' . $this->searchArea . '%')
                    // O filtra por nombre o apellido del encargado relacionado
                    ->orWhereHas('encargado', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->searchArea . '%')
                          ->orWhere('apellidos', 'like', '%' . $this->searchArea . '%');
                    });
            })
            ->orderBy('nombre') // Ordena los resultados por el nombre del área
            // Pagina los resultados, usando 'pagina_a' como nombre de la variable de paginación para evitar conflictos
            ->paginate(5, ['*'], 'pagina_a');

        // --- Consulta y paginación de ENCARGADOS ---
        $encargados = EncargadoArea::query()
            ->where(function ($query) {
                // Filtra por nombre, apellidos o cargo del encargado
                $query->where('nombre', 'like', '%' . $this->searchEncargado . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->searchEncargado . '%')
                    ->orWhere('cargo', 'like', '%' . $this->searchEncargado . '%');
            })
            ->orderBy('apellidos') // Ordena los resultados por los apellidos del encargado
            // Pagina los resultados, usando 'pagina_e' como nombre de la variable de paginación
            ->paginate(5, ['*'], 'pagina_e');

        // --- Lista de encargados para el select de áreas (no paginada) ---
        $listaEncargados = EncargadoArea::orderBy('nombre')->get();

        // El nombre de la vista 'gestion-areas-encargados' coincide con el nombre de la clase en kebab-case.
        return view('livewire.servicios.gestion-areas-encargados', [
            'areas' => $areas,
            'encargados' => $encargados,
            'listaEncargados' => $listaEncargados, // Pasa la lista de encargados para el select
        ]);
    }

    // --- MÉTODOS CRUD PARA ÁREAS ---

    /**
     * Abre el modal para crear una nueva área.
     * Resetea los campos y establece el modo de creación.
     */
    public function createArea()
    {
        $this->resetAreaFields(); // Limpia los campos del formulario
        $this->isAreaEditMode = false; // Establece el modo a creación
        $this->isAreaModalOpen = true; // Abre el modal
    }

    /**
     * Abre el modal para editar un área existente.
     * Carga los datos del área y establece el modo de edición.
     *
     * @param int $id El ID del área a editar.
     */
    public function editArea($id)
    {
        $area = Area::findOrFail($id); // Busca el área por ID o lanza una excepción
        $this->areaId = $id;
        $this->nombreArea = $area->nombre;
        $this->id_encargado_area_fk = $area->id_encargado_area_fk;
        $this->isAreaEditMode = true; // Establece el modo a edición
        $this->isAreaModalOpen = true; // Abre el modal
    }

    /**
     * Almacena o actualiza un área en la base de datos.
     * Realiza la validación y luego usa `updateOrCreate`.
     */
    public function storeArea()
    {
        $this->validate(); // Ejecuta las reglas de validación definidas en rules()

        // Crea o actualiza el registro del área.
        // Si $this->areaId tiene un valor, busca y actualiza; si es null, crea uno nuevo.
        Area::updateOrCreate(['id_area' => $this->areaId], [
            'nombre' => $this->nombreArea,
            'id_encargado_area_fk' => $this->id_encargado_area_fk,
        ]);

        // Muestra un mensaje de éxito.
        session()->flash('mensaje', $this->isAreaEditMode ? 'Área actualizada correctamente.' : 'Área creada correctamente.');

        $this->closeAreaModal(); // Cierra el modal después de guardar
    }

    /**
     * Elimina un área de la base de datos.
     *
     * @param int $id El ID del área a eliminar.
     */
    public function deleteArea($id)
    {
        // Aquí podrías añadir una confirmación al usuario antes de eliminar.
        // Por ejemplo, usando un evento de Livewire para mostrar un modal de confirmación.
        Area::find($id)->delete();
        session()->flash('mensaje', 'Área eliminada correctamente.');
    }

    /**
     * Cierra el modal de áreas y resetea sus campos.
     */
    public function closeAreaModal()
    {
        $this->isAreaModalOpen = false;
        $this->resetAreaFields();
    }

    /**
     * Resetea las propiedades relacionadas con el formulario de áreas.
     */
    private function resetAreaFields()
    {
        $this->reset(['areaId', 'nombreArea', 'id_encargado_area_fk']);
    }

    // --- MÉTODOS CRUD PARA ENCARGADOS ---

    /**
     * Abre el modal para crear un nuevo encargado.
     * Resetea los campos y establece el modo de creación.
     */
    public function createEncargado()
    {
        $this->resetEncargadoFields(); // Limpia los campos del formulario
        $this->isEncargadoEditMode = false; // Establece el modo a creación
        $this->isEncargadoModalOpen = true; // Abre el modal
    }

    /**
     * Abre el modal para editar un encargado existente.
     * Carga los datos del encargado y establece el modo de edición.
     *
     * @param int $id El ID del encargado a editar.
     */
    public function editEncargado($id)
    {
        $encargado = EncargadoArea::findOrFail($id); // Busca el encargado por ID o lanza una excepción
        $this->encargadoId = $id;
        $this->nombreEncargado = $encargado->nombre;
        $this->apellidosEncargado = $encargado->apellidos;
        $this->cargoEncargado = $encargado->cargo;
        $this->isEncargadoEditMode = true; // Establece el modo a edición
        $this->isEncargadoModalOpen = true; // Abre el modal
    }

    /**
     * Almacena o actualiza un encargado en la base de datos.
     * Realiza la validación y luego usa `updateOrCreate`.
     */
    public function storeEncargado()
    {
        $this->validate(); // Ejecuta las reglas de validación definidas en rules()

        // Crea o actualiza el registro del encargado.
        EncargadoArea::updateOrCreate(['id_encargado_area' => $this->encargadoId], [
            'nombre' => $this->nombreEncargado,
            'apellidos' => $this->apellidosEncargado,
            'cargo' => $this->cargoEncargado,
        ]);

        // Muestra un mensaje de éxito.
        session()->flash('mensaje', $this->isEncargadoEditMode ? 'Encargado actualizado correctamente.' : 'Encargado creado correctamente.');

        $this->closeEncargadoModal(); // Cierra el modal después de guardar
    }

    /**
     * Elimina un encargado de la base de datos, con una verificación de relaciones.
     *
     * @param int $id El ID del encargado a eliminar.
     */
    public function deleteEncargado($id)
    {
        // Carga el encargado y cuenta cuántas áreas tiene asignadas.
        $encargado = EncargadoArea::withCount('areas')->findOrFail($id);

        // Si el encargado tiene áreas asignadas, no se permite la eliminación.
        if ($encargado->areas_count > 0) {
            session()->flash('error', 'No se puede eliminar. El encargado tiene áreas asignadas. Primero reasigne o elimine esas áreas.');
            return; // Detiene la ejecución del método
        }

        // Si no tiene áreas asignadas, procede con la eliminación.
        $encargado->delete();
        session()->flash('mensaje', 'Encargado eliminado correctamente.');
    }

    /**
     * Cierra el modal de encargados y resetea sus campos.
     */
    public function closeEncargadoModal()
    {
        $this->isEncargadoModalOpen = false;
        $this->resetEncargadoFields();
    }

    /**
     * Resetea las propiedades relacionadas con el formulario de encargados.
     */
    private function resetEncargadoFields()
    {
        $this->reset(['encargadoId', 'nombreEncargado', 'apellidosEncargado', 'cargoEncargado']);
    }

    /**
     * Se ejecuta automáticamente cuando la propiedad $searchArea cambia.
     * Resetea la paginación de 'pagina_a' a la primera página.
     */
    public function updatingSearchArea()
    {
        $this->resetPage('pagina_a');
    }

    /**
     * Se ejecuta automáticamente cuando la propiedad $searchEncargado cambia.
     * Resetea la paginación de 'pagina_e' a la primera página.
     */
    public function updatingSearchEncargado()
    {
        $this->resetPage('pagina_e');
    }
}
