<?php

namespace App\Http\Controllers\Servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servicios\EncargadoMantenimiento; // Se importa el Modelo EncargadoMantenimiento

class EncargadoMantenimientoController extends Controller
{
    /**
     * Almacena un nuevo encargado de mantenimiento en la base de datos.
     * Realiza la validación de los datos de entrada.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP entrante.
     * @return \Illuminate\Http\RedirectResponse Redirecciona a la página de reportes con un mensaje flash.
     */
    public function store(Request $request)
    {
        // Validación de los datos del formulario.
        // Las reglas aseguran que los campos requeridos estén presentes y cumplan con las especificaciones.
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',
            'contacto' => 'nullable|string|max:100', // 'nullable' permite que el campo sea opcional
        ]);

        // Se usa el método `create()` del Modelo Eloquent para insertar un nuevo registro.
        // `$validatedData` contiene solo los campos validados, lo cual es una buena práctica de seguridad.
        EncargadoMantenimiento::create($validatedData);

        // Redirige a la ruta 'servicios.reportes' (asumiendo que es la página principal de mantenimientos
        // o donde se gestionan los reportes y donde se abrió el modal de creación de encargado).
        // Se adjunta un mensaje flash de éxito para informar al usuario.
        return redirect()->route('servicios.reportes')->with('success', 'Encargado registrado exitosamente.');
    }

    /**
     * Muestra una lista de encargados de mantenimiento.
     * Este método es común para una vista de índice o gestión de encargados.
     * Si no tienes una vista específica para solo encargados de mantenimiento,
     * este método podría no ser necesario o podría integrarse en otro controlador.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Puedes añadir lógica de búsqueda y paginación aquí si es necesario.
        $search_query = $request->input('search');

        $encargados = EncargadoMantenimiento::when($search_query, function ($query, $search) {
                $query->where('nombre', 'like', "%{$search}%")
                      ->orWhere('apellidos', 'like', "%{$search}%")
                      ->orWhere('cargo', 'like', "%{$search}%")
                      ->orWhere('contacto', 'like', "%{$search}%");
            })
            ->orderBy('apellidos')
            ->paginate(10); // Pagina los resultados

        return view('servicios.encargados_mantenimiento.index', compact('encargados', 'search_query'));
    }

    /**
     * Muestra el formulario para crear un nuevo encargado de mantenimiento.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('servicios.encargados_mantenimiento.create');
    }

    /**
     * Muestra el formulario para editar un encargado de mantenimiento existente.
     *
     * @param \App\Models\Servicios\EncargadoMantenimiento $encargado
     * @return \Illuminate\View\View
     */
    public function edit(EncargadoMantenimiento $encargado)
    {
        return view('servicios.encargados_mantenimiento.edit', compact('encargado'));
    }

    /**
     * Actualiza un encargado de mantenimiento existente en la base de datos.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Servicios\EncargadoMantenimiento $encargado
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, EncargadoMantenimiento $encargado)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',
            'contacto' => 'nullable|string|max:100',
        ]);

        $encargado->update($validatedData);

        return redirect()->route('servicios.encargados_mantenimiento.index')->with('success', 'Encargado actualizado exitosamente.');
    }

    /**
     * Elimina un encargado de mantenimiento de la base de datos.
     * Se verifica si el encargado tiene mantenimientos asociados antes de eliminar.
     *
     * @param \App\Models\Servicios\EncargadoMantenimiento $encargado
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(EncargadoMantenimiento $encargado)
    {
        if ($encargado->mantenimientos()->count() > 0) {
            return redirect()->route('servicios.encargados_mantenimiento.index')->with('error', 'No se puede eliminar. El encargado tiene mantenimientos asignados.');
        }

        $encargado->delete();

        return redirect()->route('servicios.encargados_mantenimiento.index')->with('success', 'Encargado eliminado exitosamente.');
    }
}
