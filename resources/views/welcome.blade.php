<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualización de Facturas</title>
    <!-- CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <!-- Encabezado -->
        <header class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Facturas</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaFactura">Nueva Factura</button>
        </header>

<!-- Modal VER FACTURA-->
<div class="modal fade" id="modalVerFactura" tabindex="-1" aria-labelledby="modalVerFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerFacturaLabel">Detalles de la Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Información General de la Factura -->
                <p><strong>Número de Factura:</strong> <span id="ver_codigo_factura"></span></p>
                <p><strong>Nombre del Cliente:</strong> <span id="ver_nombre_cliente"></span></p>
                <p><strong>Cédula del Cliente:</strong> <span id="ver_cedula"></span></p>
                <p><strong>Fecha de la Factura:</strong> <span id="ver_fecha_factura"></span></p>
                <p><strong>Total de la Factura:</strong> <span id="ver_precio_total"></span></p>
                <hr
                <!-- Tabla de Productos -->
                <h6>Detalles de los Productos</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre del Producto</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="ver_productos">
                            <!-- Se llenará dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
        
<!-- Formulario EDITAR FACTURA -->
<div class="modal fade" id="modalEditarFactura" tabindex="-1" aria-labelledby="modalEditarFacturaLabel" aria-hidden="true">
    <form id="formEditarFactura">
        @csrf
        <input type="hidden" id="editar_codigo_factura" name="codigo_factura" >
        <input type="hidden" id="id_factura_oculto" name="id_factura" value="12345" readonly> 
        <div class="mb-3">
            <label for="editar_cedula" class="form-label">Cédula del Cliente</label>
            <input type="text" class="form-control" id="editar_cedula" name="cedula" readonly>
        </div>
            <div id="editar_productoContainer">
                <div class="producto mb-3">
                    <label for="editar_producto" class="form-label">Producto</label>
                    <select class="form-select" id="editar_producto" name="editar_producto[]" required>
                        <option value="">Seleccione un producto</option>
                    </select>
                    <hr
                    <label for="editar_cantidad" class="form-label" id="editar_cantidad" name="editar_cantidad" required>Cantidad</label>
                    <input type="number" class="form-control" name="editar_cantidad[]" required
                    <button type="button" class="btn btn-danger btn-sm mt-2 remove-editar_producto" style="display: none;">Eliminar</button>
                </div>
            </div
            <button type="button" class="btn btn-success btn-sm" id="agregar_editarProducto">Agregar Producto</button>
            <br>
            <div class="mb-3">
                <label for="editar_total" class="form-label">Total</label>
                <input type="text" class="form-control" id="editar_total" name="total" disabled>
            </div>
    </form>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarFactura" id="modalEditarFactura">
            Editar Factura
        </button>
    </div>
</div>

<!-- Modal de Creación de Factura -->
<div class="modal fade" id="modalNuevaFactura" tabindex="-1" aria-labelledby="modalNuevaFacturaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevaFacturaLabel">Crear Nueva Factura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Formulario para crear factura -->
                <form id="formNuevaFactura">
                    @csrf
                    <div class="mb-3">
                        <label for="cedula" class="form-label">Cédula del Cliente</label>
                        <input type="text" class="form-control" id="cedula" name="cedula" required>
                    </div>
                    <!-- Contenedor de productos -->
                    <div id="productosContainer">
                        <div class="producto mb-3">
                            <label for="producto" class="form-label">Producto</label>
                            <select class="form-select" name="producto[]" required>
                                <option value="numero_detalle" id="numero_detalle">Seleccione un producto</option>
                            </select>
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" name="cantidad[]" id="unidad_producto" value="unidad_producto" required>
                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-producto" style="display: none;">Eliminar</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" id="agregarProducto">Agregar Producto</button>
                    <br>
                    <div class="mb-3">
                        <label for="numero_factura" class="form-label">Número de Factura</label>
                        <input type="text" class="form-control" id="numero_factura" name="numero_factura" disabled>
                    </div>
                    <!-- Campo Total que se calculará automáticamente -->
                    <div class="mb-3">
                        <label for="total" class="form-label">Total</label>
                        <input type="text" class="form-control" id="total" name="total" disabled>
                        <button type="button" id="calcular-btn" class="btn btn-primary">Calcular Total</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarFactura">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Formulario de búsqueda -->
<div class="input-group mb-3">
    <input type="text" class="form-control" placeholder="Buscar factura o cliente..." id="search">
    <button class="btn btn-outline-secondary" type="button" id="btn-buscar">Buscar</button>
</div>

