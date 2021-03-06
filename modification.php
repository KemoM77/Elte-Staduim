<?php
include('storage.php');
include('auth.php');
include('userstorage.php');
include('teamStorage.php');
include('matchesStorage.php');


function redirect($page) {
  header("Location: ${page}");
  exit();
}

// input
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$teamId = $_GET["id"];
$matchId = $_GET["idmod"];

$teamsStorg = new TeamStorage();
$teams = $teamsStorg->findAll();


$matchstrg = new MatchesStorage();
$matches =  $matchstrg->findMany(function ($match) use ($teamId){return ($match['home']['id'] === $teamId ||  $match['away']['id'] === $teamId); });


$errors = [];
$errors['date'] = " ";
$data = [];
if(count($_POST)>0)
{
  if(isset($_POST['date'])&&$_POST['date']!="" && trim($_POST['date'])!="")
  {
    $data['id']= $matchId;
    $data['home']['id'] = $matchstrg->findById($matchId)['home']['id'];
    $data['home']['score'] = $_POST['score1'];
    $data['away']['id'] = $matchstrg->findById($matchId)['away']['id'];
    $data['away']['score'] = $_POST['score2'];
    $data['date']= $_POST['date'];
    $matchstrg->update($matchId,$data);
    redirect('teams.php?id='.$teamId);

  }
  else{
    $errors['date'] = "Date should be set";
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
* {box-sizing: border-box;}

input[type=text], select, textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  margin-top: 6px;
  margin-bottom: 16px;
  resize: vertical;
}

input[type=submit] {
  background-color: #04AA6D;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type=submit]:hover {
  background-color: #45a049;
}

.container {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
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

<h1> Admin page : <?= $teamsStorg->findById($teamId)['name'] ?> Team information modify !</h1>

<p>Here only admins can reach here and modify the details of the match related to some team.</p>

<ul class="w3-leftbar w3-theme-border" style="list-style:none">
 <li>This page was created for the php mandotory assignment of web programming crouse.</li>
 <li>This website collects the football teams, matches and the users and fans.</li>
 <li>Admin Can remove comments and edit matches details</li>
 <li>This website is resposive so feel free to access from any device</li>
 <li>No Cookies are used on the website.</li>
</ul>

<h3>Modifying Form </h3>

<div class="container">
  <form action="" method="post">
    <label for="date">Date of match</label>
    <input type="date" id="date" name="date" placeholder="date" value="<?=$matchstrg->findById($matchId)['date']?>"><span style="color:red"><?=$errors['date']?></span>
    
<br><br>
    <label for="score1">Home Team Score</label>
    <input type="number" id="score1" name="score1" placeholder="Home team Score" value="<?=$matchstrg->findById($matchId)['home']['score']?>">

    <label for="score2">Guest Team Score</label>
    <input type="number" id="score2" name="score2" placeholder="Guest team Score"  value="<?=$matchstrg->findById($matchId)['away']['score']?>">

    <input type="submit" value="Submit">
  </form>
</div>



<br>

<img src="https://www.sportfogadasonline.com/blog/images/hungarian-players-after-euro-2020.jpg" style="width:95%; " alt="Responsive">

<hr>

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

