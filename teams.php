<?php
include('storage.php');
include('auth.php');
include('userstorage.php');
include('teamStorage.php');
include('matchesStorage.php');
include('commentsStorage.php');


function redirect($page) {
  header("Location: ${page}");
  exit();
}

// input
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$teamId = $_GET["id"];

$teamsStorg = new TeamStorage();
$teams = $teamsStorg->findAll();

$commentsStrg = new CommentsStorage();
$comments = $commentsStrg->findMany(function ($comment) use ($teamId){return ($comment['teamid'] === $teamId ||  $comment['teamid'] === $teamId); });

$matchstrg = new MatchesStorage();
$matches =  $matchstrg->findMany(function ($match) use ($teamId){return ($match['home']['id'] === $teamId ||  $match['away']['id'] === $teamId); });


if((count($_GET) > 0) && isset($_GET['id']) && isset($_GET['delete']) )
{
  $commentsStrg->delete($_GET['delete']);
  redirect("teams.php?id=${teamId}");
}



$errors['comment'] = " ";
$data = [];
if (count($_POST) > 0){
  if(isset($_POST['comment']) && $_POST['comment'] != "" && !trim($_POST['comment']) == "")
  {
    $data['author'] = $auth->authenticated_user()['username'];
    $data['text'] = $_POST['comment'];
    $data['teamid'] = $teamId;
    $data['date'] = "".date("Y-m-d")." ". date("h:i:sa");
    $commentsStrg->add($data);
    redirect("teams.php?id=${teamId}");
  }
  else{
    $errors['comment'] = "Comment cannot be empty!";
  }
}


?>


<html>
<title><?=$teamsStorg->findById($teamId)['name']?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-teal.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>

body {font-family: "Roboto", sans-serif}
.w3-bar-block .w3-bar-item {
  padding: 16px;
  font-weight: bold;
}
* {
  box-sizing: border-box;
}

input[type=text], select, textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  resize: vertical;
}

label {
  padding: 12px 12px 12px 0;
  display: inline-block;
}

input[type=submit] {
  background-color: #4CAF50;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  float: right;
}

input[type=submit]:hover {
  background-color: #45a049;
}

.container {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}

.col-25 {
  float: left;
  width: 25%;
  margin-top: 6px;
}

.col-75 {
  float: left;
  width: 75%;
  margin-top: 6px;
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}

/* Responsive layout - when the screen is less than 600px wide, make the two columns stack on top of each other instead of next to each other */
@media screen and (max-width: 600px) {
  .col-25, .col-75, input[type=submit] {
    width: 100%;
    margin-top: 0;
  }
}

</style>
<body>

<nav class="w3-sidebar w3-bar-block w3-collapse w3-animate-left w3-card" style="z-index:3;width:250px;" id="mySidebar">
  <a class="w3-bar-item w3-button w3-border-bottom w3-large" href="https://www.elte.hu"><img src="https://www.elte.hu/media/98/4e/09bd02a0531e0a6378cfaf14e5c72244b304d9bbb78c58659350ad23390d/elte_angol_fekvo_kek_logo.png" style="width:80%;"></a>
  <a class="w3-bar-item w3-button w3-hide-large w3-large" href="javascript:void(0)" onclick="w3_close()">Close <i class="fa fa-remove"></i></a>
  <a class="w3-bar-item w3-button w3-teal" href="index.php">
            <?php if ($auth->is_authenticated()):?>
            Hi <?=$auth->authenticated_user()['username']?>
            <?php if ($auth->authenticated_user()['role'] === "admin"):?>
              (Admin)
            <?php endif?> 
            <?php endif?>
           <?php if (!$auth->is_authenticated()):?>
            Home
            <?php endif?>
            </a>
  <?php
          if (!$auth->is_authenticated()):?>
            <a class="w3-bar-item w3-button w3-teal" href="login.php">Sign in</a>
            <a class="w3-bar-item w3-button w3-teal"href="register.php">Sign up</a>
            <?php endif?>
            <?php
          if ($auth->is_authenticated()):?>
            <a class="w3-bar-item w3-button w3-teal" href="logout.php">Logout</a>
            <?php endif?>
  <div>
    <a class="w3-bar-item w3-button" onclick="myAccordion('demo')" href="javascript:void(0)">Football Teams <i class="fa fa-caret-down"></i></a>
    <div id="demo" class="w3-hide">
    <?php foreach ($teams as $team) : ?>
      <a class="w3-bar-item w3-button" href="teams.php?id=<?=$team['id']?>"><?=$team['name']?></a>
      <?php endforeach?>
    </div>
  </div>
</nav>

<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" id="myOverlay"></div>

<div class="w3-main" style="margin-left:250px;">

<div id="myTop" class="w3-container w3-top w3-theme w3-large">
  <p><i class="fa fa-bars w3-button w3-teal w3-hide-large w3-xlarge" onclick="w3_open()"></i>
  <span id="myIntro" class="w3-hide">ELTE Staduim Homepage</span></p>
</div>

<header class="w3-container w3-theme" style="padding:64px 32px">
  <h1 class="w3-xxxlarge">Welcome to ELTE staduim Website</h1>
</header>

<div class="w3-container" style="padding:32px">

<h1> Welcome to <?= $teamsStorg->findById($teamId)['name'] ?> Team!</h1>

<p>Here you can find information about the team and comment on their page and let them know how you really support them.</p>

