<?php
include('includes/init.php');
const MAX_FILE_SIZE = 1000000;
const BOX_UPLOADS_PATH = "uploads/photos/";
$sql = "SELECT * FROM photos";
$params = array();
$imagenames = exec_sql_query($db, "SELECT DISTINCT photo FROM photos", NULL)->fetchAll(PDO::FETCH_COLUMN);
$tagnames = exec_sql_query($db, "SELECT DISTINCT name FROM tags", NULL)->fetchAll(PDO::FETCH_COLUMN);

function print_images($table) {
  ?>
  <tr>
    <td><?php echo htmlspecialchars($table["photo"]);?></td>
    <td><img src="<?php echo "uploads/photos/". htmlspecialchars($table["photo"]);?>" height = 150px /></td>
  </tr>
  <?php
}

function print_image($table) {
  ?>
  <tr>
    <td><img src="<?php echo "uploads/photos/". htmlspecialchars($table["photo"]);?>" height = 600px /></td>
  </tr>

  <?php
}
?>

<?php
function print_info($table){
  ?>
  <tr>
    <td><?php echo htmlspecialchars("Name: \"".$table["photo"]. "\"");?> </td>
    <td><?php echo htmlspecialchars("Uploader: \"".$table["uploader_name"]. "\"");?> </td>
  </tr>
<?php
}?>

<?php
function print_tag($table){
  ?>
  <tr>
    <td><?php echo htmlspecialchars("Tag: \"".$table["name"]. "\"");?> </td>
  </tr>
<?php
}?>

<?php
function print_tags($table){
  ?>
  <tr>
    <td><?php echo htmlspecialchars("Id: \"".$table["id"]. "\"");?> </td>
    <td><?php echo htmlspecialchars("Name: \"".$table["name"]. "\"");?> </td>
  </tr>
<?php
}

if (isset($_POST["submit_upload"])) {
  $upload_info = $_FILES["box_file"];
  if ($upload_info['error'] == UPLOAD_ERR_OK) {
    $upload_name = basename($upload_info["name"]);
    $upload_ext = strtolower(pathinfo($upload_name, PATHINFO_EXTENSION) );
    $sql = "INSERT INTO photos (photo,  uploader_name) VALUES (:filename,:description)";
    $params = array(
      ':filename' => $upload_name,
      ':description' => $current_user,
    );
    $result = exec_sql_query($db, $sql, $params);
    if ($result) {
      $file_id = $db->lastInsertId("id");
      if (move_uploaded_file($upload_info["tmp_name"], BOX_UPLOADS_PATH . "$file_id.$upload_ext")){
        $sql = "UPDATE photos SET photo=:id_file WHERE(id = :idd)";
        $params = array(
          ':id_file' => "$file_id.$upload_ext",
          'idd' => $file_id
        );
        exec_sql_query($db,$sql,$params);
        array_push($messages, "Your file has been uploaded.");
      }
    } else {
      array_push($messages, "Failed to upload file. TODO");
    }
  } else {
    array_push($messages, "Failed to upload file. TODO");
  }
}

if (isset($_POST['seeAll'])){
  $sql = "SELECT * FROM photos";
}

if (isset($_POST['allTag'])){
  $sql = "SELECT * FROM tags";
}

if (isset($_GET['category'])) {
  $one_image= filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING);
  $sql = "SELECT * FROM photos WHERE id = $one_image";
}

if (isset($_GET['tagcategory'])) {
  $one_tag= filter_input(INPUT_GET, 'tagcategory', FILTER_SANITIZE_STRING);
  $sql = "SELECT * FROM photos LEFT OUTER JOIN taglist ON photos.id = taglist.imageid LEFT OUTER JOIN tags ON taglist.tagid = tags.id WHERE tags.id = $one_tag";
}

