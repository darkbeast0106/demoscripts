<?php session_start(); ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL injection</title>
    <style>
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php if (isset($_POST['action'])){
    $action = $_POST['action'];
    $db = new User();
    $db->migrate_up();
    switch ($action) {
        case 'reg':
            if (
                isset($_POST['felhasznalonev']) && !empty($_POST['felhasznalonev']) &&
                isset($_POST['jelszo']) && !empty($_POST['jelszo']) &&
                isset($_POST['teljesnev']) && !empty($_POST['teljesnev']) 
            ) {
                $felhasznalonev = $_POST['felhasznalonev'];
                $jelszo = $_POST['jelszo'];
                $teljesnev = $_POST['teljesnev'];
                if($db->register($felhasznalonev, $jelszo, $teljesnev)){
                
                    ?> <p>Sikeres regisztráció</p> <?php
                } else {
                    
                ?> <p class="error">HIBA történt, sikertelen regisztráció</p> <?php
                }
            } else {
                ?> <p class="error">Minden mezőt ki kell tölteni</p> <?php
            }
            break;
        
        case 'login':
            if (
                isset($_POST['felhasznalonev']) && !empty($_POST['felhasznalonev']) &&
                isset($_POST['jelszo']) && !empty($_POST['jelszo']) 
            ) {
                $felhasznalonev = $_POST['felhasznalonev'];
                $jelszo = $_POST['jelszo'];
                if (!$db->login($felhasznalonev, $jelszo)) {
                    ?> <p class="error">Hibás felhasználónév vagy jelszó</p> <?php
                }
                
            } else {
                ?> <p class="error">Minden mezőt ki kell tölteni</p> <?php
            }
            break;
        case 'logout':
            unset($_SESSION['felhasznalo']);
            break;
        case 'destroy':
            $db->migrate_down();
            break;
    }

} ?>


<?php if (empty($_SESSION['felhasznalo'])): ?>
    <form method="POST">
        <input type="text" name="felhasznalonev" placeholder="Felhasználónév"> <br>
        <input type="password" name="jelszo" placeholder="Jelszó"> <br>
        <input type="text" name="teljesnev" placeholder="Teljes név"> <br>
        <input type="hidden" name="action" value="reg">
        <button type="submit">Regisztráció</button>
    </form>
    <br>
    <form method="POST">
        <input type="text" name="felhasznalonev" placeholder="Felhasználónév"> <br>
        <input type="password" name="jelszo" placeholder="Jelszó"> <br>
        <input type="hidden" name="action" value="login">
        <button type="submit">Bejelentkezés</button>
    </form>
<?php else: ?>
    <h1>Üdvözöllek <?php echo $_SESSION['felhasznalo']['teljesnev'] ?></h1>
    <form method="POST">
        <input type="hidden" name="action" value="logout">
        <button type="submit">Kijelentkezés</button>
    </form>
<?php endif; ?>
</body>
</html>

<?php 
class Adatbazis {
    protected $conn;
    private $host   = "localhost";
    private $user   = "root";
    private $pass   = "";
    private $dbname = "test";

    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        $this->conn->set_charset("utf8");
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

class User extends Adatbazis {
    public function register($felhasznalonev, $jelszo, $teljesnev)
    {
        $sql = "INSERT INTO felhasznalo (felhasznalonev, jelszo, teljesnev)
            VALUES ('$felhasznalonev', '$jelszo', '$teljesnev');";
        return $this->conn->query($sql);
    }

    public function login($felhasznalonev, $jelszo)
    {
        $sql = "SELECT * FROM felhasznalo WHERE felhasznalonev = '$felhasznalonev' AND jelszo = '$jelszo';";
        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['felhasznalo'] = $row;
            return true;
        }
        return false;
    }

    public function migrate_up()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `felhasznalo` ( `id` INT NOT NULL AUTO_INCREMENT , 
            `felhasznalonev` VARCHAR(100) NOT NULL , 
            `jelszo` VARCHAR(150) NOT NULL ,
            `teljesnev` VARCHAR(100) NOT NULL , PRIMARY KEY (`id`), UNIQUE (`felhasznalonev`)) ENGINE = InnoDB, COLLATE utf8_hungarian_ci;";
        $this->conn->query($sql);
    }

    public function migrate_down()
    {
        $sql = "DROP TABLE IF EXISTS felhasznalo;";
        $this->conn->query($sql);
    }
}

/*
pl. felhasználónév:
' OR 1 = 1 -- 

*/


?>