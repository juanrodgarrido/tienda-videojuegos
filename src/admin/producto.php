<?php
require __DIR__ . '/../../app/auth.php';
require __DIR__ . '/../../app/conexion.php';



exigirAdmin();

$id = trim($_GET['id'] ?? "");

$producto = null;
$errores = [];

$bd = obtenerBD();
$coleccion = $bd->productos;

if ($id !== "") {
    try {
        $objectId = new MongoDB\BSON\ObjectId($id);
    } catch (MongoDB\Driver\Exception\InvalidArgumentException) {
        $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El producto no existe'];
        header('Location: /tienda.php');
        exit;
    }

    $producto = $coleccion->findOne([
        "_id" => $objectId
    ]);
    if ($producto === null) {
        $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El producto no existe'];
        header('Location: /tienda.php');
        exit;
    }
}

$titulo = $producto === null ? "Añadir producto" : "Editar producto";
$enviado = $_SERVER['REQUEST_METHOD'] === 'POST';
$nombre = "";
$descripcion = "";
$precio = "";
$stock = "";
$imagen = "";
if ($producto !== null) {
    $nombre = $producto['nombre'];
    $descripcion = $producto['descripcion'];
    $precio = number_format($producto['precio'] / 100, 2, '.', '');
    $stock = $producto['stock'];
    $imagen = $producto['imagen'] ?? "";
}


if ($enviado) {

    $nombre = trim($_POST["nombre"] ?? "");

    if ($nombre === "") {
        $errores["nombre"] = "El nombre es obligatorio";
    } elseif (mb_strlen($nombre) > 100) {
        $errores["nombre"] = "El nombre es demasiado largo";
    }

    $descripcion = trim($_POST["descripcion"] ?? "");

    if ($descripcion === "") {
        $errores["descripcion"] = "La descripción es obligatoria";
    } elseif (mb_strlen($descripcion) > 300) {
        $errores["descripcion"] = "La descripción es demasiado larga";
    }

    $stock = trim($_POST["stock"] ?? "");
    $stockNumero = filter_var($stock, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($stockNumero === false) {
        $errores["stock"] = "El stock debe ser un entero mayor o igual que 0";
    }

    $imagen = trim($_POST["imagen"] ?? "");


    if ($imagen !== "") {
        $imagenLink = filter_var($imagen, FILTER_VALIDATE_URL);
        if ($imagenLink === false) {
            $errores["imagen"] = "La imagen debe cumplir el formato";
        }
    }

    $precio = trim($_POST["precio"] ?? "");

    $precioNumero = filter_var($precio, FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
    if ($precioNumero === false) {
        $errores["precio"] = "El precio debe ser un número de 0 o más";
    } else {
        $precioCentimos = (int) round($precioNumero * 100);
    }

    if (count($errores) === 0) {
        if ($producto === null) {
            $coleccion->insertOne([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precioCentimos,
                'stock' => $stockNumero,
                'imagen' => $imagen === "" ? null : $imagen,
                'activo' => true,
                'destacado' => false
            ]);
            $_SESSION['aviso'] = ['tipo' => 'success', 'texto' => 'El producto se ha guardado correctamente'];
        } else {
            $coleccion->updateOne([
                "_id" => $objectId
            ], [
                '$set' => [
                    'nombre' => $nombre,
                    'descripcion' => $descripcion,
                    'precio' => $precioCentimos,
                    'stock' => $stockNumero,
                    'imagen' => $imagen === "" ? null : $imagen,                    
                ]
            ]);
            $_SESSION['aviso'] = ['tipo' => 'success', 'texto' => 'El producto se ha editado correctamente'];
        }
        header('Location: /tienda.php');
        exit;
    }
}

require __DIR__ . "/../../app/partials/header.php";
?>

<main class="flex-grow-1 py-5">
    <div class="container contenedor-estrecho">
        <h1 class="text-uppercase mb-4"><?= $titulo ?></h1>
        <div class="card bg-black border-top border-3 borde-acento">
            <div class="card-body p-4">
                <form method="POST" novalidate>
                    <div>
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text"
                            class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : ($enviado ? 'is-valid' : '') ?>"
                            id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required maxlength="100">
                        <div class="invalid-feedback">
                            <?= $errores['nombre'] ?? '' ?>
                        </div>
                    </div>
                    <div>
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea
                            class="form-control <?= isset($errores['descripcion']) ? 'is-invalid' : ($enviado ? 'is-valid' : '') ?> "
                            rows="3" id="descripcion" name="descripcion" required
                            maxlength="300"><?= htmlspecialchars($descripcion) ?></textarea>
                        <div class="invalid-feedback">
                            <?= $errores['descripcion'] ?? '' ?>
                        </div>
                    </div>
                    <div>
                        <label for="precio" class="form-label">Precio (€)</label>
                        <input type="number"
                            class="form-control <?= isset($errores['precio']) ? 'is-invalid' : ($enviado ? 'is-valid' : '') ?>"
                            id="precio" name="precio" value="<?= htmlspecialchars($precio) ?>" required min="0"
                            step="0.01">
                        <div class="invalid-feedback">
                            <?= $errores['precio'] ?? '' ?>
                        </div>
                    </div>
                    <div>
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number"
                            class="form-control <?= isset($errores['stock']) ? 'is-invalid' : ($enviado ? 'is-valid' : '') ?>"
                            id="stock" name="stock" required min="0" step="1" value="<?= htmlspecialchars($stock) ?>">
                        <div class="invalid-feedback">
                            <?= $errores['stock'] ?? '' ?>
                        </div>
                    </div>
                    <div>
                        <label for="imagen" class="form-label">Imagen</label>
                        <input type="url"
                            class="form-control <?= isset($errores['imagen']) ? 'is-invalid' : ($enviado ? 'is-valid' : '') ?>"
                            id="imagen" name="imagen" value="<?= htmlspecialchars($imagen) ?>">
                        <div class="invalid-feedback">
                            <?= $errores['imagen'] ?? '' ?>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-2 justify-content-center">
                        <button type="submit" class="btn btn-acento boton-igual">Guardar</button>
                        <a href="/tienda.php" class="btn btn-outline-light boton-igual">Cancelar</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</main>


<?php
require __DIR__ . "/../../app/partials/footer.php";