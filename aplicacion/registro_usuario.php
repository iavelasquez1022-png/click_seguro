<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrarme</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-image: url('img/fondo.avif');
      background-repeat: round;
      background-size: 350px;
      min-height: 100vh;
    }

    .registro {
      background-color: rgba(255, 255, 255, 0.6);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px); 
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      padding: 30px;
    }
    .avatar-option {
  transition: 0.3s;
}

input[type="radio"]:checked + .avatar-option {
  border: 4px solid #0d6efd !important;
  transform: scale(1.05);
  box-shadow: 0 0 15px rgba(13, 110, 253, 0.5);
}
  </style>
</head>
<body class="d-flex align-items-center justify-content-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 registro mt-5">
        <h3 class="mb-4 text-center">Ingresa los siguiente datos</h3>
        <form action="procesar_registro.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario();">
          <div class="row mb-3">
<div class="row mb-4 text-center">

    <label class="form-label mb-3">
        Selecciona tu avatar
    </label>

    <div class="col-md-4">
        <label class="w-100">
            <input type="radio" name="avatar" value="avatar1.jpeg" required hidden>

            <img src="img/avatar1.jpeg"
                 class="img-fluid border rounded p-2 avatar-option"
                 style="height:120px; cursor:pointer;">

            <p class="mt-2">Cyber Fox</p>
        </label>
    </div>

    <div class="col-md-4">
        <label class="w-100">
            <input type="radio" name="avatar" value="avatar2.jpeg" hidden>

            <img src="img/avatar2.jpeg"
                 class="img-fluid border rounded p-2 avatar-option"
                 style="height:120px; cursor:pointer;">

            <p class="mt-2">Shield Bot</p>
        </label>
    </div>

    <div class="col-md-4">
        <label class="w-100">
            <input type="radio" name="avatar" value="avatar3.jpeg" hidden>

            <img src="img/avatar3.jpeg"
                 class="img-fluid border rounded p-2 avatar-option"
                 style="height:120px; cursor:pointer;">

            <p class="mt-2">Safe Ninja</p>
        </label>
    </div>

</div>
          	<div class="col-md-6">
          		<label for= "id_usuario" class= "form-label">Número de identificacion sin puntos ni otros caracteres especiales</label>
          		<input type="text" class="form-control" id="id_usuario" name="id_usuario" pattern="[0-9]+" title="solo numeros" required>
          	</div>	
          </div>
          <div class="mb-3">
            <label for="nombres" class="form-label">Nombres completos</label>
            <input type="text" class="form-control" id="nombres" name="nombres" required>
          </div>
          <div class="mb-3">
            <label for="apellidos" class="form-label">Ambos pellidos</label>
            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico activo y accesible</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono fijo o celular</label>
            <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]+" title="solo numeros" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Crea tu contraseña y presiona enter</label>
            <div class="position-relative">
            <input type="password" class="form-control" id="password" name="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%?&.#])[A-Za-z\d@$!%?&.#]{8,}$" title="Mínimo 8 caracteres con una mayúscula, una minúscula, un número y un símbolo especial" onfocus="mostrarRequisitos()" onblur="ocultarRequisitos()" required>
            <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;" onclick="togglePassword()">
     		<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" viewBox="0 0 16 16" id="eyeIcon">
        	<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
        	<path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
     	 	</svg>
    		</span>
  			</div>
          </div>
          <div id="requisitosPassword" class="alert alert-info mt-2 d-none">La contraseña debe tener:
  			<ul class="mb-0">
    			<li>Mínimo 8 caracteres</li>
    			<li>Al menos una letra mayúscula</li>
    			<li>Al menos una letra minúscula</li>
    			<li>Al menos un número</li>
    			<li>Al menos un carácter especial (!, @, #, $, etc.)</li>
  			</ul>
		  </div>
<div class="mb-3" style="display: none;">
  <select class="form-select" id="id_rol" name="id_rol">
    <option value="3" selected>Usuario</option>
    <option value="2">Tutor</option>
  </select>
</div>
          <div class="mb-3 d-none" id="campoDocumento">
            <label for="documento" class="form-label">Documento que lo acredita como tutor</label>
            <input type="file" class="form-control" id="documento" name="documento" accept=".jpg,.jpeg,.png,.pdf">
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Registrarse</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
  
  	function mostrarRequisitos() {
    document.getElementById('requisitosPassword').classList.remove('d-none');
  	}

  function ocultarRequisitos() {
    document.getElementById('requisitosPassword').classList.add('d-none');
  	}

    function mostrarCampoDocumento() {
      const rol = document.getElementById('id_rol').value;
      const campo = document.getElementById('campoDocumento');
      campo.classList.toggle('d-none', rol !== '2');
    }

    function validarFormulario() {
      const rol = document.getElementById('id_rol').value;
      const archivo = document.getElementById('documento');
      if (rol === '2' && (!archivo.files || archivo.files.length === 0)) {
        alert("Debe subir un documento que lo acredite como tutor.");
        return false;
      }
      return true;
    }
    
	function togglePassword() {
  		const input = document.getElementById("password");
  		input.type = input.type === "password" ? "text" : "password";
	}

  </script>
</body>
</html>