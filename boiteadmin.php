<?php

if (isset($_POST['adresse']) && $_POST['adresse']!=""){
	//echo "Votre nom est ".$_POST['adresse'];
}
if (isset($_POST['desc']) && $_POST['desc']!=""){
	//echo "Votre premier est ".$_POST['desc'];
}

if (isset($_POST['nchambre']) && $_POST['nchambre']!=""){
	//echo "Votre premier est ".$_POST['nchambre'];
}
if (isset($_POST['superficie']) && $_POST['superficie']!=""){
	//echo "Votre premier est ".$_POST['superficie'];
}
$listimage=[];
//var_dump($_FILES);

foreach ($_FILES['photo']['name'] as $index => $fileName) {
    $fileTmpPath = $_FILES['photo']['tmp_name'][$index];
    $fileSize = $_FILES['photo']['size'][$index];
    $fileType = $_FILES['photo']['type'][$index];
    $fileError = $_FILES['photo']['error'][$index];
    $destination = '/xampp/htdocs/img/'. basename($fileName);
	$listimage[]=(string)basename($fileName);
   // print_r($listimage);
   // print_r($fileTmpPath);
    //echo "fffffffffffffffffffffffffff";
    if (move_uploaded_file($fileTmpPath, $destination)) {
       // echo "Fichier téléchargé avec succès : $fileName<br>";
    } else {
        //echo "Erreur lors du déplacement du fichier : $fileName<br>";
    }
}


   
//print_r($listimage);
?>


    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   
<form>
 <input type="hidden" id="adresse" value="<?php echo $_POST['adresse']; ?>">
 <input type="hidden" id="desc" value="<?php echo $_POST['desc']; ?>">
 <input type="hidden" id="nchambre" value="<?php echo $_POST['nchambre']; ?>">
 <input type="hidden" id="superficie" value="<?php echo $_POST['superficie']; ?>">
 <input type="hidden" id="listimage" value="<?php echo  htmlspecialchars(json_encode($listimage)); ?>">
</form>
<div id="pandore">

<br>Adresse:<?php echo $_POST['adresse']; ?></br>
<br>description:<?php echo $_POST['desc']; ?></br>
<br>nchambre:<?php echo $_POST['nchambre']; ?></br>
<br>superficie:<?php echo $_POST['superficie']; ?></br>
image:<?php echo  htmlspecialchars(json_encode($listimage)); ?>
<input id="nombre_de_points" type="text" value="">
</div>

</body>
<style>
    body{
        display:flex;
        align-items:center;
        flex-direction:column;
    }
 
    
    #pandore{
    justify-content:center;
    flex-wrap:wrap;
  display:flex;
  height:250px;
 /* border:black solid 8px; justify-content:flex-start;*/
  width:40%;
  background-color: white;
  border: 1px solid #ddd;
  box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.3); /* Ombre douce */
  margin-top:10px;
  border-radius:4px;
    }  
    button {
    background-color: #ffb300;
    color: black;
    border: none;
    border-radius: 4px;
   
    font-size: 1em;
    cursor: pointer;
    
    text-align: center;
    transition: background-color 0.3s ease;
}
</style>
<script>

   let adresse=document.getElementById('adresse').value
   let desc=document.getElementById('desc').value
   let nchambre=document.getElementById('nchambre').value
   let superficie=document.getElementById('superficie').value
   let listimage=document.getElementById('listimage').value
   
   
function voir(){
    console.log(nombredepoints)
}
  async function fotch(){
    let nombredepoints=document.getElementById('nombre_de_points').value
   await fetch('createfolder.php', {
  method: 'POST', // Méthode POST pour envoyer des données
  headers: {
    'Content-Type': 'application/json' // Indique que le `body` contient du JSON
  },
  body: JSON.stringify({
    adresse: adresse, // Nom de l'utilisateur
    desc: desc,
    nchambre: nchambre,
    superficie: superficie,
    listimage:listimage,
    nombredepoints:nombredepoints,
    indice:'hr'
  })
})
.then(response => response.json())
.then(data => console.log(data))

} 
</script>
<div id="button">
<button onclick=fotch()>valider </button>
<button >refuser </button>
</div>
<button onclick=voir()>voir </button>
</html>