if(isset($_GET['tagname'])){
  $tag_name = filter_input(INPUT_GET, 'tagname', FILTER_SANITIZE_STRING);
  $current_image = filter_input(INPUT_GET, 'image_id', FILTER_SANITIZE_STRING);

  if(!in_array($tag_name,$tagnames)){
    $sql = "INSERT INTO tags (name) VALUES (:tagname)";
    $params = array('tagname' => $tag_name);
    exec_sql_query($db,$sql,$params);
    $tag_id = $db->lastInsertId("id");
    $sql = "INSERT INTO taglist (imageid,tagid) VALUES (:imageid, :tagid)";
    $params = array('imageid' => $current_image, 'tagid' => $tag_id);
    exec_sql_query($db,$sql,$params);
    array_push($messages, "New Tag Successfully Added");
  }else{
    $sql = "SELECT id FROM tags WHERE tags.name = \"" . "$tag_name" . "\" limit 1";
    $temp = exec_sql_query($db,$sql,$params)->fetchAll(PDO::FETCH_COLUMN);
    $tag_id= $temp[0];
    $sql = "INSERT INTO taglist (imageid,tagid) VALUES (:imageid, :tagid)";
    $params = array('imageid' => $current_image, 'tagid' => $tag_id);
    exec_sql_query($db,$sql,$params);
    array_push($messages, "Existing Tag Successfully Added");
  }
}

if(isset($_GET['tag_remove'])){
  $current_image = filter_input(INPUT_GET, 'image_id', FILTER_SANITIZE_STRING);
  $tag_name = filter_input(INPUT_GET, 'tag_remove', FILTER_SANITIZE_STRING);

  $tagsql = "SELECT id FROM tags WHERE tags.name = \"" . "$tag_name" . "\" limit 1";
  $tag_temp = exec_sql_query($db,$tagsql,$params)->fetchAll(PDO::FETCH_COLUMN);
  $tag_id = $tag_temp[0];

  $sql = "DELETE FROM taglist WHERE (imageid = :imgid AND tagid = :tgid)";
  $params = array('imgid' => $current_image, 'tgid' => $tag_id);
  exec_sql_query($db,$sql,$params);
}