<ul class="w3-leftbar w3-theme-border" style="list-style:none">
 <li>This page was created for the php mandotory assignment of web programming crouse.</li>
 <li>This website collects the football teams, matches and the users and fans.</li>
 <li>Admin Can remove comments and edit matches details</li>
 <li>This website is resposive so feel free to access from any device</li>
 <li>No Cookies are used on the website.</li>
</ul>

<br>

<img src="https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/f4dca3b8-d976-466a-b5d4-3de29b63d7d5/da7dfj5-6436164e-4024-4915-82be-1ca215461772.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOiIsImlzcyI6InVybjphcHA6Iiwib2JqIjpbW3sicGF0aCI6IlwvZlwvZjRkY2EzYjgtZDk3Ni00NjZhLWI1ZDQtM2RlMjliNjNkN2Q1XC9kYTdkZmo1LTY0MzYxNjRlLTQwMjQtNDkxNS04MmJlLTFjYTIxNTQ2MTc3Mi5qcGcifV1dLCJhdWQiOlsidXJuOnNlcnZpY2U6ZmlsZS5kb3dubG9hZCJdfQ.-XrdvBkuL32o1h1D0U-0ZLysx8ANgGZhLd4lZy1jrhY" style="width:95%; " alt="Responsive">

<hr>
<h2>Matches</h2>
<div class="w3-container">
  <h2>Matches Table</h2>
  <div class="w3-responsive">
  <table class="w3-table-all w3-hoverable">
    <thead>
      <tr class="w3-light-grey">
      <th>Date</th>
        <th>Home Team</th>
        <th>Guest Team</th>
        <th>Score</th>
       <?php if ($auth->authenticated_user()['role'] === "admin"):?>
        <th>Modify</th>
       <?php endif?>
      </tr>
    </thead>
    <?php foreach ($matches as $match) : ?>
      <tr>
        <td><?=$match['date']?></td>
      <td><?=$teamsStorg->findById($match['home']['id'])['name']?></td>
      <td><?=$teamsStorg->findById($match['away']['id'])['name']?></td>
      <td style="color:<?php
      if(($match['home']['id'] === $teamsStorg->findById($teamId)['id'] && $match['home']['score'] > $match['away']['score']) ||($match['away']['id'] === $teamsStorg->findById($teamId)['id']&& $match['home']['score'] < $match['away']['score']))
      {
        echo "green";
      }
      else if ($match['home']['score'] === $match['away']['score'])
      {
        echo "yellow";
      }
      else{
        echo "red";
      }
      ?>"><?=$match['home']['score']?> -- <?=$match['away']['score']?> </td>
      <?php if ($auth->authenticated_user()['role'] === "admin"):?>
        <td><a href="modification.php?id=<?=$teamId?>&idmod=<?=$match['id']?>">Modify</a></td>
        <?php endif?> 
      </tr>    
      <?php endforeach?>
  </table>
      </div>
</div>
<hr>

<h2>Comments(<?= count($comments)?>) : </h2>
<?php foreach($comments as $comment):?>
<div class="w3-container w3-black w3-leftbar">
<p><strong style="font-size:larger;"><?=$comment['author']?> </strong><span style="font-size:smaller;"><?=$comment['date']?>:</span><br><?=$comment['text']?></p>
<?php if ($auth->authenticated_user()['role'] === "admin"):?>
      <p>  <a href="teams.php?id=<?=$teamsStorg->findById($teamId)['id']?>&delete=<?=$comment['id']?>">Delete comment</a></p>
        <?php endif?> 
</div>
<br><br>
<?php endforeach ?>
<hr>
</div>

<?php if (!$auth->is_authenticated()):?>
            <h5>You Cannot leave comment ! Login first</h5>
            <?php endif?>

<?php if ($auth->is_authenticated()):?>
<form action="teams.php?id=<?=$teamId?>" method="post">
<div class="row">
    <div class="col-25">
      <label for="subject">Leave comment:</label>
    </div>
    <div class="col-75">
      <textarea id="comment" name="comment" placeholder="Write something.." style="height:200px"></textarea>
    </div>
  </div>
   <strong><?=$errors['comment']?></strong>
  <br>
  <div class="row">
    <input type="submit" value="Submit">
  </div>
</form>  
<?php endif?>


<footer class="w3-container w3-theme" style="padding:32px">
  <p>Created by Abdulhakeem Al-Absi</p>
</footer>
     

<script>
// Open and close the sidebar on medium and small screens
function w3_open() {
  document.getElementById("mySidebar").style.display = "block";
  document.getElementById("myOverlay").style.display = "block";
}

function w3_close() {
  document.getElementById("mySidebar").style.display = "none";
  document.getElementById("myOverlay").style.display = "none";
}

// Change style of top container on scroll
window.onscroll = function() {myFunction()};
function myFunction() {
  if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
    document.getElementById("myTop").classList.add("w3-card-4", "w3-animate-opacity");
    document.getElementById("myIntro").classList.add("w3-show-inline-block");
  } else {
    document.getElementById("myIntro").classList.remove("w3-show-inline-block");
    document.getElementById("myTop").classList.remove("w3-card-4", "w3-animate-opacity");
  }
}

// Accordions
function myAccordion(id) {
  var x = document.getElementById(id);
  if (x.className.indexOf("w3-show") == -1) {
    x.className += " w3-show";
    x.previousElementSibling.className += " w3-theme";
  } else { 
    x.className = x.className.replace("w3-show", "");
    x.previousElementSibling.className = 
    x.previousElementSibling.className.replace(" w3-theme", "");
  }
}
</script>
     
</body>
</html> 

