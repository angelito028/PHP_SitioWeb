<?php include "../template/cabecera.php"; ?>
<?php 

$txtId = (isset($_POST['txtId'])) ? $_POST['txtId'] : "";
$txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";

$txtImagen = (isset($_FILES['txtImagen']['name'])) ? $_FILES['txtImagen']['name'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";

include "../config/bd.php";

// echo $txtId."<br>";
// echo $txtNombre."<br>";
// echo $txtImagen."<br>";
// echo $accion."<br>";

switch($accion) {
  case "Agregar":
    // INSERT INTO `libros` (`id`, `nombre`, `imagen`) VALUES (NULL, 'Libro HTML y CSS', 'imagen.jpg');

    $sentenciaSQL = $conn -> prepare("INSERT INTO `libros` (`nombre`, `imagen`) VALUES (:nombre, :imagen);");
    $sentenciaSQL -> bindParam(':nombre', $txtNombre);

    $fecha = new DateTime();
    $nombreArchivo = ($txtImagen != "") ? $fecha -> getTimestamp(). "_" . $_FILES["txtImagen"]["name"] : "imagen.jpg";

    $tmpImagen = $_FILES["txtImagen"]["tmp_name"];

    if($tmpImagen != "") {
      move_uploaded_file($tmpImagen, "../../img/" . $nombreArchivo);
    }

    $sentenciaSQL -> bindParam(':imagen', $txtImagen);
    $sentenciaSQL -> execute();


    break;
  
  case "Modificar":
    // echo "Presionado botón modificar.";

    $sentenciaSQL = $conn -> prepare("UPDATE libros SET nombre=:nombre WHERE id=:id");
    $sentenciaSQL -> bindParam(":nombre", $txtNombre);
    $sentenciaSQL -> bindParam(":id", $txtId);
    $sentenciaSQL -> execute();

    if($txtImagen != "") {
      $sentenciaSQL = $conn -> prepare("UPDATE libros SET imagen=:imagen WHERE id=:id");
      $sentenciaSQL -> bindParam(":imagen", $txtImagen);
      $sentenciaSQL -> bindParam(":id", $txtId);
      $sentenciaSQL -> execute();
    }

    break;
  
  case "Cancelar":
    echo "Presionado botón cancelar.";
    break;
    
  case "Seleccionar":
    // echo "Presionado botón seleccionar.";
    // SELECT * FROM libros WHERE id = 2;
    // :id = Es igual al id que ingresamos

    $sentenciaSQL = $conn -> prepare("SELECT * FROM libros WHERE id=:id");
    $sentenciaSQL -> bindParam(':id', $txtId);
    $sentenciaSQL -> execute();
    $libro = $sentenciaSQL -> fetch(PDO::FETCH_LAZY);

    $txtNombre = $libro['nombre'];
    $txtImagen = $libro['imagen'];

    break;

  case "Borrar":
      // echo "Presionado botón borrar.";
    $sentenciasSQL = $conn -> prepare("SELECT imagen FROM libros WHERE id=:id");
    $sentenciaSQL -> bindParam(":id", $txtId);
    $sentenciaSQL -> execute();
    $libro = $sentenciaSQL -> fetch(PDO::FETCH_LAZY);

    if(isset($libro["imagen"]) && ($libro["imagen"] != "imagen.jpg")) {
      if(file_exists("../../img/" . $libro["imagen"])) {
        unlink("../../img/" . $libro["imagen"]);
      }
    }

    $sentenciaSQL = $conn -> prepare("DELETE FROM libros WHERE id=:id");
    $sentenciaSQL -> bindParam(':id', $txtId);
    $sentenciaSQL -> execute();

      break;
}

$sentenciaSQL = $conn -> prepare("SELECT * FROM libros");
$sentenciaSQL -> execute();
$listaLibros = $sentenciaSQL -> fetchAll(PDO::FETCH_ASSOC);


?>

<div class="col-md-5">

  <div class="card">
    <div class="card-header">
      Datos de los Libros
    </div>
    <div class="card-body">
      
      <form method="post" enctype="multipart/form-data">
        <div class = "form-group">
          <label for="txtId">Id</label>
          <input type="text" class="form-control" id="txtId" value="<?php echo $txtId ?>"  name="txtId" placeholder="Id del Libro" autocomplete="off">
        </div>

        <div class="form-group">
          <label for="txtNombre">Nombre</label>
          <input type="text" class="form-control" id="txtNombre" value="<?php echo $txtNombre ?>"  name="txtNombre" placeholder="Nombre del Libro" autocomplete="off">
        </div>
  
        <div class="form-group">
          <label for="txtImagen">Imagen </label>
          <?php echo $txtImagen ?>
          <input type="file" class="form-control" id="txtImagen" name="txtImagen" placeholder="Imagen">
        </div>

        <div class="btn-group" role="group" aria-label="">
          <button type="submit" name="accion" value="Agregar" class="btn btn-success">Agregar</button>
          <button type="submit" name="accion" value="Modificar" class="btn btn-warning">Modificar</button>
          <button type="submit" name="accion" value="Cancelar" class="btn btn-info">Cancelar</button>
        </div>

      </form>
    </div>
  </div>
</div>



<div class="col-md-7">
  
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Id</th>
        <th>Nombre</th>
        <th>Imagen</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($listaLibros as $libro){ ?>
      <tr>
        <td><?php echo $libro['id']; ?></td>
        <td><?php echo $libro['nombre'];?></td>
        <td><?php echo $libro['imagen'];?></td>
        
        <td>
          <form method="post">
            <input type="hidden" id="txtId" name="txtId" value="<?php echo $libro['id'] ?>">
            <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary">
            <input type="submit" name="accion" value="Borrar" class="btn btn-danger">
          </form>
        
        </td>
      
        </tr>

      <?php } ?>
    </tbody>
  </table>

</div>

<?php include "../template/pie.php"; ?>