if(isset($_POST['delete'])){
  $current_image = filter_input(INPUT_POST, 'image_id', FILTER_SANITIZE_STRING);
  $image_info = exec_sql_query($db,"SELECT photo FROM photos WHERE id = $current_image limit 1",$params)->fetchAll(PDO::FETCH_COLUMN);
  $image_name = $image_info[0];
  $sql = "DELETE FROM taglist WHERE imageid = " .  $current_image;
  exec_sql_query($db,$sql,$params);
  $sql = "DELETE FROM photos WHERE id = " . $current_image;
  exec_sql_query($db,$sql,$params);
  unlink(BOX_UPLOADS_PATH . $image_name);
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/indexstyle.css" media="all" />
  <title>Index</title>
</head>

<body>
  <div class="left" style="background-color:white;text-align:left;">
    <center>

    <h3>View Images</h3>

    <form id="images" action="gallery.php" method="post">
      <p></p>
      <button type="submit" name="seeAll">See The Entire Photo Gallery</button>
   </form>
   <p></p>

   <form id="searchform" action="gallery.php" method="get">
     <select name="category">
       <option value="" selected disabled>Choose Image</option>
       <?php
       foreach($imagenames as $image_name => $label){
         ?>
         <option value="<?php echo $image_name;?>"><?php echo $label;?></option>
         <?php
       }?>
     </select>
     <button type="submit">Search</button>
</form>

<h3>View Tags</h3>

<form id="tags" action="gallery.php" method="post">
  <p></p>
  <button type="submit" name="allTag">See Entire Tag Collection</button>
</form>
<p></p>

<p></p>
<form id="tagsearch" action="gallery.php" method="get">
  <select name="tagcategory">
    <option value="" selected disabled>Choose Tag</option>
    <?php
    foreach($tagnames as $tag_name => $label){
      ?>
      <option value="<?php echo $tag_name;?>"><?php echo $label;?></option>
      <?php
    }?>
  </select>
  <button type="submit">Search</button>
</form>

<?php
if (isset($_GET['category'])){
?>
<h3>Tag Image</h3>

<form id="addtag" action="gallery.php" method="get">
      <label>Name:</label>
       <input type="text" name="tagname" required/>
       <!-- learned from Stack Overflow-->
       <input type = "hidden" name = "image_id" value = "<?php echo isset($_GET['category']) ? $_GET['category'] : '' ?> "/>
       <!-- ____________________________ -->
       <p></p>
       <button name="addtag" type="submit">Add tag to this image</button>
</form>

<?php
    $tagsql = "SELECT DISTINCT name FROM tags LEFT OUTER JOIN taglist ON tags.id = taglist.tagid LEFT OUTER JOIN photos ON photos.id = taglist.imageid WHERE photos.id =" . $_GET['category'];
    $imagetags = exec_sql_query($db, $tagsql, $params)->fetchAll(PDO::FETCH_COLUMN);
    $usersql = "SELECT uploader_name FROM photos WHERE id =" .  $_GET['category'] . " limit 1";
    $user= exec_sql_query($db, $usersql, $params)->fetchAll(PDO::FETCH_COLUMN);
    $uploader = $user[0];

if($current_user == $uploader){?>
<h3>Remove Tag</h3>
<form id="tag_remove" action="gallery.php" method="get">
  <input type = "hidden" name = "image_id" value = "<?php echo isset($_GET['category']) ? $_GET['category'] : '' ?> "/>
  <select name="tag_remove">
    <option value="" selected disabled>Remove Tag</option>
    <?php
    foreach($imagetags as $tag_name => $label){
      ?>
      <option value="<?php echo $label;?>"><?php echo $label;?></option>
      <?php
    }?>
  </select>
  <button type="removetag">Remove</button>
</form>

<p></p>
<form id="delete" action="gallery.php" method="post">
  <input type = "hidden" name = "image_id" value = "<?php echo isset($_GET['category']) ? $_GET['category'] : '' ?> "/>
  <p></p>
  <button type="submit" name="delete">Delete This Image</button>
</form>

<?php
} ?>
<?php
}?>

<!-- Start of Login Only Functions-->
<?php if(isset($_COOKIE["session"])){?>
<h3>Upload Image</h3>
<form id="uploadFile" action="gallery.php" method="post" enctype="multipart/form-data">
<ul>
    <p></p>
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
    <input type="file" name="box_file" required>
    <p></p>
    <p align = "left">
    <button name="submit_upload" type="submit">Upload it to Image Database</button>
</ul>
</form>
<?php }?>
<!-- End Login Functions-->

<h3><a href = "index.php">Go back to Login</a></h3>

  <?php
    if(strpos($sql, 'INSERT') == true){
        print_messages();
      }?>
</div>

<div class="right" style="background-color:white;text-align:center;">
    <?php
    if(strpos($sql, 'INSERT') == false){
      print_messages();
    }

    if (isset($_POST['seeAll']) || isset($_GET['tagcategory'])){?>

    <?php if(isset($_POST['seeAll'])){
      $pagetitle = "Photo Gallery";
    }else {
      $pagetitle = "Tagged Images";
    }?>
    <h2><center><?php echo htmlspecialchars($pagetitle); ?></h2>

    <table>
    <tr>
      <th>Photo Name</th>
      <th>Photos</th>
    </tr>
    <?php
    $output = exec_sql_query($db, $sql, $params)->fetchAll();
    foreach($output as $info) {
      print_images($info);
    }
    ?>
    </table>
  <?php } ?>

  <?php
  if (isset($_GET['category'])){?>
  <table>
  <tr>
    <th>Photo</th>
  </tr>
  <?php
  $output = exec_sql_query($db, $sql, $params)->fetchAll();
  foreach($output as $info) {
    print_image($info);
  }
  ?>
</table>

<?php
foreach($output as $info){
if($info["id"] < 18){?>
  <span class="citation"><a href="https://arvalis.deviantart.com/gallery/39915677/Realistic-Pokemon">https://arvalis.deviantart.com/gallery/39915677/Realistic-Pokemon</a></span>
<?php
}
}
?>

<table>
  <tr>
    <th>Photo Name</th>
    <th>Uploader</th>
  </tr>
  <?php
  foreach($output as $info){
    print_info($info);
  }?>
</table>

<table>
  <tr>
    <th>Tagged With</th>
  </tr>
  <?php
  $sql = "SELECT * FROM tags LEFT OUTER JOIN taglist ON tags.id = taglist.tagid LEFT OUTER JOIN photos ON photos.id = taglist.imageid WHERE photos.id = $one_image";
  $output = exec_sql_query($db, $sql, $params)->fetchAll();
  foreach($output as $info){
    print_tag($info);
  }?>
</table>
  <?php } ?>

  <?php
  if ($sql == "SELECT * FROM tags"){?>
  <h2>Tag Collection </h2>
  <table>
  <tr>
    <th>Tag Id</th>
    <th>Tag Name</th>
  </tr>
  <?php
  $output = exec_sql_query($db, $sql, $params)->fetchAll();
  foreach($output as $info) {
    print_tags($info);
  }
  ?>
  </table>
<?php
}
?>
</div>

</body>

</html>