<!-- Tabla de facturas -->
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Número de Factura</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tabla-facturas"  id="resultados">
            <!-- Las filas se llenarán dinámicamente con los resultados de la búsqueda -->
            <!-- Ejemplo de filas estáticas (esto se eliminará al realizar la búsqueda) -->
            <tr>
                <td colspan="6" class="text-center">Realice una búsqueda para ver los resultados.</td>
            </tr>
        </tbody>
    </table>
</div>

    <script>
    //CARGAR FACTURAS
    function cargarFacturas() {
        fetch('/facturas') 
            .then(response => response.json())
            .then(data => actualizarTabla(data))
            .catch(error => {
                console.error(error);
                document.getElementById('resultados').innerHTML = '<p style="color: red;">Ocurrió un error al cargar las facturas.</p>';
            });
    }
    // ACTUALIZAR TABLA
    function actualizarTabla(data) {
        const tablaFacturas = document.getElementById('tabla-facturas');
        tablaFacturas.innerHTML = ''; // Limpiar tabla

        if (data.length > 0) {
            data.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${item.codigo_factura || 'N/A'}</td>
                    <td>${item.fecha_factura || 'N/A'}</td>
                    <td>${item.cc || 'N/A'}</td>
                    <td>$${item.precio_total || '0.00'}</td>
                    <td>
                        <button class="btn btn-sm btn-info">Ver</button>
                        <button class="btn btn-sm btn-warning btn-editar" data-id="${item.codigo_factura}">Editar</button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${item.codigo_factura}">Eliminar</button>
                    </td>
                `;
                tablaFacturas.appendChild(row);
            });
        } else {
            tablaFacturas.innerHTML = '<tr><td colspan="6" class="text-center">No se encontraron resultados.</td></tr>';
        }
    }
    // Evento para buscar facturas
    document.getElementById('btn-buscar').addEventListener('click', function () {
        const termino = document.getElementById('search').value;

        if (!termino.trim()) {
            document.getElementById('resultados').innerHTML = '<p style="color: red;">Por favor ingrese un término de búsqueda.</p>';
            cargarFacturas(); 
            return;
        }

        fetch('/buscar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ termino: termino })
        })
            .then(response => response.json())
            .then(data => {
                actualizarTabla(data);
                document.getElementById('resultados').innerHTML = `<p style="color: green;">Se encontraron ${data.length} resultados.</p>`;
            })
            .catch(error => {
                console.error(error);
                document.getElementById('resultados').innerHTML = '<p style="color: red;">Ocurrió un error al realizar la búsqueda.</p>';
            });
     });
    // CARGAR FACTURAS
    window.onload = cargarFacturas;
    // VER DETALLE
    document.addEventListener('DOMContentLoaded', function () {     
        document.querySelectorAll('.btn-ver').forEach(button => {
            button.addEventListener('click', function () {
                const facturaId = this.getAttribute('data-id');
            
                fetch(`/factura/${facturaId}/ver`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                    
                        document.getElementById('ver_codigo_factura').innerText = data.codigo_factura;
                        document.getElementById('ver_nombre_cliente').innerText = data.nombre_cliente || 'N/A';
                        document.getElementById('ver_cedula').innerText = data.cc;
                        document.getElementById('ver_fecha_factura').innerText = data.fecha_factura;
                        document.getElementById('ver_precio_total').innerText = `$${data.precio_total}`;
                    
                        const productosContainer = document.getElementById('ver_productos');
                        productosContainer.innerHTML = '';
                    
                        data.productos.forEach((producto, index) => {
                            const subtotal = producto.cantidad * producto.precio_unitario;
                        
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${index + 1}</td>
                                <td>${producto.nombre}</td>
                                <td>${producto.descripcion || 'Sin descripción'}</td>
                                <td>${producto.cantidad}</td>
                                <td>$${producto.precio_unitario.toFixed(2)}</td>
                                <td>$${subtotal.toFixed(2)}</td>
                            `;
                            productosContainer.appendChild(row);
                        });
                    
                        const modalVerFactura = new bootstrap.Modal(document.getElementById('modalVerFactura'));
                        modalVerFactura.show();
                    })
                    .catch(error => {
                        console.error('Error al obtener los detalles de la factura:', error);
                        alert('Ocurrió un error al cargar los detalles de la factura.');
                    });
            });
            });
        });

    // EDITAR FACTURA
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('btn-editar')) {
            const codigo_factura = e.target.getAttribute('data-id');

            fetch(`/factura/${codigo_factura}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('editar_codigo_factura').value = data.codigo_factura;
                        document.getElementById('editar_cedula').value = data.cc;
                        document.getElementById('editar_producto').value = data.producto;
                        document.getElementById('editar_cantidad').value = data.cantidad;
                        document.getElementById('editar_total').value = data.total;

                        const modalEditarFactura = new bootstrap.Modal(document.getElementById('modalEditarFactura'));
                        modalEditarFactura.show();

                    } else {
                        alert('Factura no encontrada.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Ocurrió un error al cargar la factura.');
                });
        }
        });
    // GUARDAR CAMBIO DE FACTURA EDITADA
    document.getElementById('modalEditarFactura').addEventListener('click', function () {
        const formData = new FormData(document.getElementById('modalEditarFactura'));

        fetch(`/factura/${formData.get('codigo_factura')}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify(Object.fromEntries(formData)),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Factura actualizada correctamente.');
                    document.getElementById('modalEditarFactura').modal('hide');
                    cargarFacturas(); // Recargar tabla
                } else {
                    alert('No se pudo actualizar la factura.');
                }
            })
            .catch(error => {
                console.error(error);
                alert('Ocurrió un error al intentar actualizar la factura.');
                });
        });


    //ELIMINAR FACTURA
    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('btn-eliminar')) {
            const codigo_factura = e.target.getAttribute('data-id');
            if (confirm(`¿Estás seguro de que deseas eliminar la factura #${codigo_factura}?`)) {
                fetch(`/eliminar-factura/${codigo_factura}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Factura eliminada correctamente.');
                        cargarFacturas(); // Recargar la tabla
                    } else {
                        alert('No se pudo eliminar la factura. Inténtalo nuevamente.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Ocurrió un error al intentar eliminar la factura.');
                });
            }
        }
        });

    //CREAR NUEVA FACTURA
    document.getElementById('guardarFactura').addEventListener('click', function () {
        var cedula = document.getElementById('cedula').value;

        fetch("{{ route('validarCedula') }}", {
            method: "POST", 
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                cedula: cedula
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                $('#modalNuevaFactura').modal('hide');
            } else {
                alert(data.success);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
        });

    // Obtener el número de factura automáticamente al abrir el modal nueva Factura
    document.addEventListener('DOMContentLoaded', function() {
    
        document.getElementById('modalNuevaFactura').addEventListener('show.bs.modal', function () {
            fetch('/numeroFactura')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('numero_factura').value = data.numero_factura;
                })
                .catch(error => console.error('Error al obtener número de factura:', error));
        });
        });



















   
    //SELECT DESCRIPCION PRODUCTO
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/productos')
            .then(response => response.json())
            .then(data => {
                const selectProducto = document.querySelectorAll('select[name="producto[]"]');
                selectProducto.forEach(select => {
                    data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.id;
                        option.textContent = producto.descripcion_producto;
                        select.appendChild(option);
                    });
                });
            })
            .catch(error => console.error('Error al cargar productos:', error));
        //AGREGAR OTRO PRODUCTO
        document.getElementById('agregarProducto').addEventListener('click', function() {
            var container = document.getElementById('productosContainer');

            var newProducto = document.createElement('div');
            newProducto.classList.add('producto', 'mb-3');

            newProducto.innerHTML = `
                <label for="producto" class="form-label">Producto</label>
                <select class="form-select" name="producto[]" required>
                    <option value="numero_detalle" id="numero_detalle">Seleccione un producto</option>
                </select>

                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="number" class="form-control" name="cantidad[]" required>

                <button type="button" class="btn btn-danger btn-sm mt-2 remove-producto">Eliminar</button>
            `;

            container.appendChild(newProducto);

            fetch('/productos')
                .then(response => response.json())
                .then(data => {
                    const newSelect = newProducto.querySelector('select[name="producto[]"]');
                    data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.id;
                        option.textContent = producto.descripcion_producto;
                        newSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar productos:', error));

            newProducto.querySelector('.remove-producto').style.display = 'inline-block';

            newProducto.querySelector('.remove-producto').addEventListener('click', function() {
                container.removeChild(newProducto);
            });
        });
    });

    //CAULCULAR TOTAL
    document.getElementById('calcular-btn').addEventListener('click', function () {
        const itemId = document.getElementById('numero_factura').value; // ID del producto seleccionado
        const unidadProducto = document.getElementById('unidad_producto').value; // Cantidad seleccionada

        if (!itemId || !unidadProducto || unidadProducto <= 0) {
            alert('Por favor, seleccione un producto y una cantidad válida.');
            return;
        }

        fetch('/calcularTotal', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' 
            },
            body: JSON.stringify({
                item_id: numero_factura,
                detalle_factura_id: 1, 
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.total !== undefined) {
                document.getElementById('total').value = data.total;
            } else {
                alert('Error al calcular el total.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al calcular el total.');
        });
    });





</script>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
