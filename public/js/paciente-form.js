document.addEventListener('DOMContentLoaded', function() {
    // Cargar selects
    loadSelectOptions();
    
    // Cargar municipios cuando cambie departamento
    document.getElementById('departamento_id').addEventListener('change', function() {
        loadMunicipios(this.value);
    });

    // Enviar formulario
    document.getElementById('paciente-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await axios.post( apiURL +'/api/pacientes', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            
            window.location.href = apiURL +'/pacientes?success=1';
            
        } catch (error) {
            if (error.response.status === 422) {
                showErrors(error.response.data.errors);
            } else {
                alert('Error al guardar paciente');
            }
        }
    });
});

async function loadSelectOptions() {
    try {
        const response = await axios.get( apiURL +'/api/form-data');
        const data = response.data;
        
        // Llenar selects
        fillSelect('#tipo_documento_id', data.tipos_documento);
        fillSelect('#genero_id', data.generos);
        fillSelect('#departamento_id', data.departamentos);
        
    } catch (error) {
        console.error('Error cargando datos:', error);
    }
}

async function loadMunicipios(departamentoId) {
    try {
        const response = await axios.get( apiURL +`/api/departamentos/${departamentoId}/municipios`);
        fillSelect('#municipio_id', response.data.data);
        document.getElementById('municipio_id').disabled = false;
    } catch (error) {
        console.error('Error cargando municipios:', error);
    }
}

function fillSelect(selector, items) {
    const select = document.querySelector(selector);
    select.innerHTML = '<option value="">Seleccione...</option>';
    
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item.nombre;
        select.appendChild(option);
    });
}

function showErrors(errors) {
    // Limpiar errores previos
    document.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });
    document.querySelectorAll('.invalid-feedback').forEach(el => {
        el.remove();
    });
    
    // Mostrar nuevos errores
    for (const field in errors) {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[field].join(', ');
            input.parentNode.appendChild(errorDiv);
        }
    }
}